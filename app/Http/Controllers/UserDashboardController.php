<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $totalRequests = ServiceRequest::where('user_id', $user->id)->count();
        $pendingRequests = ServiceRequest::where('user_id', $user->id)
            ->where('status', 'pending')->count();
        $inProgressRequests = ServiceRequest::where('user_id', $user->id)
            ->where('status', 'in_progress')->count();
        $completedRequests = ServiceRequest::where('user_id', $user->id)
            ->where('status', 'completed')->count();
        $recentRequests = ServiceRequest::with('assignedStaff')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('backend.dashboard.user_dashboard', compact(
            'user',
            'totalRequests',
            'pendingRequests',
            'inProgressRequests',
            'completedRequests',
            'recentRequests'
        ));
    }
}
