<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $user = session('user');
        $notifications = Notification::where('user_id', $user->id)
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('notifications', compact('notifications'));
    }

    public function unreadCount()
    {
        $user = session('user');
        if (!$user) return response()->json(['count' => 0]);

        $count = Notification::where('user_id', $user->id)->where('is_read', false)->count();

        $latest = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->with('sender')
            ->latest()
            ->first();

        return response()->json([
            'count' => $count,
            'latest_id' => $latest ? $latest->id : 0,
            'latest' => $latest ? [
                'id' => $latest->id,
                'title' => $latest->title,
                'message' => $latest->message,
                'sender' => $latest->sender->name ?? '-',
                'type' => $latest->type,
                'link' => $latest->link,
            ] : null,
        ]);
    }

    public function markRead($id)
    {
        $user = session('user');
        Notification::where('id', $id)->where('user_id', $user->id)->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    public function markAllRead()
    {
        $user = session('user');
        Notification::where('user_id', $user->id)->where('is_read', false)->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }
}
