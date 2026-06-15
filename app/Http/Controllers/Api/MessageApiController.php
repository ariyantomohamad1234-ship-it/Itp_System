<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Message;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageApiController extends Controller
{
    public function channels(Request $request)
    {
        $user = $request->user();

        if (in_array($user->role, ['admin', 'admin_galangan'])) {
            $projects = Project::where('status', 'active')->orderBy('nama_project')->get();
        } else {
            $projects = Project::whereHas('users', fn($q) => $q->where('users.id', $user->id))
                ->where('status', 'active')->orderBy('nama_project')->get();
        }

        $result = [];
        foreach ($projects as $p) {
            $latest = Message::where('project_id', $p->id)->with('user')->latest()->first();
            $lastRead = DB::table('user_message_reads')
                ->where('user_id', $user->id)->where('project_id', $p->id)
                ->value('last_read_message_id') ?? 0;
            $unread = Message::where('project_id', $p->id)->where('id', '>', $lastRead)
                ->where('user_id', '!=', $user->id)->count();

            $result[] = [
                'project_id' => $p->id,
                'nama_project' => $p->nama_project,
                'kode_project' => $p->kode_project,
                'message_count' => Message::where('project_id', $p->id)->count(),
                'unread_count' => $unread,
                'latest_message' => $latest ? [
                    'message' => $latest->message,
                    'sender' => $latest->user->name ?? '-',
                    'time' => $latest->created_at->format('H:i'),
                ] : null,
            ];
        }

        return response()->json($result);
    }

    public function messages(Request $request, $projectId)
    {
        $user = $request->user();
        $messages = Message::where('project_id', $projectId)->with('user')->orderBy('created_at', 'asc')->get();

        if ($messages->isNotEmpty()) {
            DB::table('user_message_reads')->updateOrInsert(
                ['user_id' => $user->id, 'project_id' => $projectId],
                ['last_read_message_id' => $messages->last()->id, 'updated_at' => now()]
            );
        }

        $members = DB::table('project_user')
            ->join('users', 'project_user.user_id', '=', 'users.id')
            ->where('project_user.project_id', $projectId)
            ->select('users.id', 'users.name', 'users.role')->get();

        return response()->json([
            'messages' => $messages->map(fn($m) => [
                'id' => $m->id, 'message' => $m->message,
                'user_name' => $m->user->name, 'user_role' => $m->user->role,
                'user_id' => $m->user_id,
                'created_at' => $m->created_at->format('H:i'),
                'created_at_full' => $m->created_at->format('d M Y, H:i'),
            ]),
            'members' => $members,
        ]);
    }

    public function send(Request $request)
    {
        $request->validate(['project_id' => 'required|exists:projects,id', 'message' => 'required|string|max:2000']);
        $user = $request->user();

        $msg = Message::create([
            'project_id' => $request->project_id,
            'user_id' => $user->id,
            'message' => $request->message,
        ]);
        $msg->load('user');

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $msg->id, 'message' => $msg->message,
                'user_name' => $msg->user->name, 'user_role' => $msg->user->role,
                'user_id' => $msg->user_id,
                'created_at' => $msg->created_at->format('H:i'),
                'created_at_full' => $msg->created_at->format('d M Y, H:i'),
            ],
        ]);
    }

    // === NOTIFICATIONS ===
    public function notifications(Request $request)
    {
        $user = $request->user();
        $notifications = Notification::where('user_id', $user->id)
            ->with('sender')->orderBy('created_at', 'desc')->take(50)->get();
        return response()->json($notifications);
    }

    public function unreadNotificationCount(Request $request)
    {
        $user = $request->user();
        $count = Notification::where('user_id', $user->id)->where('is_read', false)->count();
        return response()->json(['count' => $count]);
    }

    public function markNotificationRead(Request $request, $id)
    {
        Notification::where('id', $id)->where('user_id', $request->user()->id)->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    public function markAllNotificationsRead(Request $request)
    {
        Notification::where('user_id', $request->user()->id)->where('is_read', false)->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }
}
