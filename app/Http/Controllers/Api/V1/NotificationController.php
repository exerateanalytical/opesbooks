<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\InAppNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /** GET /api/v1/notifications */
    public function index(Request $request): JsonResponse
    {
        $items = InAppNotification::where('user_id', $request->user()->id)
            ->latest()->limit(30)->get();

        return response()->json([
            'unread'        => $items->whereNull('read_at')->count(),
            'notifications' => $items->values(),
        ]);
    }

    /** POST /api/v1/notifications/{id}/read */
    public function markRead(Request $request, int $id): JsonResponse
    {
        InAppNotification::where('user_id', $request->user()->id)->where('id', $id)
            ->update(['read_at' => now()]);
        return response()->json(['ok' => true]);
    }

    /** POST /api/v1/notifications/read-all */
    public function markAllRead(Request $request): JsonResponse
    {
        InAppNotification::where('user_id', $request->user()->id)->whereNull('read_at')
            ->update(['read_at' => now()]);
        return response()->json(['ok' => true]);
    }
}
