<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Project;
use App\Models\ProjectTemplate;
use App\Models\Modul;
use App\Models\Blok;
use App\Models\SubBlok;
use App\Models\Itp;
use App\Models\ItpData;
use App\Services\ProjectTemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminApiController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $totalUsers = User::count();
        $totalProjects = Project::count();
        $totalModuls = DB::table('moduls')->count();
        $totalItps = DB::table('itps')->count();

        $projects = Project::with('users')->orderBy('id', 'desc')->get();

        foreach ($projects as $project) {
            $t = DB::table('itps')
                ->join('sub_bloks', 'itps.sub_blok_id', '=', 'sub_bloks.id')
                ->join('bloks', 'sub_bloks.blok_id', '=', 'bloks.id')
                ->join('moduls', 'bloks.modul_id', '=', 'moduls.id')
                ->where('moduls.project_id', $project->id)->count();
            $a = DB::table('itp_data')
                ->join('itps', 'itp_data.itp_id', '=', 'itps.id')
                ->join('sub_bloks', 'itps.sub_blok_id', '=', 'sub_bloks.id')
                ->join('bloks', 'sub_bloks.blok_id', '=', 'bloks.id')
                ->join('moduls', 'bloks.modul_id', '=', 'moduls.id')
                ->where('moduls.project_id', $project->id)
                ->where('itp_data.status', 'approved')->count();
            $project->progress = $t > 0 ? round(($a / $t) * 100) : 0;
        }

        // Get all users with their projects
        $users = User::with('projects')->orderBy('id', 'desc')->get();

        // Get recent activity (last 15 ITP data entries with user and project info)
        $recentActivity = DB::table('itp_data')
            ->join('itps', 'itp_data.itp_id', '=', 'itps.id')
            ->join('sub_bloks', 'itps.sub_blok_id', '=', 'sub_bloks.id')
            ->join('bloks', 'sub_bloks.blok_id', '=', 'bloks.id')
            ->join('moduls', 'bloks.modul_id', '=', 'moduls.id')
            ->join('projects', 'moduls.project_id', '=', 'projects.id')
            ->join('users', 'itp_data.uploaded_by', '=', 'users.id')
            ->select([
                'itp_data.id',
                'itp_data.uploaded_by',
                'itp_data.status',
                'itp_data.keterangan',
                'itp_data.rejection_note',
                'itp_data.created_at',
                'itp_data.updated_at',
                'users.name as user_name',
                'users.role as user_role',
                'projects.kode_project',
                'itps.code as itp_code',
            ])
            ->orderBy('itp_data.updated_at', 'desc')
            ->limit(15)
            ->get();

        return response()->json([
            'total_users' => $totalUsers,
            'total_projects' => $totalProjects,
            'total_moduls' => $totalModuls,
            'total_itps' => $totalItps,
            'projects' => $projects,
            'users' => $users,
            'recent_activity' => $recentActivity,
        ]);
    }

    public function users()
    {
        return response()->json(User::orderBy('id', 'desc')->get());
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:4',
            'role' => 'required|in:admin,admin_galangan,yard,class,os,stat',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json(['success' => true, 'message' => 'User berhasil dibuat.', 'data' => $user], 201);
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        if ($user->role === 'admin') {
            return response()->json(['error' => 'Tidak bisa menghapus akun admin.'], 403);
        }
        $user->delete();
        return response()->json(['success' => true, 'message' => 'User berhasil dihapus.']);
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

        $data = $request->only('nama_project', 'kode_project', 'deskripsi', 'tanggal_kontrak', 'tanggal_mulai', 'deadline');

        if ($request->filled('template_id')) {
            $template = ProjectTemplate::findOrFail($request->template_id);
            $service = new ProjectTemplateService();
            $project = $service->cloneTemplate($template, $data);
        } else {
            $project = Project::create(array_merge($data, ['status' => 'active']));
        }

        return response()->json(['success' => true, 'message' => 'Project berhasil dibuat.', 'data' => $project], 201);
    }

    public function toggleProjectStatus($id)
    {
        $project = Project::findOrFail($id);
        $project->status = $project->status === 'active' ? 'archived' : 'active';
        $project->save();
        return response()->json(['success' => true, 'status' => $project->status]);
    }

    public function assignUser(Request $request)
    {
        $request->validate(['project_id' => 'required|exists:projects,id', 'user_id' => 'required|exists:users,id']);
        $exists = DB::table('project_user')->where('project_id', $request->project_id)->where('user_id', $request->user_id)->exists();
        if ($exists) return response()->json(['error' => 'User sudah di-assign.'], 409);

        DB::table('project_user')->insert([
            'project_id' => $request->project_id, 'user_id' => $request->user_id,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        return response()->json(['success' => true, 'message' => 'User berhasil di-assign.']);
    }

    public function unassignUser($projectId, $userId)
    {
        DB::table('project_user')->where('project_id', $projectId)->where('user_id', $userId)->delete();
        return response()->json(['success' => true, 'message' => 'User berhasil di-unassign.']);
    }

    public function templates()
    {
        return response()->json(ProjectTemplate::where('is_active', true)->orderBy('name')->get());
    }
}
