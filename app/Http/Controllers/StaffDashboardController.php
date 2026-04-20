<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;

class StaffDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $assignedRequests = ServiceRequest::whereHas('assignedStaff', function ($query) use ($user) {
            $query->where('users.id', $user->id);
        })->count();
        $inProgressRequests = ServiceRequest::whereHas('assignedStaff', function ($query) use ($user) {
            $query->where('users.id', $user->id);
        })
            ->where('status', 'in_progress')
            ->count();
        $completedRequests = ServiceRequest::whereHas('assignedStaff', function ($query) use ($user) {
            $query->where('users.id', $user->id);
        })
            ->where('status', 'completed')
            ->count();

        return view('backend.dashboard.staff_dashboard', compact(
            'user',
            'assignedRequests',
            'inProgressRequests',
            'completedRequests'
        ));
    }
}
