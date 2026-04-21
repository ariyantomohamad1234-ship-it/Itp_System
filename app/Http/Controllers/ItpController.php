<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Project;
use App\Models\Modul;
use App\Models\Blok;
use App\Models\SubBlok;
use App\Models\Itp;
use App\Models\ItpData;
use App\Models\User;

class ItpController extends Controller
{
    /**
     * Dashboard: Netflix-style project picker
     */
    public function dashboard()
    {
        $user = session('user');
        if ($user->role === 'admin') {
            return redirect('/admin/dashboard');
        }

        $projects = DB::table('projects')
            ->join('project_user', 'projects.id', '=', 'project_user.project_id')
            ->where('project_user.user_id', $user->id)
            ->where('projects.status', 'active')
            ->select('projects.*')
            ->get();

        // Calculate progress per project
        $projectProgress = [];
        foreach ($projects as $p) {
            $total = DB::table('itps')
                ->join('sub_bloks', 'itps.sub_blok_id', '=', 'sub_bloks.id')
                ->join('bloks', 'sub_bloks.blok_id', '=', 'bloks.id')
                ->join('moduls', 'bloks.modul_id', '=', 'moduls.id')
                ->where('moduls.project_id', $p->id)
                ->count();

            $done = DB::table('itps')
                ->join('sub_bloks', 'itps.sub_blok_id', '=', 'sub_bloks.id')
                ->join('bloks', 'sub_bloks.blok_id', '=', 'bloks.id')
                ->join('moduls', 'bloks.modul_id', '=', 'moduls.id')
                ->where('moduls.project_id', $p->id)
                ->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('itp_data')
                        ->whereColumn('itp_data.itp_id', 'itps.id')
                        ->whereIn('itp_data.status', ['done', 'approved']);
                })
                ->count();

