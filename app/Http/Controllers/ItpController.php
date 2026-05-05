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
<<<<<<< HEAD
use App\Models\ActivityLog;
use App\Services\NotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
=======
use App\Services\NotificationService;
>>>>>>> 686ff83021b22abebb231249e1d8bddfbadec271

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

        $projectIds = $projects->pluck('id')->toArray();

        // === OPTIMIZED: Batch progress query instead of N+1 ===
        $totalCounts = [];
        $doneCounts = [];
        
        if (!empty($projectIds)) {
            $totalCounts = DB::table('itps')
                ->join('sub_bloks', 'itps.sub_blok_id', '=', 'sub_bloks.id')
                ->join('bloks', 'sub_bloks.blok_id', '=', 'bloks.id')
                ->join('moduls', 'bloks.modul_id', '=', 'moduls.id')
                ->whereIn('moduls.project_id', $projectIds)
                ->select('moduls.project_id', DB::raw('COUNT(*) as total'))
                ->groupBy('moduls.project_id')
                ->pluck('total', 'project_id');

            $doneCounts = DB::table('itps')
                ->join('sub_bloks', 'itps.sub_blok_id', '=', 'sub_bloks.id')
                ->join('bloks', 'sub_bloks.blok_id', '=', 'bloks.id')
                ->join('moduls', 'bloks.modul_id', '=', 'moduls.id')
                ->whereIn('moduls.project_id', $projectIds)
                ->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('itp_data')
                        ->whereColumn('itp_data.itp_id', 'itps.id')
                        ->whereIn('itp_data.status', ['done', 'approved']);
                })
                ->select('moduls.project_id', DB::raw('COUNT(*) as done'))
                ->groupBy('moduls.project_id')
                ->pluck('done', 'project_id');
        }

        // Calculate progress per project and filter
        $projectProgress = [];
        $activeProjects = [];
        
        $now = now();

        foreach ($projects as $p) {
            // Cek apakah project belum mulai
            if ($p->tanggal_mulai && \Carbon\Carbon::parse($p->tanggal_mulai)->isAfter($now)) {
                continue; // Skip project yang belum mulai
            }

            $total = $totalCounts[$p->id] ?? 0;
            $done = $doneCounts[$p->id] ?? 0;
            $percent = $total > 0 ? round(($done / $total) * 100) : 0;

            // Cek apakah project sudah selesai 100%
            if ($total > 0 && $done === $total) {
                continue; // Skip project yang sudah 100% selesai
            }

            $projectProgress[$p->id] = [
                'total' => $total,
                'done' => $done,
                'percent' => $percent,
            ];
            
            $activeProjects[] = $p;
        }

        $projects = collect($activeProjects);

        // === PENDING TASKS (User Friendly "List Item yang perlu dicek") ===
        $pendingTasks = [];
        if (!empty($projectIds)) {
            $roleField = $user->role . '_val';
            
            $pendingTasks = Itp::whereIn('sub_blok_id', function($q) use ($projectIds) {
                $q->select('id')->from('sub_bloks')->whereIn('blok_id', function($q) use ($projectIds) {
                    $q->select('id')->from('bloks')->whereIn('modul_id', function($q) use ($projectIds) {
                        $q->select('id')->from('moduls')->whereIn('project_id', $projectIds);
                    });
                });
            })
            ->where(function($q) use ($roleField) {
                $q->where($roleField, 'W')->orWhere($roleField, 'RV');
            })
            ->with(['itpData' => function($q) use ($user) {
                $q->join('users', 'itp_data.uploaded_by', '=', 'users.id')
                  ->where('users.role', $user->role);
            }])
            ->get()
            ->filter(function($itp) {
                // Check if this specific role has NOT uploaded/approved yet
                return $itp->itpData->isEmpty();
            })
            ->take(5);
        }

        return view('dashboard', compact('projects', 'projectProgress', 'pendingTasks'));
    }

    /**
     * Daftar modul + progress per modul
     * Implements 3-state locking: locked → active → completed
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

        // Sort by start_day so modules appear in construction order
        $modul = DB::table('moduls')
            ->where('project_id', $projectId)
            ->orderByRaw('COALESCE(start_day, 99999) ASC')
            ->get();

        // === OPTIMIZED: Batch progress query instead of N+1 ===
        $modulIds = $modul->pluck('id')->toArray();

        $totalCounts = DB::table('itps')
            ->join('sub_bloks', 'itps.sub_blok_id', '=', 'sub_bloks.id')
            ->join('bloks', 'sub_bloks.blok_id', '=', 'bloks.id')
            ->whereIn('bloks.modul_id', $modulIds)
            ->select('bloks.modul_id', DB::raw('COUNT(*) as total'))
            ->groupBy('bloks.modul_id')
            ->pluck('total', 'modul_id');

        $doneCounts = DB::table('itps')
            ->join('sub_bloks', 'itps.sub_blok_id', '=', 'sub_bloks.id')
            ->join('bloks', 'sub_bloks.blok_id', '=', 'bloks.id')
            ->whereIn('bloks.modul_id', $modulIds)
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('itp_data')
                    ->whereColumn('itp_data.itp_id', 'itps.id')
                    ->whereIn('itp_data.status', ['done', 'approved']);
            })
            ->select('bloks.modul_id', DB::raw('COUNT(*) as done'))
            ->groupBy('bloks.modul_id')
            ->pluck('done', 'modul_id');

        $modulProgress = [];
        foreach ($modul as $m) {
            $total = $totalCounts[$m->id] ?? 0;
            $done = $doneCounts[$m->id] ?? 0;
            $modulProgress[$m->id] = [
                'total' => $total,
                'done' => $done,
                'percent' => $total > 0 ? round(($done / $total) * 100) : 0,
            ];
        }

<<<<<<< HEAD
        // === Calculate "Hari ke-N" ===
        $projectStart = $project->tanggal_mulai ? \Carbon\Carbon::parse($project->tanggal_mulai) : null;
        $dayN = $projectStart ? max(1, (int) $projectStart->diffInDays(now()) + 1) : null;

        // === 3-STATE MODULE LOCKING ===
        // States: 'locked' (belum waktunya), 'active' (sedang berlangsung), 'completed' (sudah selesai)
=======
        // Calculate module lock status and "Hari ke-N"
        $projectStart = $project->tanggal_mulai ? \Carbon\Carbon::parse($project->tanggal_mulai) : null;
        $dayN = $projectStart ? (int) $projectStart->diffInDays(now()) + 1 : null;

>>>>>>> 686ff83021b22abebb231249e1d8bddfbadec271
        $modulLock = [];
        foreach ($modul as $m) {
            $startDay = $m->start_day ?? null;
            $durationDays = $m->duration_days ?? null;

<<<<<<< HEAD
            if ($projectStart && $startDay && $durationDays) {
                $endDay = $startDay + $durationDays - 1;

                if ($dayN < $startDay) {
                    // Haven't reached start day yet → LOCKED
                    $unlockDate = $projectStart->copy()->addDays($startDay - 1);
                    $modulLock[$m->id] = [
                        'state' => 'locked',
                        'unlock_date' => $unlockDate->format('d M Y'),
                        'start_day' => $startDay,
                        'end_day' => $endDay,
                        'duration_days' => $durationDays,
                        'days_until_unlock' => $startDay - $dayN,
                    ];
                } elseif ($dayN > $endDay) {
                    // Past the end day → COMPLETED
                    $modulLock[$m->id] = [
                        'state' => 'completed',
                        'unlock_date' => null,
                        'start_day' => $startDay,
                        'end_day' => $endDay,
                        'duration_days' => $durationDays,
                        'days_since_completed' => $dayN - $endDay,
                    ];
                } else {
                    // Within the window → ACTIVE
                    $daysRemaining = $endDay - $dayN + 1;
                    $daysElapsed = $dayN - $startDay + 1;
                    $timePercent = round(($daysElapsed / $durationDays) * 100);
                    $modulLock[$m->id] = [
                        'state' => 'active',
                        'unlock_date' => null,
                        'start_day' => $startDay,
                        'end_day' => $endDay,
                        'duration_days' => $durationDays,
                        'days_remaining' => $daysRemaining,
                        'days_elapsed' => $daysElapsed,
                        'time_percent' => $timePercent,
                    ];
                }
            } else {
                // No schedule set → default active
                $modulLock[$m->id] = [
                    'state' => 'active',
                    'unlock_date' => null,
                    'start_day' => $startDay,
                    'end_day' => null,
=======
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
>>>>>>> 686ff83021b22abebb231249e1d8bddfbadec271
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
        $modul = Modul::findOrFail($modulId);
        $project = Project::findOrFail($modul->project_id);

        $bloks = Blok::where('modul_id', $modulId)->get();

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
        $blok = Blok::findOrFail($blokId);
        $subbloks = SubBlok::where('blok_id', $blokId)->get();

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
        $subblok = SubBlok::findOrFail($subblokId);
        $blok = Blok::with('modul')->findOrFail($subblok->blok_id);
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

<<<<<<< HEAD
        // Visibility: everyone can see everyone's data (read-only for roles they can't ACC/Reject)
        $visibleRoles = ['yard', 'os', 'class', 'stat'];
=======
        // Visibility: which roles' data can this user see
        $visibleRoles = match ($role) {
            'os'    => ['yard', 'os'],
            'class' => ['yard', 'os', 'class'],
            'stat'  => ['yard', 'os', 'class', 'stat'],
            'yard'  => ['yard'],
            default => ['yard', 'os', 'class', 'stat'],
        };
>>>>>>> 686ff83021b22abebb231249e1d8bddfbadec271

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

<<<<<<< HEAD
        ActivityLog::record('submit_itp', $msg, $itp);

=======
>>>>>>> 686ff83021b22abebb231249e1d8bddfbadec271
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

<<<<<<< HEAD
        ActivityLog::record('approve_itp', "ACC data ITP milik {$data->uploader->name} (Role: {$data->uploader->role})", $itp);

=======
>>>>>>> 686ff83021b22abebb231249e1d8bddfbadec271
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

<<<<<<< HEAD
        ActivityLog::record('reject_itp', "Reject data ITP milik {$dataOwner->name} dengan alasan: {$request->note}", $itp);

        return response()->json(['success' => true, 'message' => 'Data ITP ditolak dan dikembalikan untuk revisi.']);
    }

    /**
     * Export ITP data to PDF Certificate
     */
    public function exportPdf($id)
    {
        $itpData = ItpData::with(['itp', 'uploader'])->findOrFail($id);
        $itp = $itpData->itp;
        
        // Fetch project info
        $project = DB::table('projects')
            ->join('moduls', 'projects.id', '=', 'moduls.project_id')
            ->join('bloks', 'moduls.id', '=', 'bloks.modul_id')
            ->join('sub_bloks', 'bloks.id', '=', 'sub_bloks.blok_id')
            ->where('sub_bloks.id', $itp->sub_blok_id)
            ->select('projects.*', 'moduls.nama_modul', 'bloks.nama_blok', 'sub_bloks.nama_sub_blok')
            ->first();

        $pdf = Pdf::loadView('reports.itp-certificate', compact('itpData', 'itp', 'project'));
        
        return $pdf->download("ITP_Certificate_{$itp->code}_{$itpData->id}.pdf");
=======
        return response()->json(['success' => true, 'message' => 'Data ITP ditolak dan dikembalikan untuk revisi.']);
>>>>>>> 686ff83021b22abebb231249e1d8bddfbadec271
    }
}
