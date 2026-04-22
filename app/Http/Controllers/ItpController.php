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
use App\Services\NotificationService;

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

        // Calculate module lock status and "Hari ke-N"
        $projectStart = $project->tanggal_mulai ? \Carbon\Carbon::parse($project->tanggal_mulai) : null;
        $dayN = $projectStart ? (int) $projectStart->diffInDays(now()) + 1 : null;

        $modulLock = [];
        foreach ($modul as $m) {
            $startDay = $m->start_day ?? null;
            $durationDays = $m->duration_days ?? null;

            if ($projectStart && $startDay) {
                $unlockDate = $projectStart->copy()->addDays($startDay - 1);
                $isLocked = now()->lt($unlockDate);
                $modulLock[$m->id] = [
                    'locked' => $isLocked,
                    'unlock_date' => $unlockDate->format('d M Y'),
                    'start_day' => $startDay,
                    'duration_days' => $durationDays,
                ];
            } else {
                $modulLock[$m->id] = [
                    'locked' => false,
                    'unlock_date' => null,
                    'start_day' => $startDay,
                    'duration_days' => $durationDays,
                ];
            }
        }

        return view('modul', compact('modul', 'project', 'modulProgress', 'modulLock', 'dayN'));
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

        // Determine which role this user can ACC/reject (one level below)
        $canAccRole = self::ROLE_HIERARCHY[$role] ?? null;

        $allData = $itp->itpData->map(function ($d) use ($canAccRole, $role) {
            $uploaderRole = $d->uploader->role ?? '-';
            return [
                'id' => $d->id,
                'photo' => $d->photo,
                'keterangan' => $d->keterangan,
                'status' => $d->status,
                'role' => $uploaderRole,
                'name' => $d->uploader->name ?? '-',
                'approved_at' => $d->approved_at,
                'rejection_note' => $d->rejection_note,
                'updated_at' => $d->updated_at,
                'can_acc' => $canAccRole === $uploaderRole && $d->status === 'done',
                'can_reject' => $canAccRole === $uploaderRole && $d->status === 'done',
            ];
        })->values();

        // Visibility: which roles' data can this user see
        $visibleRoles = match ($role) {
            'os'    => ['yard', 'os'],
            'class' => ['yard', 'os', 'class'],
            'stat'  => ['yard', 'os', 'class', 'stat'],
            'yard'  => ['yard'],
            default => ['yard', 'os', 'class', 'stat'],
        };

        return response()->json([
            'itp' => $itp,
            'my_data' => $myData,
            'all_data' => $allData,
            'role' => $role,
            'can_submit' => $canSubmit,
            'photo_required' => $canSubmit ? $itp->isPhotoRequired($role) : false,
            'val' => $val,
            'can_acc_role' => $canAccRole,
            'visible_roles' => $visibleRoles,
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
            $updateData = [
                'keterangan' => $request->keterangan,
                'status' => 'done',
                'rejection_note' => null, // Clear rejection note on resubmit
            ];
            if ($photoPath) { $updateData['photo'] = $photoPath; }
            $existing->update($updateData);

            $isResubmit = in_array($existing->getOriginal('status'), ['needs_revision', 'rejected']);
            $msg = $isResubmit ? 'Data ITP berhasil di-resubmit!' : 'Data ITP berhasil diperbarui!';
        } else {
            ItpData::create([
                'itp_id' => $request->itp_id,
                'uploaded_by' => $user->id,
                'photo' => $photoPath,
                'keterangan' => $request->keterangan,
                'status' => 'done',
            ]);
            $msg = 'Data ITP berhasil disimpan!';
        }

        // Send notification to role above
        try {
            $userModel = User::find($user->id);
            $notifService = new NotificationService();
            $notifService->notifySubmit($itp, $userModel);
        } catch (\Throwable $e) {
            // Don't fail the submission if notification fails
        }

        return response()->json(['success' => true, 'message' => $msg]);
    }

    /**
     * Role hierarchy: key can ACC/reject the value role (one level below only)
     */
    private const ROLE_HIERARCHY = [
        'os'    => 'yard',
        'class' => 'os',
        'stat'  => 'class',
    ];

    /**
     * ACC (approve) ITP data — strict hierarchy enforcement
     */
    public function approveItpData($id)
    {
        $data = ItpData::with('uploader')->findOrFail($id);
        $user = session('user');
        $myRole = $user->role;

        // Check hierarchy: current user's role must be exactly one level above uploader's role
        if (!isset(self::ROLE_HIERARCHY[$myRole])) {
            return response()->json(['success' => false, 'message' => 'Role Anda tidak memiliki kewenangan ACC.'], 403);
        }

        $uploaderRole = $data->uploader->role ?? null;
        if ($uploaderRole !== self::ROLE_HIERARCHY[$myRole]) {
            $expected = self::ROLE_HIERARCHY[$myRole];
            return response()->json([
                'success' => false,
                'message' => "Anda (role {$myRole}) hanya bisa ACC data milik role {$expected}."
            ], 403);
        }

        $data->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        // Send notifications
        try {
            $itp = Itp::find($data->itp_id);
            $approver = User::find($user->id);
            $dataOwner = $data->uploader;
            $notifService = new NotificationService();
            $notifService->notifyApproved($itp, $approver, $dataOwner);
        } catch (\Throwable $e) {}

        return response()->json(['success' => true, 'message' => 'Data ITP berhasil di-ACC!']);
    }

    /**
     * Reject ITP data — hierarchy enforced, note is mandatory
     */
    public function rejectItpData(Request $request, $id)
    {
        $request->validate(['note' => 'required|string|min:3']);

        $data = ItpData::with('uploader')->findOrFail($id);
        $user = session('user');
        $myRole = $user->role;

        // Same hierarchy check as approve
        if (!isset(self::ROLE_HIERARCHY[$myRole])) {
            return response()->json(['success' => false, 'message' => 'Role Anda tidak memiliki kewenangan reject.'], 403);
        }

        $uploaderRole = $data->uploader->role ?? null;
        if ($uploaderRole !== self::ROLE_HIERARCHY[$myRole]) {
            $expected = self::ROLE_HIERARCHY[$myRole];
            return response()->json([
                'success' => false,
                'message' => "Anda (role {$myRole}) hanya bisa reject data milik role {$expected}."
            ], 403);
        }

        $data->update([
            'status' => 'needs_revision',
            'rejection_note' => $request->note,
            'approved_at' => null,
        ]);

        // Send rejection notification
        try {
            $itp = Itp::find($data->itp_id);
            $rejector = User::find($user->id);
            $dataOwner = $data->uploader;
            $notifService = new NotificationService();
            $notifService->notifyRejected($itp, $rejector, $dataOwner, $request->note);
        } catch (\Throwable $e) {}

        return response()->json(['success' => true, 'message' => 'Data ITP ditolak dan dikembalikan untuk revisi.']);
    }
}
