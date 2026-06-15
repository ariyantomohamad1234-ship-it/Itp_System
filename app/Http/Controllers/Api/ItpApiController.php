<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Modul;
use App\Models\Blok;
use App\Models\SubBlok;
use App\Models\Itp;
use App\Models\ItpData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ItpApiController extends Controller
{
    public function projects(Request $request)
    {
        $user = $request->user();
        $projects = DB::table('projects')
            ->join('project_user', 'projects.id', '=', 'project_user.project_id')
            ->where('project_user.user_id', $user->id)
            ->where('projects.status', 'active')
            ->select('projects.*')->get();

        $pIds = $projects->pluck('id')->toArray();
        $totals = collect();
        $dones = collect();

        if (!empty($pIds)) {
            $totals = DB::table('itps')
                ->join('sub_bloks', 'itps.sub_blok_id', '=', 'sub_bloks.id')
                ->join('bloks', 'sub_bloks.blok_id', '=', 'bloks.id')
                ->join('moduls', 'bloks.modul_id', '=', 'moduls.id')
                ->whereIn('moduls.project_id', $pIds)
                ->select('moduls.project_id', DB::raw('COUNT(*) as total'))
                ->groupBy('moduls.project_id')
                ->pluck('total', 'project_id');

            $dones = DB::table('itps')
                ->join('sub_bloks', 'itps.sub_blok_id', '=', 'sub_bloks.id')
                ->join('bloks', 'sub_bloks.blok_id', '=', 'bloks.id')
                ->join('moduls', 'bloks.modul_id', '=', 'moduls.id')
                ->whereIn('moduls.project_id', $pIds)
                ->whereExists(function ($q) {
                    $q->select(DB::raw(1))->from('itp_data')
                      ->whereColumn('itp_data.itp_id', 'itps.id')
                      ->whereIn('itp_data.status', ['done', 'approved']);
                })
                ->select('moduls.project_id', DB::raw('COUNT(*) as done'))
                ->groupBy('moduls.project_id')
                ->pluck('done', 'project_id');
        }

        $result = [];
        foreach ($projects as $p) {
            $t = $totals[$p->id] ?? 0;
            $d = $dones[$p->id] ?? 0;
            $result[] = [
                'id' => $p->id,
                'nama_project' => $p->nama_project,
                'kode_project' => $p->kode_project,
                'deskripsi' => $p->deskripsi,
                'status' => $p->status,
                'tanggal_kontrak' => $p->tanggal_kontrak,
                'tanggal_mulai' => $p->tanggal_mulai,
                'deadline' => $p->deadline,
                'progress' => ['total' => $t, 'done' => $d, 'percent' => $t > 0 ? round(($d / $t) * 100) : 0],
            ];
        }
        return response()->json($result);
    }

    public function moduls(Request $request, $projectId)
    {
        $user = $request->user();
        $project = DB::table('projects')->where('id', $projectId)->first();
        if (!$project) return response()->json(['error' => 'Not found'], 404);

        if (!$user->isAdmin()) {
            $ok = DB::table('project_user')->where('project_id', $projectId)->where('user_id', $user->id)->exists();
            if (!$ok) return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        $moduls = DB::table('moduls')->where('project_id', $projectId)
            ->orderByRaw('COALESCE(start_day, 99999) ASC')->get();
        $mIds = $moduls->pluck('id')->toArray();

        $totals = DB::table('itps')
            ->join('sub_bloks', 'itps.sub_blok_id', '=', 'sub_bloks.id')
            ->join('bloks', 'sub_bloks.blok_id', '=', 'bloks.id')
            ->whereIn('bloks.modul_id', $mIds)
            ->select('bloks.modul_id', DB::raw('COUNT(*) as total'))
            ->groupBy('bloks.modul_id')->pluck('total', 'modul_id');

        $dones = DB::table('itps')
            ->join('sub_bloks', 'itps.sub_blok_id', '=', 'sub_bloks.id')
            ->join('bloks', 'sub_bloks.blok_id', '=', 'bloks.id')
            ->whereIn('bloks.modul_id', $mIds)
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))->from('itp_data')
                  ->whereColumn('itp_data.itp_id', 'itps.id')
                  ->whereIn('itp_data.status', ['done', 'approved']);
            })
            ->select('bloks.modul_id', DB::raw('COUNT(*) as done'))
            ->groupBy('bloks.modul_id')->pluck('done', 'modul_id');

        $pStart = $project->tanggal_mulai ? Carbon::parse($project->tanggal_mulai) : null;
        $dayN = $pStart ? max(1, (int) $pStart->diffInDays(now()) + 1) : null;

        $result = [];
        foreach ($moduls as $m) {
            $t = $totals[$m->id] ?? 0;
            $d = $dones[$m->id] ?? 0;
            $lockState = 'active';
            $lockInfo = [];

            if ($pStart && $m->start_day && $m->duration_days && $dayN) {
                $endDay = $m->start_day + $m->duration_days - 1;
                if ($dayN < $m->start_day) {
                    $lockState = 'locked';
                    $lockInfo = [
                        'unlock_date' => $pStart->copy()->addDays($m->start_day - 1)->format('d M Y'),
                        'days_until_unlock' => $m->start_day - $dayN,
                    ];
                } elseif ($dayN > $endDay) {
                    $lockState = 'completed';
                    $lockInfo = ['days_since_completed' => $dayN - $endDay];
                } else {
                    $elapsed = $dayN - $m->start_day + 1;
                    $lockInfo = [
                        'days_remaining' => $endDay - $dayN + 1,
                        'days_elapsed' => $elapsed,
                        'time_percent' => round(($elapsed / $m->duration_days) * 100),
                    ];
                }
            }

            $result[] = [
                'id' => $m->id, 'nama_modul' => $m->nama_modul,
                'deskripsi' => $m->deskripsi,
                'start_day' => $m->start_day, 'duration_days' => $m->duration_days,
                'progress' => ['total' => $t, 'done' => $d, 'percent' => $t > 0 ? round(($d / $t) * 100) : 0],
                'lock_state' => $lockState, 'lock_info' => $lockInfo,
            ];
        }

        return response()->json(['project' => $project, 'day_n' => $dayN, 'moduls' => $result]);
    }

    public function bloks($modulId)
    {
        $modul = Modul::findOrFail($modulId);
        $project = Project::findOrFail($modul->project_id);
        $bloks = Blok::where('modul_id', $modulId)->get();

        $result = [];
        foreach ($bloks as $b) {
            $t = DB::table('itps')->join('sub_bloks', 'itps.sub_blok_id', '=', 'sub_bloks.id')
                ->where('sub_bloks.blok_id', $b->id)->count();
            $d = DB::table('itps')->join('sub_bloks', 'itps.sub_blok_id', '=', 'sub_bloks.id')
                ->where('sub_bloks.blok_id', $b->id)
                ->whereExists(fn($q) => $q->select(DB::raw(1))->from('itp_data')
                    ->whereColumn('itp_data.itp_id', 'itps.id')->whereIn('itp_data.status', ['done', 'approved']))
                ->count();
            $result[] = ['id' => $b->id, 'nama_blok' => $b->nama_blok,
                'progress' => ['total' => $t, 'done' => $d, 'percent' => $t > 0 ? round(($d / $t) * 100) : 0]];
        }
        return response()->json(['modul' => $modul, 'project' => $project, 'bloks' => $result]);
    }

    public function subbloks($blokId)
    {
        $blok = Blok::with('modul')->findOrFail($blokId);
        $subs = SubBlok::where('blok_id', $blokId)->get();
        $result = [];
        foreach ($subs as $s) {
            $t = DB::table('itps')->where('sub_blok_id', $s->id)->count();
            $d = DB::table('itps')->where('sub_blok_id', $s->id)
                ->whereExists(fn($q) => $q->select(DB::raw(1))->from('itp_data')
                    ->whereColumn('itp_data.itp_id', 'itps.id')->whereIn('itp_data.status', ['done', 'approved']))
                ->count();
            $result[] = ['id' => $s->id, 'nama_sub_blok' => $s->nama_sub_blok,
                'progress' => ['total' => $t, 'done' => $d, 'percent' => $t > 0 ? round(($d / $t) * 100) : 0]];
        }
        return response()->json(['blok' => $blok, 'subbloks' => $result]);
    }

    public function assembly(Request $request, $subblokId)
    {
        $subblok = SubBlok::findOrFail($subblokId);
        $user = $request->user();
        $role = $user->role;

        $itps = Itp::where('sub_blok_id', $subblokId)->with(['itpData.uploader'])
            ->orderBy('assembly_code')->orderBy('code')->get();

        $grouped = $itps->groupBy('assembly_code');
        $result = [];

        foreach ($grouped as $code => $items) {
            $inspections = [];
            foreach ($items as $itp) {
                $val = $itp->getValForRole($role);
                $canSubmit = in_array(strtoupper($val), ['W', 'RV']);
                $myData = $itp->itpData->where('uploaded_by', $user->id)->first();

                $inspections[] = [
                    'id' => $itp->id, 'code' => $itp->code, 'item' => $itp->item,
                    'assembly_description' => $itp->assembly_description,
                    'yard_val' => $itp->yard_val, 'class_val' => $itp->class_val,
                    'os_val' => $itp->os_val, 'stat_val' => $itp->stat_val,
                    'my_val' => $val, 'can_submit' => $canSubmit,
                    'photo_required' => $itp->isPhotoRequired($role),
                    'my_data' => $myData ? [
                        'id' => $myData->id, 'photo' => $myData->photo,
                        'keterangan' => $myData->keterangan, 'status' => $myData->status,
                        'rejection_note' => $myData->rejection_note,
                    ] : null,
                    'all_data_count' => $itp->itpData->count(),
                ];
            }
            $result[] = [
                'assembly_code' => $code,
                'assembly_description' => $items->first()->assembly_description,
                'inspections' => $inspections,
            ];
        }
        return response()->json(['subblok' => $subblok, 'blok' => $subblok->blok, 'role' => $role, 'assemblies' => $result]);
    }

    public function showItpData(Request $request, $itpId)
    {
        $itp = Itp::with('itpData.uploader')->findOrFail($itpId);
        $user = $request->user();
        $role = $user->role;
        $val = $itp->getValForRole($role);
        $canSubmit = in_array(strtoupper($val), ['W', 'RV']);
        $myData = $itp->itpData->where('uploaded_by', $user->id)->first();

        $hierarchy = ['os' => 'yard', 'class' => 'os', 'stat' => 'class'];
        $canAccRole = $hierarchy[$role] ?? null;

        $allData = $itp->itpData->map(function ($d) use ($canAccRole) {
            $uRole = $d->uploader->role ?? '-';
            return [
                'id' => $d->id, 'photo' => $d->photo, 'keterangan' => $d->keterangan,
                'status' => $d->status, 'role' => $uRole, 'name' => $d->uploader->name ?? '-',
                'approved_at' => $d->approved_at, 'rejection_note' => $d->rejection_note,
                'updated_at' => $d->updated_at,
                'can_acc' => $canAccRole === $uRole && $d->status === 'done',
                'can_reject' => $canAccRole === $uRole && $d->status === 'done',
            ];
        })->values();

        return response()->json([
            'itp' => $itp, 'my_data' => $myData, 'all_data' => $allData,
            'role' => $role, 'can_submit' => $canSubmit,
            'photo_required' => $canSubmit ? $itp->isPhotoRequired($role) : false,
            'val' => $val, 'can_acc_role' => $canAccRole,
            'all_vals' => ['yard' => $itp->yard_val, 'class' => $itp->class_val, 'os' => $itp->os_val, 'stat' => $itp->stat_val],
        ]);
    }

    public function storeItpData(Request $request)
    {
        $user = $request->user();
        $itp = Itp::findOrFail($request->itp_id);
        $val = $itp->getValForRole($user->role);

        if (!in_array(strtoupper($val), ['W', 'RV'])) {
            return response()->json(['success' => false, 'message' => 'Role Anda tidak bisa submit.'], 403);
        }

        if ($itp->isPhotoRequired($user->role) && !$request->hasFile('photo')) {
            $existing = ItpData::where('itp_id', $request->itp_id)->where('uploaded_by', $user->id)->first();
            if (!$existing || !$existing->photo) {
                return response()->json(['success' => false, 'message' => 'Foto wajib untuk kode W.'], 422);
            }
        }

        $photo = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo')->store('itp_photos', 'public');
        }

        $existing = ItpData::where('itp_id', $request->itp_id)->where('uploaded_by', $user->id)->first();
        if ($existing) {
            $upd = ['keterangan' => $request->keterangan, 'status' => 'done', 'rejection_note' => null];
            if ($photo) $upd['photo'] = $photo;
            $existing->update($upd);
            $msg = 'Data ITP berhasil diperbarui!';
        } else {
            ItpData::create([
                'itp_id' => $request->itp_id, 'uploaded_by' => $user->id,
                'photo' => $photo, 'keterangan' => $request->keterangan, 'status' => 'done',
            ]);
            $msg = 'Data ITP berhasil disimpan!';
        }
        return response()->json(['success' => true, 'message' => $msg]);
    }

    public function approveItpData(Request $request, $id)
    {
        $data = ItpData::with('uploader')->findOrFail($id);
        $hierarchy = ['os' => 'yard', 'class' => 'os', 'stat' => 'class'];
        $myRole = $request->user()->role;

        if (!isset($hierarchy[$myRole])) {
            return response()->json(['success' => false, 'message' => 'Role Anda tidak bisa ACC.'], 403);
        }
        if (($data->uploader->role ?? null) !== $hierarchy[$myRole]) {
            return response()->json(['success' => false, 'message' => "Hanya bisa ACC data role {$hierarchy[$myRole]}."], 403);
        }
        $data->update(['status' => 'approved', 'approved_at' => now()]);
        return response()->json(['success' => true, 'message' => 'Data ITP di-ACC!']);
    }

    public function rejectItpData(Request $request, $id)
    {
        $request->validate(['note' => 'required|string|min:3']);
        $data = ItpData::with('uploader')->findOrFail($id);
        $hierarchy = ['os' => 'yard', 'class' => 'os', 'stat' => 'class'];
        $myRole = $request->user()->role;

        if (!isset($hierarchy[$myRole])) {
            return response()->json(['success' => false, 'message' => 'Role Anda tidak bisa reject.'], 403);
        }
        if (($data->uploader->role ?? null) !== $hierarchy[$myRole]) {
            return response()->json(['success' => false, 'message' => "Hanya bisa reject data role {$hierarchy[$myRole]}."], 403);
        }
        $data->update(['status' => 'needs_revision', 'rejection_note' => $request->note, 'approved_at' => null]);
        return response()->json(['success' => true, 'message' => 'Data ITP ditolak.']);
    }
}
