<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Message;
use App\Models\Project;

class MessageController extends Controller
{
    /**
     * Chat page - show project channels + messages
     */
    public function index(Request $request)
    {
        $user = session('user');

        // Get projects for this user
        if ($user->role === 'admin') {
            $projects = Project::where('status', 'active')
                ->withCount(['messages as unread_count' => function ($q) use ($user) {
                    // No real "unread" tracking, but we count recent messages
                }])
                ->orderBy('nama_project')
                ->get();
        } else {
            $projects = Project::whereHas('users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->where('status', 'active')
            ->orderBy('nama_project')
            ->get();
        }

        // Add latest message info to each project
        foreach ($projects as $p) {
            $p->latest_message = Message::where('project_id', $p->id)
                ->with('user')
                ->latest()
                ->first();
            $p->message_count = Message::where('project_id', $p->id)->count();
        }

        // Active project
        $activeProjectId = $request->query('project', $projects->first()?->id);
        $activeProject = $projects->firstWhere('id', $activeProjectId);

        $messages = [];
        $members = collect();

        if ($activeProject) {
            $messages = Message::where('project_id', $activeProject->id)
                ->with('user')
                ->orderBy('created_at', 'asc')
                ->get();

            // Get project members
            $members = DB::table('project_user')
                ->join('users', 'project_user.user_id', '=', 'users.id')
                ->where('project_user.project_id', $activeProject->id)
                ->select('users.*')
                ->get();
                
            // Update last read mark
            $lastMsg = $messages->last();
            if ($lastMsg) {
                DB::table('user_message_reads')->updateOrInsert(
                    ['user_id' => $user->id, 'project_id' => $activeProject->id],
                    ['last_read_message_id' => $lastMsg->id, 'updated_at' => now()]
                );
            }
        }

        return view('messages', compact('projects', 'activeProject', 'messages', 'members'));
    }

    /**
     * Send message (AJAX)
     */
    public function send(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'message' => 'required|string|max:2000',
        ]);

        $user = session('user');

        // Verify access
        if ($user->role !== 'admin') {
            $isAssigned = DB::table('project_user')
                ->where('project_id', $request->project_id)
                ->where('user_id', $user->id)
                ->exists();
            if (!$isAssigned) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
            }
        }

        $msg = Message::create([
            'project_id' => $request->project_id,
            'user_id' => $user->id,
            'message' => $request->message,
        ]);

        $msg->load('user');

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $msg->id,
                'message' => $msg->message,
                'user_name' => $msg->user->name,
                'user_role' => $msg->user->role,
                'user_id' => $msg->user_id,
                'created_at' => $msg->created_at->format('H:i'),
                'created_at_full' => $msg->created_at->format('d M Y, H:i'),
            ],
        ]);
    }

    /**
     * Fetch new messages (AJAX polling)
     */
    public function fetch(Request $request)
    {
        $projectId = $request->query('project_id');
        $afterId = $request->query('after_id', 0);
        $user = session('user');

        $messages = Message::where('project_id', $projectId)
            ->where('id', '>', $afterId)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();
            
        if ($messages->isNotEmpty() && $user) {
            DB::table('user_message_reads')->updateOrInsert(
                ['user_id' => $user->id, 'project_id' => $projectId],
                ['last_read_message_id' => $messages->last()->id, 'updated_at' => now()]
            );
        }

        $mappedMessages = $messages->map(function ($m) {
            return [
                'id' => $m->id,
                'message' => $m->message,
                'user_name' => $m->user->name,
                'user_role' => $m->user->role,
                'user_id' => $m->user_id,
                'created_at' => $m->created_at->format('H:i'),
                'created_at_full' => $m->created_at->format('d M Y, H:i'),
            ];
        });

        return response()->json(['messages' => $mappedMessages]);
    }

    /**
     * Get latest message and total unread count for global notifications
     */
    public function unreadCount()
    {
        $user = session('user');
        if (!$user) return response()->json(['latest_id' => 0, 'unread_count' => 0]);

        $projectIds = [];
        if ($user->role === 'admin') {
            $projectIds = Project::where('status', 'active')->pluck('id')->toArray();
        } else {
            $projectIds = DB::table('project_user')->where('user_id', $user->id)->pluck('project_id')->toArray();
        }

        if (empty($projectIds)) {
            return response()->json(['latest_id' => 0, 'unread_count' => 0]);
        }

        // Get unread count
        $unreadCount = 0;
        foreach ($projectIds as $pid) {
            $lastRead = DB::table('user_message_reads')
                ->where('user_id', $user->id)
                ->where('project_id', $pid)
                ->value('last_read_message_id') ?? 0;

            $unreadCount += Message::where('project_id', $pid)
                ->where('id', '>', $lastRead)
                ->where('user_id', '!=', $user->id)
                ->count();
        }

        // Get latest message for toast
        $latest = Message::with(['user', 'project'])
            ->whereIn('project_id', $projectIds)
            ->where('user_id', '!=', $user->id)
            ->latest('id')
            ->first();

        if ($latest) {
            return response()->json([
                'latest_id' => $latest->id,
                'unread_count' => $unreadCount,
                'sender' => $latest->user->name,
                'project_name' => $latest->project->kode_project,
                'message' => $latest->message,
            ]);
        }

        return response()->json(['latest_id' => 0, 'unread_count' => 0]);
    }
}
