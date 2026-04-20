@extends('backend.layouts.main')

@section('title', 'Dashboard')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @if (session('loginSuccess'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('loginSuccess') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                            <div>
                                <h4 class="text-primary mb-1 app-page-heading">
                                    Admin Dashboard
                                </h4>
                                <p class="mb-0">
                                    Welcome {{ $user->name }}. Use this dashboard to monitor requests, assign service staff,
                                    and keep the workflow moving.
                                </p>
                            </div>
                            <div class="app-card-actions">
                                <a href="{{ route('admin.requests') }}" class="btn btn-primary">
                                    Manage Requests
                                </a>
                                <a href="{{ route('admin.manage-users') }}" class="btn btn-outline-primary">
                                    Manage Users
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
                        <h6>Pending Requests</h6>
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

            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Role Overview</h5>
                        <p class="mb-3">Keep the right people in the system and make sure requests can be assigned quickly.</p>
                        <div class="d-flex justify-content-between align-items-center border rounded p-3 mb-3 gap-2">
                            <span>Service Staff</span>
                            <strong>{{ $totalStaff }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center border rounded p-3 gap-2">
                            <span>Clients</span>
                            <strong>{{ $totalClients }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">What To Do Next</h5>
                        <div class="mb-3">
                            <strong>1. Review new requests</strong>
                            <p class="mb-0 text-muted">Check pending requests and make sure urgent work is not delayed.</p>
                        </div>
                        <div class="mb-3">
                            <strong>2. Assign service staff</strong>
                            <p class="mb-0 text-muted">Allocate each request to the right staff member based on workload.</p>
                        </div>
                        <div>
                            <strong>3. Monitor completion</strong>
                            <p class="mb-0 text-muted">Track in-progress work and confirm that completed requests are closed properly.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
