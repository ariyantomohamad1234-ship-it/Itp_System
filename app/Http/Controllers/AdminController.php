<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Project;
use App\Models\ProjectTemplate;
use App\Models\Modul;
use App\Models\Blok;
use App\Models\SubBlok;
use App\Models\Itp;
use App\Models\User;
use App\Models\AssemblyCode;
use App\Models\ActivityLog;
use App\Services\ProjectTemplateService;

class AdminController extends Controller
{
    public function dashboard()
    {
        $userSession = session('user');
        $currentUser = User::find($userSession->id);
        
        $projectsQuery = Project::with('users')->orderBy('id', 'desc');
        $usersQuery = User::with('projects')->orderBy('id', 'desc');
        
        if ($currentUser->isAdminGalangan()) {
            // Admin Galangan only sees projects they are assigned to
            $assignedProjectIds = $currentUser->projects()->pluck('projects.id')->toArray();
            
            $projectsQuery->whereIn('id', $assignedProjectIds);
            
            // Users assigned to those projects
            $usersQuery->whereHas('projects', function($q) use ($assignedProjectIds) {
                $q->whereIn('projects.id', $assignedProjectIds);
            });
            
            $projects = $projectsQuery->get();
            $users = $usersQuery->get();
            
            $totalUsers = $users->count();
            $totalProjects = $projects->count();
            
            $totalModuls = DB::table('moduls')
                ->whereIn('project_id', $assignedProjectIds)
                ->count();
                
            $totalItps = DB::table('itps')
                ->join('sub_bloks', 'itps.sub_blok_id', '=', 'sub_bloks.id')
                ->join('bloks', 'sub_bloks.blok_id', '=', 'bloks.id')
                ->join('moduls', 'bloks.modul_id', '=', 'moduls.id')
                ->whereIn('moduls.project_id', $assignedProjectIds)
                ->count();

            // Scoped Activity
            $recentActivity = DB::table('itp_data')
                ->join('itps', 'itp_data.itp_id', '=', 'itps.id')
                ->join('sub_bloks', 'itps.sub_blok_id', '=', 'sub_bloks.id')
                ->join('bloks', 'sub_bloks.blok_id', '=', 'bloks.id')
                ->join('moduls', 'bloks.modul_id', '=', 'moduls.id')
                ->join('users', 'itp_data.uploaded_by', '=', 'users.id')
                ->whereIn('moduls.project_id', $assignedProjectIds)
                ->select('itp_data.*', 'users.name as user_name', 'users.role as user_role', 'itps.code as itp_code')
                ->orderBy('itp_data.updated_at', 'desc')
                ->limit(10)
                ->get();
        } else {
            // Super Admin sees everything
            $projects = $projectsQuery->get();
            $users = $usersQuery->get();
            
            $totalUsers = User::count();
            $totalProjects = Project::count();
            $totalModuls = DB::table('moduls')->count();
            $totalItps = DB::table('itps')->count();

            // All Activity
            $recentActivity = DB::table('itp_data')
                ->join('itps', 'itp_data.itp_id', '=', 'itps.id')
                ->join('users', 'itp_data.uploaded_by', '=', 'users.id')
                ->select('itp_data.*', 'users.name as user_name', 'users.role as user_role', 'itps.code as itp_code')
                ->orderBy('itp_data.updated_at', 'desc')
                ->limit(15)
                ->get();
        }

        // Calculate progress for each project
        foreach ($projects as $project) {
            $totalProjectItps = DB::table('itps')
                ->join('sub_bloks', 'itps.sub_blok_id', '=', 'sub_bloks.id')
                ->join('bloks', 'sub_bloks.blok_id', '=', 'bloks.id')
                ->join('moduls', 'bloks.modul_id', '=', 'moduls.id')
                ->where('moduls.project_id', $project->id)
                ->count();
                
            $approvedProjectItps = DB::table('itp_data')
                ->join('itps', 'itp_data.itp_id', '=', 'itps.id')
                ->join('sub_bloks', 'itps.sub_blok_id', '=', 'sub_bloks.id')
                ->join('bloks', 'sub_bloks.blok_id', '=', 'bloks.id')
                ->join('moduls', 'bloks.modul_id', '=', 'moduls.id')
                ->where('moduls.project_id', $project->id)
                ->where('itp_data.status', 'approved')
                ->count();
                
            $project->progress = $totalProjectItps > 0 ? round(($approvedProjectItps / $totalProjectItps) * 100) : 0;
        }

        return view('admin.dashboard', compact(
            'totalUsers', 'totalProjects', 'totalModuls', 'totalItps',
            'users', 'projects', 'currentUser', 'recentActivity'
        ));
    }

    // === USER MANAGEMENT ===

