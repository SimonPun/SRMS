<?php

namespace App\Http\Controllers;

use App\Models\RequestUpdate;
use Illuminate\Http\Request;
use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ServiceRequestController extends Controller
{
    public function create()
    {
        $categories = ServiceCategory::orderBy('name')->get();

        return view('backend.requests.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|exists:service_categories,name|max:100',
            'location' => 'nullable|string|max:255',
            'priority' => 'required|in:low,medium,high'
        ]);

        ServiceRequest::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'location' => $request->location,
            'priority' => $request->priority,
        ]);

        return back()->with('success', 'Request submitted successfully');
    }

    public function edit(Request $request, ServiceRequest $serviceRequest)
    {
        $this->authorizeClientOwnership($request, $serviceRequest);

        $categories = ServiceCategory::orderBy('name')->get();

        return view('backend.requests.edit', compact('serviceRequest', 'categories'));
    }

    public function update(Request $request, ServiceRequest $serviceRequest)
    {
        $this->authorizeClientOwnership($request, $serviceRequest);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|exists:service_categories,name|max:100',
            'location' => 'nullable|string|max:255',
            'priority' => 'required|in:low,medium,high',
        ]);

        $serviceRequest->update([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'location' => $request->location,
            'priority' => $request->priority,
        ]);

        return redirect()->route('requests.index')->with('success', 'Request updated successfully');
    }

    public function destroy(Request $request, ServiceRequest $serviceRequest)
    {
        $this->authorizeClientOwnership($request, $serviceRequest);

        $serviceRequest->delete();

        return redirect()->route('requests.index')->with('success', 'Request deleted successfully');
    }

    public function index(Request $request)
    {
        $requests = ServiceRequest::with(['assignedStaff', 'updates.updatedBy'])
            ->where('user_id', auth()->id());

        $requests = $this->applyFilters($requests, $request)
            ->latest()
            ->get();

        return view('backend.requests.index', compact('requests'));
    }

    public function allRequests(Request $request)
    {
        $requests = ServiceRequest::with(['user', 'assignedStaff', 'updates.updatedBy'])
            ->latest();

        $requests = $this->applyFilters($requests, $request);

        if ($request->filled('assigned_to')) {
            $requests->whereHas('assignedStaff', function ($query) use ($request) {
                $query->where('users.id', $request->assigned_to);
            });
        }

        $requests = $requests->get();
        $users = User::where('role', 'service_staff')->orderBy('name')->get();

        return view('backend.requests.all_requests', compact('requests', 'users'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed'
        ]);

        $serviceRequest = ServiceRequest::with('assignedStaff')->findOrFail($id);
        $user = $request->user();

        if (
            in_array($user->role, ['client', 'requester', 'user'], true) ||
            ($user->role === 'service_staff' && !$serviceRequest->assignedStaff->contains('id', $user->id))
        ) {
            abort(403);
        }

        DB::transaction(function () use ($request, $serviceRequest, $user) {
            $oldStatus = $serviceRequest->status;

            if ($user->role === 'service_staff') {
                $serviceRequest->assignedStaff()->updateExistingPivot($user->id, [
                    'staff_status' => $request->status,
                    'updated_at' => now(),
                ]);

                $serviceRequest->load('assignedStaff');
                $newStatus = $this->calculateOverallStatus($serviceRequest);
            } else {
                $newStatus = $request->status;
                $serviceRequest->assignedStaff()->syncWithPivotValues(
                    $serviceRequest->assignedStaff->pluck('id')->all(),
                    ['staff_status' => $request->status],
                    false
                );
            }

            $serviceRequest->update([
                'status' => $newStatus,
            ]);

            RequestUpdate::create([
                'service_request_id' => $serviceRequest->id,
                'updated_by' => $user->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'note' => $request->input('note'),
            ]);
        });

        return back()->with('success', 'Status updated successfully');
    }

    public function assign(Request $request, $id)
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
        ]);

        $serviceRequest = ServiceRequest::findOrFail($id);
        $assignedUsers = User::where('role', 'service_staff')
            ->whereIn('id', $request->user_ids)
            ->get();

        if ($assignedUsers->count() !== count($request->user_ids)) {
            return back()->with('error', 'Only service staff users can be assigned.');
        }
        $primaryAssigneeId = (int) $request->user_ids[0];

        DB::transaction(function () use ($serviceRequest, $assignedUsers, $request, $primaryAssigneeId) {
            $oldStatus = $serviceRequest->status;
            $staffIds = $assignedUsers->pluck('id')->all();
            $staffNames = $assignedUsers->pluck('name')->implode(', ');
            $existingStatuses = $serviceRequest->assignedStaff()
                ->whereIn('users.id', $staffIds)
                ->get()
                ->mapWithKeys(function ($staffUser) {
                    return [$staffUser->id => $staffUser->pivot->staff_status ?: 'pending'];
                });

            $syncData = [];
            foreach ($staffIds as $staffId) {
                $syncData[$staffId] = [
                    'staff_status' => $existingStatuses[$staffId] ?? 'pending',
                ];
            }

            $serviceRequest->update([
                'assigned_to' => $primaryAssigneeId,
            ]);
            $serviceRequest->assignedStaff()->sync($syncData);
            $serviceRequest->load('assignedStaff');
            $newStatus = $this->calculateOverallStatus($serviceRequest);
            if ($newStatus === 'pending' && !empty($staffIds) && $oldStatus !== 'completed') {
                $newStatus = 'in_progress';
            }
            $serviceRequest->update(['status' => $newStatus]);

            RequestUpdate::create([
                'service_request_id' => $serviceRequest->id,
                'updated_by' => $request->user()->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'note' => 'Assigned to ' . $staffNames,
            ]);
        });

        return back()->with('success', 'Request assigned successfully');
    }

    public function assignedRequests()
    {
        $requests = ServiceRequest::with(['user', 'assignedStaff', 'updates.updatedBy'])
            ->whereHas('assignedStaff', function ($query) {
                $query->where('users.id', auth()->id());
            });

        $requests = $this->applyFilters($requests, request())
            ->latest()
            ->get();

        return view('backend.requests.assigned_requests', compact('requests'));
    }

    public function show(Request $request, ServiceRequest $serviceRequest)
    {
        $user = $request->user();

        if (
            in_array($user->role, ['client', 'requester', 'user'], true) &&
            $serviceRequest->user_id !== $user->id
        ) {
            abort(403);
        }

        if (
            $user->role === 'service_staff' &&
            !$serviceRequest->assignedStaff()->where('users.id', $user->id)->exists()
        ) {
            abort(403);
        }

        $serviceRequest->load(['user', 'assignedStaff', 'updates.updatedBy']);
        $users = $user->role === 'admin'
            ? User::where('role', 'service_staff')->orderBy('name')->get()
            : collect();

        return view('backend.requests.show', compact('serviceRequest', 'users'));
    }

    protected function applyFilters($query, Request $request)
    {
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where(function ($innerQuery) use ($request) {
                $innerQuery->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%')
                    ->orWhere('location', 'like', '%' . $request->search . '%');
            });
        }

        return $query;
    }

    protected function calculateOverallStatus(ServiceRequest $serviceRequest): string
    {
        $staffStatuses = $serviceRequest->assignedStaff->pluck('pivot.staff_status')->filter();

        if ($staffStatuses->isEmpty()) {
            return 'pending';
        }

        if ($staffStatuses->every(fn($status) => $status === 'completed')) {
            return 'completed';
        }

        if ($staffStatuses->contains('in_progress') || $staffStatuses->contains('completed')) {
            return 'in_progress';
        }

        return 'pending';
    }

    protected function authorizeClientOwnership(Request $request, ServiceRequest $serviceRequest): void
    {
        $user = $request->user();

        if (
            !in_array($user->role, ['client', 'requester', 'user'], true) ||
            $serviceRequest->user_id !== $user->id
        ) {
            abort(403);
        }
    }
}