            $projectProgress[$p->id] = [
                'total' => $total,
                'done' => $done,
                'percent' => $total > 0 ? round(($done / $total) * 100) : 0,
            ];
        }

        return view('dashboard', compact('projects', 'projectProgress'));
    }

    /**
     * Daftar modul + progress per modul
     */
    public function modul($projectId)
    {
        $user = session('user');
        $project = DB::table('projects')->where('id', $projectId)->first();
        if (!$project) abort(404);

        if ($user->role !== 'admin') {
            $isAssigned = DB::table('project_user')
                ->where('project_id', $projectId)
                ->where('user_id', $user->id)
                ->exists();
            if (!$isAssigned) abort(403);
        }

        $modul = DB::table('moduls')->where('project_id', $projectId)->get();

        // Progress per modul
        $modulProgress = [];
        foreach ($modul as $m) {
            $total = DB::table('itps')
                ->join('sub_bloks', 'itps.sub_blok_id', '=', 'sub_bloks.id')
                ->join('bloks', 'sub_bloks.blok_id', '=', 'bloks.id')
                ->where('bloks.modul_id', $m->id)
                ->count();

            $done = DB::table('itps')
                ->join('sub_bloks', 'itps.sub_blok_id', '=', 'sub_bloks.id')
                ->join('bloks', 'sub_bloks.blok_id', '=', 'bloks.id')
                ->where('bloks.modul_id', $m->id)
                ->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('itp_data')
                        ->whereColumn('itp_data.itp_id', 'itps.id')
                        ->whereIn('itp_data.status', ['done', 'approved']);
                })
                ->count();

            $modulProgress[$m->id] = [
                'total' => $total,
                'done' => $done,
                'percent' => $total > 0 ? round(($done / $total) * 100) : 0,
            ];
        }

        return view('modul', compact('modul', 'project', 'modulProgress'));
    }

    /**
     * Daftar blok + progress per blok
     */
    public function blok($modulId)
    {
        $modul = DB::table('moduls')->where('id', $modulId)->first();
        if (!$modul) abort(404);
        $project = DB::table('projects')->where('id', $modul->project_id)->first();

        $bloks = DB::table('bloks')->where('modul_id', $modulId)->get();

        $blokProgress = [];
        foreach ($bloks as $b) {
            $total = DB::table('itps')
                ->join('sub_bloks', 'itps.sub_blok_id', '=', 'sub_bloks.id')
                ->where('sub_bloks.blok_id', $b->id)
                ->count();

            $done = DB::table('itps')
                ->join('sub_bloks', 'itps.sub_blok_id', '=', 'sub_bloks.id')
                ->where('sub_bloks.blok_id', $b->id)
                ->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('itp_data')
                        ->whereColumn('itp_data.itp_id', 'itps.id')
                        ->whereIn('itp_data.status', ['done', 'approved']);
                })
                ->count();

            $blokProgress[$b->id] = [
                'total' => $total,
                'done' => $done,
                'percent' => $total > 0 ? round(($done / $total) * 100) : 0,
            ];
        }

        return view('blok', compact('bloks', 'modul', 'project', 'blokProgress'));
    }

    public function subblok($blokId)
    {
        $blok = DB::table('bloks')->where('id', $blokId)->first();
        if (!$blok) abort(404);

        $subbloks = DB::table('sub_bloks')->where('blok_id', $blokId)->get();

        $progress = [];
        foreach ($subbloks as $s) {
            $total = DB::table('itps')->where('sub_blok_id', $s->id)->count();
            $done = DB::table('itps')
                ->where('sub_blok_id', $s->id)
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('itp_data')
                        ->whereColumn('itp_data.itp_id', 'itps.id')
                        ->whereIn('itp_data.status', ['done', 'approved']);
                })
                ->count();

            $approved = DB::table('itps')
                ->where('sub_blok_id', $s->id)
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('itp_data')
                        ->whereColumn('itp_data.itp_id', 'itps.id')
                        ->where('itp_data.status', 'approved');
                })
                ->count();

            $percent = $total > 0 ? round(($done / $total) * 100) : 0;
            $progress[$s->id] = compact('total', 'done', 'approved', 'percent');
        }

        return view('subblok', compact('subbloks', 'blok', 'progress'));
    }

    public function assembly($subblokId)
    {
        $subblok = DB::table('sub_bloks')->where('id', $subblokId)->first();
        if (!$subblok) abort(404);

        $blok = DB::table('bloks')->where('id', $subblok->blok_id)->first();
        $role = session('user')->role;
        $userId = session('user')->id;

        $itps = Itp::where('sub_blok_id', $subblokId)
            ->with(['itpData.uploader'])
            ->orderBy('assembly_code')
            ->orderBy('code')
            ->get();

        $grouped = $itps->groupBy('assembly_code');

        return view('assembly', compact('grouped', 'subblok', 'blok', 'role', 'userId'));
    }

    /**
     * Show ITP data (AJAX)
     */
    public function showItpData($itpId)
    {
        $itp = Itp::with('itpData.uploader')->findOrFail($itpId);
        $user = session('user');
        $role = $user->role;
        $val = $itp->getValForRole($role);
        $canSubmit = in_array(strtoupper($val), ['W', 'RV']);

        $myData = $itp->itpData->where('uploaded_by', $user->id)->first();

        $allData = $itp->itpData->map(function ($d) {
            return [
                'id' => $d->id,
                'photo' => $d->photo,
                'keterangan' => $d->keterangan,
                'status' => $d->status,
                'role' => $d->uploader->role ?? '-',
                'name' => $d->uploader->name ?? '-',
                'approved_at' => $d->approved_at,
                'rejection_note' => $d->rejection_note,
                'updated_at' => $d->updated_at,
            ];
        })->values();

        return response()->json([
            'itp' => $itp,
            'my_data' => $myData,
            'all_data' => $allData,
            'role' => $role,
            'can_submit' => $canSubmit,
            'photo_required' => $canSubmit ? $itp->isPhotoRequired($role) : false,
            'val' => $val,
            'all_vals' => [
                'yard' => $itp->yard_val,
                'class' => $itp->class_val,
                'os' => $itp->os_val,
                'stat' => $itp->stat_val,
            ],
        ]);
    }

    /**
     * Store ITP data
     */
    public function storeItpData(Request $request)
    {
        $user = session('user');
        $itp = Itp::findOrFail($request->itp_id);
        $val = $itp->getValForRole($user->role);

        if (!in_array(strtoupper($val), ['W', 'RV'])) {
            return response()->json(['success' => false, 'message' => 'Role Anda tidak bisa submit untuk kode ini.'], 403);
        }

        if ($itp->isPhotoRequired($user->role) && !$request->hasFile('photo')) {
            $existing = ItpData::where('itp_id', $request->itp_id)->where('uploaded_by', $user->id)->first();
            if (!$existing || !$existing->photo) {
                return response()->json(['success' => false, 'message' => 'Foto wajib diupload untuk kode W (Witness).'], 422);
            }
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('itp_photos', 'public');
        }

        $existing = ItpData::where('itp_id', $request->itp_id)->where('uploaded_by', $user->id)->first();

        if ($existing) {
            $updateData = ['keterangan' => $request->keterangan, 'status' => 'done'];
            if ($photoPath) { $updateData['photo'] = $photoPath; }
            $existing->update($updateData);
        } else {
            ItpData::create([
                'itp_id' => $request->itp_id,
                'uploaded_by' => $user->id,
                'photo' => $photoPath,
                'keterangan' => $request->keterangan,
                'status' => 'done',
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Data ITP berhasil disimpan!']);
    }

    /**
     * ACC (approve) ITP data
     */
    public function approveItpData($id)
    {
        $data = ItpData::findOrFail($id);
        $user = session('user');

        if ($data->uploaded_by !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Anda hanya bisa ACC data milik Anda sendiri.'], 403);
        }

        $data->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Data ITP berhasil di-ACC!']);
    }

    /**
     * Reject ITP data (revert to done for re-review)
     */
    public function rejectItpData(Request $request, $id)
    {
        $data = ItpData::findOrFail($id);

        $data->update([
            'status' => 'rejected',
            'rejection_note' => $request->note,
        ]);

        return response()->json(['success' => true, 'message' => 'Data ITP ditolak.']);
    }
}
