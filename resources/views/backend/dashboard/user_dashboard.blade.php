@extends('backend.layouts.main')

@section('title', 'Client Dashboard')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                            <div>
                                <h4 class="text-primary mb-1 app-page-heading">My Dashboard</h4>
                                <p class="mb-0">
                                    Welcome {{ $user->name }}. Submit a service request, track progress, and review recent updates.
                                </p>
                            </div>
                            <div class="app-card-actions">
                                <a href="{{ route('requests.index') }}" class="btn btn-primary">
                                    Open My Requests
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h6>Total Requests</h6>
                        <h3 class="mb-0">{{ $totalRequests }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h6>Pending</h6>
                        <h3 class="mb-0">{{ $pendingRequests }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h6>In Progress</h6>
                        <h3 class="mb-0">{{ $inProgressRequests }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h6>Completed</h6>
                        <h3 class="mb-0">{{ $completedRequests }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Recent Requests</h5>
                        @forelse ($recentRequests as $request)
                            <div class="border rounded p-3 mb-3">
                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-1 gap-2">
                                    <strong>{{ $request->title }}</strong>
                                    <span class="badge bg-label-primary">
                                        {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                    </span>
                                </div>
                                <div class="small text-muted">
                                    Priority: {{ ucfirst($request->priority) }} |
                                    Submitted: {{ $request->created_at->format('d M Y') }}
                                    @if ($request->assignedStaff->isNotEmpty())
                                        | Assigned: {{ $request->assignedStaff->pluck('name')->implode(', ') }}
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="mb-0 text-muted">You have not submitted any requests yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-lg-5 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">How To Use The System</h5>
                        <div class="mb-3">
                            <strong>1. Submit a clear request</strong>
                            <p class="mb-0 text-muted">Add a short title, a helpful description, and the right priority level.</p>
                        </div>
                        <div class="mb-3">
                            <strong>2. Track progress</strong>
                            <p class="mb-0 text-muted">Check whether your request is pending, in progress, or completed.</p>
                        </div>
                        <div>
                            <strong>3. Review updates</strong>
                            <p class="mb-0 text-muted">Open your request list to see assignment and status changes from staff.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
