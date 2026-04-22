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
use App\Services\ProjectTemplateService;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = DB::table('users')->count();
        $totalProjects = DB::table('projects')->count();
        $totalModuls = DB::table('moduls')->count();
        $totalItps = DB::table('itps')->count();
        $users = User::orderBy('id', 'desc')->get();
        $projects = Project::with('users')->orderBy('id', 'desc')->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'totalProjects', 'totalModuls', 'totalItps',
            'users', 'projects'
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
            'role' => 'required|in:admin,yard,class,os,stat',
        ]);

        DB::table('users')->insert([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/admin/dashboard')->with('success', 'User berhasil dibuat!');
    }

    public function deleteUser($id)
    {
        $user = DB::table('users')->where('id', $id)->first();
        if ($user && $user->role !== 'admin') {
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
            'kode_project' => 'required|string|max:100|unique:projects,kode_project',
            'deskripsi' => 'nullable|string',
            'tanggal_kontrak' => 'nullable|date',
            'tanggal_mulai' => 'nullable|date',
            'deadline' => 'nullable|date',
            'template_id' => 'nullable|exists:project_templates,id',
        ]);

        $projectData = $request->only('nama_project', 'kode_project', 'deskripsi', 'tanggal_kontrak', 'tanggal_mulai', 'deadline');

        // Mode template: clone dari template
        if ($request->filled('template_id')) {
            $template = ProjectTemplate::findOrFail($request->template_id);
            $service = new ProjectTemplateService();
            $project = $service->cloneTemplate($template, $projectData);

            $stats = [
                'moduls' => $project->moduls()->count(),
                'bloks'  => \App\Models\Blok::whereIn('modul_id', $project->moduls()->pluck('id'))->count(),
            ];

            return redirect('/admin/dashboard')->with('success',
                "Project berhasil dibuat dari template '{$template->name}'! ({$stats['moduls']} modul, {$stats['bloks']} blok)"
            );
        }

        // Mode custom/manual: buat project kosong
        DB::table('projects')->insert([
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
        $project = Project::with('users')->where('id', $id)->first();
        if (!$project) abort(404);

        $moduls = Modul::where('project_id', $id)->with('bloks.subBloks.itps')->get();
        $allUsers = User::where('role', '!=', 'admin')->get();

        try {
            $assemblyCodes = AssemblyCode::orderBy('code')->get();
        } catch (\Illuminate\Database\QueryException $e) {
            $assemblyCodes = collect();
        }

        return view('admin.manage-project', compact('project', 'moduls', 'allUsers', 'assemblyCodes'));
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
        ]);

        Itp::create($request->only(
            'sub_blok_id', 'assembly_code', 'assembly_description', 'code', 'item',
            'yard_val', 'class_val', 'os_val', 'stat_val'
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
}
