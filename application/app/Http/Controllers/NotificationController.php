<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $unreadCount = $user->unreadNotifications()->count();
        $notifications = $user->notifications()->paginate(15);
        $unreadIds = $notifications->getCollection()->whereNull('read_at')->pluck('id')->all();
        $totalCount = $notifications->total();
        $user->unreadNotifications()->update(['read_at' => now()]);

        return view('notifications.index', compact('notifications', 'totalCount', 'unreadIds', 'unreadCount'));
    }

    public function markAsRead(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect()->route('notifications.index');
    }
}