    public function createUser()
    {
        return view('admin.users-create');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:4',
            'role' => 'required|in:admin,admin_galangan,yard,class,os,stat',
        ]);

        DB::table('users')->insert([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        ActivityLog::record('create_user', "Membuat user baru: {$request->name} ({$request->role})");

        return redirect('/admin/dashboard')->with('success', 'User berhasil dibuat!');
    }

    public function deleteUser($id)
    {
        $user = DB::table('users')->where('id', $id)->first();
        if ($user && $user->role !== 'admin') {
            ActivityLog::record('delete_user', "Menghapus user: {$user->name}");
            DB::table('users')->where('id', $id)->delete();
            return redirect('/admin/dashboard')->with('success', 'User berhasil dihapus!');
        }
        return redirect('/admin/dashboard')->with('error', 'Tidak bisa menghapus akun admin!');
    }

    // === PROJECT MANAGEMENT ===

    public function createProject()
    {
        $templates = ProjectTemplate::getActiveTemplates();
        return view('admin.projects-create', compact('templates'));
    }

    public function storeProject(Request $request)
    {
        $request->validate([
            'nama_project' => 'required|string|max:255',
            'kode_project' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'tanggal_kontrak' => 'nullable|date',
            'tanggal_mulai' => 'nullable|date',
            'deadline' => 'nullable|date',
            'template_id' => 'nullable|exists:project_templates,id',
        ]);

        // Check uniqueness manually to provide better feedback
        $existingProject = DB::table('projects')->where('kode_project', $request->kode_project)->first();
        if ($existingProject) {
            $userSession = session('user');
            $currentUser = User::find($userSession->id);
            
            $isAssignedToMe = DB::table('project_user')
                ->where('project_id', $existingProject->id)
                ->where('user_id', $currentUser->id)
                ->exists();

            if ($currentUser->isAdminGalangan() && !$isAssignedToMe) {
                return back()->withErrors(['kode_project' => 'Kode project ini sudah digunakan di galangan lain. Silakan gunakan kode unik untuk galangan Anda.'])->withInput();
            }
            
            return back()->withErrors(['kode_project' => 'Kode project sudah digunakan.'])->withInput();
        }

        $projectData = $request->only('nama_project', 'kode_project', 'deskripsi', 'tanggal_kontrak', 'tanggal_mulai', 'deadline');

        // Mode template: clone dari template
        if ($request->filled('template_id')) {
            $template = ProjectTemplate::findOrFail($request->template_id);
            $service = new ProjectTemplateService();
            $project = $service->cloneTemplate($template, $projectData);

            // Auto-assign if admin_galangan
            $userSession = session('user');
            $currentUser = User::find($userSession->id);
            if ($currentUser->isAdminGalangan()) {
                DB::table('project_user')->insert([
                    'project_id' => $project->id,
                    'user_id' => $currentUser->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $stats = [
                'moduls' => $project->moduls()->count(),
                'bloks'  => \App\Models\Blok::whereIn('modul_id', $project->moduls()->pluck('id'))->count(),
            ];

            return redirect('/admin/dashboard')->with('success',
                "Project berhasil dibuat dari template '{$template->name}'! ({$stats['moduls']} modul, {$stats['bloks']} blok)"
            );
        }

        // Mode custom/manual: buat project kosong
        $projectId = DB::table('projects')->insertGetId([
            'nama_project' => $projectData['nama_project'],
            'kode_project' => $projectData['kode_project'],
            'deskripsi' => $projectData['deskripsi'],
            'tanggal_kontrak' => $projectData['tanggal_kontrak'],
            'tanggal_mulai' => $projectData['tanggal_mulai'],
            'deadline' => $projectData['deadline'],
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $project = Project::find($projectId);
        
        // Auto-assign if admin_galangan
        $userSession = session('user');
        $currentUser = User::find($userSession->id);
        if ($currentUser->isAdminGalangan()) {
            DB::table('project_user')->insert([
                'project_id' => $projectId,
                'user_id' => $currentUser->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        ActivityLog::record('create_project', "Membuat proyek baru: {$project->nama_project}", $project);

        return redirect('/admin/dashboard')->with('success', 'Project berhasil dibuat (mode manual)!');
    }

    /**
     * Toggle project status (active <-> archived)
     */
    public function toggleProjectStatus($id)
    {
        $project = DB::table('projects')->where('id', $id)->first();
        if (!$project) abort(404);

        $newStatus = $project->status === 'active' ? 'archived' : 'active';
        DB::table('projects')->where('id', $id)->update([
            'status' => $newStatus,
            'updated_at' => now(),
        ]);

        $msg = $newStatus === 'active' ? 'Project berhasil diaktifkan!' : 'Project berhasil dinonaktifkan!';
        return back()->with('success', $msg);
    }

    /**
     * Assign user to project
     */
    public function assignUser(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $exists = DB::table('project_user')
            ->where('project_id', $request->project_id)
            ->where('user_id', $request->user_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'User sudah di-assign ke project ini!');
        }

        DB::table('project_user')->insert([
            'project_id' => $request->project_id,
            'user_id' => $request->user_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'User berhasil di-assign ke project!');
    }

    /**
     * Unassign user from project
     */
    public function unassignUser($projectId, $userId)
    {
        DB::table('project_user')
            ->where('project_id', $projectId)
            ->where('user_id', $userId)
            ->delete();

        return back()->with('success', 'User berhasil di-unassign dari project!');
    }

    // === MANAGE PROJECT (Modul, Blok, SubBlok, ITP) ===

    public function manageProject($id)
    {
        $userSession = session('user');
        $currentUser = User::find($userSession->id);

        $project = Project::with('users')->where('id', $id)->first();
        if (!$project) abort(404);

        // Security check for Admin Galangan
        if ($currentUser->isAdminGalangan()) {
            $isAssigned = $currentUser->projects()->where('projects.id', $id)->exists();
            if (!$isAssigned) {
                return redirect('/admin/dashboard')->with('error', 'Anda tidak memiliki akses untuk mengelola project ini.');
            }
        }

        $moduls = Modul::where('project_id', $id)->with('bloks.subBloks.itps')->get();
        
        // Show all roles except Super Admin for assignment
        $allUsers = User::where('role', '!=', 'admin')->get();

        try {
            $assemblyCodes = AssemblyCode::orderBy('code')->get();
        } catch (\Illuminate\Database\QueryException $e) {
            $assemblyCodes = collect();
        }

        return view('admin.manage-project', compact('project', 'moduls', 'allUsers', 'assemblyCodes', 'currentUser'));
    }

    public function storeModul(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'nama_modul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        Modul::create($request->only('project_id', 'nama_modul', 'deskripsi'));
        return back()->with('success', 'Modul berhasil ditambahkan!');
    }

    public function storeBlok(Request $request)
    {
        $request->validate([
            'modul_id' => 'required|exists:moduls,id',
            'nama_blok' => 'required|string|max:255',
        ]);

        Blok::create($request->only('modul_id', 'nama_blok'));
        return back()->with('success', 'Blok berhasil ditambahkan!');
    }

    public function storeSubBlok(Request $request)
    {
        $request->validate([
            'blok_id' => 'required|exists:bloks,id',
            'nama_sub_blok' => 'required|string|max:255',
        ]);

        SubBlok::create($request->only('blok_id', 'nama_sub_blok'));
        return back()->with('success', 'Sub Blok berhasil ditambahkan!');
    }

    public function storeItp(Request $request)
    {
        $request->validate([
            'sub_blok_id' => 'required|exists:sub_bloks,id',
            'assembly_code' => 'required|string|max:255',
            'assembly_description' => 'nullable|string|max:500',
            'code' => 'required|string|max:100',
            'item' => 'required|string|max:500',
            'yard_val' => 'required|in:W,RV,-,NA',
            'class_val' => 'required|in:W,RV,-,NA',
            'os_val' => 'required|in:W,RV,-,NA',
            'stat_val' => 'required|in:W,RV,-,NA',
            'metode_inspeksi' => 'nullable|string|max:255',
            'alat_peralatan' => 'nullable|string|max:255',
            'referensi_rules' => 'nullable|string|max:255',
            'syarat_pemenuhan' => 'nullable|string|max:255',
        ]);

        Itp::create($request->only(
            'sub_blok_id', 'assembly_code', 'assembly_description', 'code', 'item',
            'yard_val', 'class_val', 'os_val', 'stat_val',
            'metode_inspeksi', 'alat_peralatan', 'referensi_rules', 'syarat_pemenuhan'
        ));

        return back()->with('success', 'Kode Inspeksi berhasil ditambahkan!');
    }

    public function deleteModul($id)
    {
        Modul::findOrFail($id)->delete();
        return back()->with('success', 'Modul berhasil dihapus!');
    }

    public function deleteBlok($id)
    {
        Blok::findOrFail($id)->delete();
        return back()->with('success', 'Blok berhasil dihapus!');
    }

    public function deleteSubBlok($id)
    {
        SubBlok::findOrFail($id)->delete();
        return back()->with('success', 'Sub Blok berhasil dihapus!');
    }

    public function deleteItp($id)
    {
        Itp::findOrFail($id)->delete();
        return back()->with('success', 'Kode Inspeksi berhasil dihapus!');
    }

    /**
     * Update module schedule (start_day, duration_days) — FEAT-07
     */
    public function updateModulSchedule(Request $request, $id)
    {
        $request->validate([
            'start_day' => 'nullable|integer|min:1',
            'duration_days' => 'nullable|integer|min:1',
        ]);

        $modul = Modul::findOrFail($id);
        $modul->update($request->only('start_day', 'duration_days'));

        return back()->with('success', 'Jadwal modul berhasil diperbarui!');
    }

    public function showLogs()
    {
        $logs = ActivityLog::with('user')->orderBy('created_at', 'desc')->paginate(50);
        return view('admin.logs', compact('logs'));
    }
}
