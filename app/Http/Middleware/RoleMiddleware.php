<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $role)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $userRole = in_array($user->role, ['user', 'requester'], true) ? 'client' : $user->role;

        if ($userRole !== $role) {
            return match ($user->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'service_staff' => redirect()->route('staff.dashboard'),
                default => redirect()->route('user.dashboard'),
            };
        }

        return $next($request);
    }
}
