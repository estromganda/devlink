<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
// use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

// use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * Liste paginée des notifications de l'utilisateur connecté.
     */
    public function index(Request $request): JsonResponse | \Inertia\Response
    {
        $user = $request->user();

        $notifications = $user->notifications()
            ->latest()
            ->paginate(20);

        if ($request->expectsJson()) {
            return response()->json([
                'notifications' => $notifications,
                'unread_count' => $user->unreadNotifications()->count(),
            ]);
        }
        return Inertia::render('notifications/Index', [
            'notifications' => $notifications,
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Marquer une notification comme lue.
     */
    public function markAsRead(Request $request, string $notification): JsonResponse | \Illuminate\Http\RedirectResponse
    {
        $userNotification = $request->user()
            ->notifications()
            ->whereKey($notification)
            ->firstOrFail();

        $userNotification->markAsRead();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Notification marquée comme lue.',
                'unread_count' => $request->user()->unreadNotifications()->count(),
            ]);
        }
        return back()->with('success', 'Notification marquée comme lue.');
    }

    /**
     * Marquer toutes les notifications comme lues.
     */
    public function markAllAsRead(Request $request): JsonResponse | \Illuminate\Http\RedirectResponse
    {
        $request->user()
            ->unreadNotifications()
            ->update([
                'read_at' => now(),
            ]);
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Toutes les notifications ont été marquées comme lues.',
                'unread_count' => 0,
            ]);
        }

        return back()->with(
            'success',
            'Toutes les notifications ont été marquées comme lues.'
        );
    }
}
