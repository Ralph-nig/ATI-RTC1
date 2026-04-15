<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(20);
        return view('client.notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $notification->markAsRead();

        // Resolve redirect URL from notification data
        $data        = $notification->data ?? [];
        $type        = $notification->type;
        $redirectUrl = url('/client/dashboard');

        if (in_array($type, ['ris_request', 'ris_approved', 'ris_rejected'])) {
            $risId = $data['ris_id'] ?? null;
            if ($risId) {
                $redirectUrl = route('client.ris.show', $risId);
            }
        } elseif (in_array($type, ['help_request', 'help_response'])) {
            $helpId = $data['help_request_id'] ?? null;
            if ($helpId) {
                $redirectUrl = route('client.help.show', $helpId);
            }
        }

        return response()->json([
            'success'      => true,
            'redirect_url' => $redirectUrl,
        ]);
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications()->update(['is_read' => true]);
        
        return response()->json(['success' => true]);
    }

    public function getUnreadCount()
    {
        $count = Auth::user()->unreadNotifications()->count();
        return response()->json(['count' => $count]);
    }

    public function getRecent()
    {
        $notifications = Auth::user()->notifications()->limit(10)->get();
        return response()->json(['notifications' => $notifications]);
    }
}