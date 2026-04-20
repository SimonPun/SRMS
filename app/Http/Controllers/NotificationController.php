<?php

namespace App\Http\Controllers;

use App\Models\RequestUpdate;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function recent(Request $request)
    {
        $user = $request->user();
        $query = $this->visibleQueryForUser($user);
        $count = $this->unreadCountForUser($user);

        $notifications = $query->latest()->take(8)->get()->map(function ($notification) use ($user) {
            $route = $user->role === 'admin'
                ? route('admin.requests.show', $notification->service_request_id)
                : ($user->role === 'service_staff'
                    ? route('staff.requests.show', $notification->service_request_id)
                    : route('requests.show', $notification->service_request_id));

            return [
                'id' => $notification->id,
                'status' => ucfirst(str_replace('_', ' ', $notification->new_status)),
                'time' => $notification->created_at->format('d M H:i'),
                'updated_by' => $notification->updatedBy->name ?? 'System',
                'request_title' => $notification->serviceRequest->title ?? ('Request #' . $notification->service_request_id),
                'note' => $notification->note,
                'is_unread' => !$user->notifications_last_read_at || $notification->created_at->gt($user->notifications_last_read_at),
                'url' => $route,
            ];
        });

        return response()->json([
            'count' => $count,
            'notifications' => $notifications,
        ]);
    }

    public function readAll(Request $request)
    {
        $user = $request->user();
        $user->forceFill([
            'notifications_last_read_at' => now(),
        ])->save();

        return response()->json([
            'success' => true,
            'count' => 0,
        ]);
    }

    public function dismiss(Request $request, RequestUpdate $requestUpdate)
    {
        $user = $request->user();

        if (!$this->userCanSeeNotification($user, $requestUpdate)) {
            abort(403);
        }

        $user->dismissedRequestUpdates()->syncWithoutDetaching([$requestUpdate->id]);

        return response()->json([
            'success' => true,
            'count' => $this->unreadCountForUser($user->fresh()),
        ]);
    }

    protected function visibleQueryForUser(User $user)
    {
        $query = RequestUpdate::with(['updatedBy:id,name', 'serviceRequest:id,title,user_id']);

        if ($user->role === 'service_staff') {
            $query->whereHas('serviceRequest.assignedStaff', function ($staffQuery) use ($user) {
                $staffQuery->where('users.id', $user->id);
            });
        } elseif (in_array($user->role, ['client', 'requester', 'user'], true)) {
            $query->whereHas('serviceRequest', function ($requestQuery) use ($user) {
                $requestQuery->where('user_id', $user->id);
            });
        }

        $query->whereDoesntHave('dismissedBy', function ($dismissedQuery) use ($user) {
            $dismissedQuery->where('users.id', $user->id);
        });

        return $query;
    }

    protected function unreadCountForUser(User $user): int
    {
        $query = $this->visibleQueryForUser($user);

        if ($user->notifications_last_read_at) {
            $query->where('created_at', '>', $user->notifications_last_read_at);
        }

        return $query->count();
    }

    protected function userCanSeeNotification(User $user, RequestUpdate $requestUpdate): bool
    {
        $requestUpdate->loadMissing(['serviceRequest.assignedStaff:id']);
        $serviceRequest = $requestUpdate->serviceRequest;

        if (!$serviceRequest) {
            return false;
        }

        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'service_staff') {
            return $serviceRequest->assignedStaff->contains('id', $user->id);
        }

        return in_array($user->role, ['client', 'requester', 'user'], true)
            && $serviceRequest->user_id === $user->id;
    }
}
