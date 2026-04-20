<?php

namespace App\Providers;

use App\Models\RequestUpdate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('backend.layouts.navbar', function ($view) {
            $user = Auth::user();

            if (!$user) {
                $view->with([
                    'notifications' => collect(),
                    'notificationCount' => 0,
                ]);
                return;
            }

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

            $unreadQuery = clone $query;
            if ($user->notifications_last_read_at) {
                $unreadQuery->where('created_at', '>', $user->notifications_last_read_at);
            }

            $notificationCount = $unreadQuery->count();
            $notifications = $query->latest()->take(8)->get();

            $view->with(compact('notifications', 'notificationCount'));
        });
    }
}
