@extends('backend.layouts.main')

@section('title', 'All Requests')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                <h4 class="mb-0 app-page-heading">All Service Requests</h4>
                <span class="badge bg-label-primary">{{ $requests->count() }} requests</span>
            </div>

            <div class="card-body border-bottom">
                <form method="GET" action="{{ route('admin.requests') }}">
                    <div class="row g-2">
                        <div class="col-sm-6 col-lg-3">
                            <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                                placeholder="Search title or location">
                        </div>
                        <div class="col-sm-6 col-lg-2">
                            <input type="text" name="category" class="form-control" value="{{ request('category') }}"
                                placeholder="Category">
                        </div>
                        <div class="col-sm-6 col-lg-2">
                            <select name="priority" class="form-select">
                                <option value="">All priorities</option>
                                <option value="low" @selected(request('priority') === 'low')>Low</option>
                                <option value="medium" @selected(request('priority') === 'medium')>Medium</option>
                                <option value="high" @selected(request('priority') === 'high')>High</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-lg-2">
                            <select name="status" class="form-select">
                                <option value="">All statuses</option>
                                <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                                <option value="in_progress" @selected(request('status') === 'in_progress')>In Progress</option>
                                <option value="completed" @selected(request('status') === 'completed')>Completed</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <select name="assigned_to" class="form-select">
                                <option value="">All staff</option>
                                @foreach ($users as $staffMember)
                                    <option value="{{ $staffMember->id }}" @selected((string) request('assigned_to') === (string) $staffMember->id)>
                                        {{ $staffMember->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="app-form-actions">
                                <button type="submit" class="btn btn-outline-primary">Filter</button>
                                <a href="{{ route('admin.requests') }}" class="btn btn-outline-secondary">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered app-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Location</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($requests as $serviceRequest)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $serviceRequest->user->name ?? 'Unknown User' }}</td>
                                    <td>{{ $serviceRequest->title }}</td>
                                    <td>{{ $serviceRequest->category }}</td>
                                    <td>{{ ucfirst($serviceRequest->priority) }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $serviceRequest->status)) }}</td>
                                    <td>
                                        @if ($serviceRequest->assignedStaff->isNotEmpty())
                                            @foreach ($serviceRequest->assignedStaff as $staffMember)
                                                <div class="small">
                                                    {{ $staffMember->name }} -
                                                    {{ ucfirst(str_replace('_', ' ', $staffMember->pivot->staff_status ?? 'pending')) }}
                                                </div>
                                            @endforeach
                                        @else
                                            Not Assigned
                                        @endif
                                    </td>
                                    <td>{{ $serviceRequest->location ?: 'N/A' }}</td>
                                    <td>{{ $serviceRequest->created_at->format('d M Y') }}</td>
                                    <td class="request-actions">
                                        <div class="dropdown">
                                            <button class="btn btn-outline-dark btn-sm dropdown-toggle w-100 request-action-trigger"
                                                type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Manage
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <div class="dropdown-header">Request Actions</div>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.requests.show', $serviceRequest) }}">
                                                        View details
                                                        <small>See full request information and assignment controls.</small>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.requests.show', $serviceRequest) }}#request-actions">
                                                        Assign staff
                                                        <small>Open the request page to choose a staff member.</small>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.requests.show', $serviceRequest) }}#request-actions">
                                                        Update status
                                                        <small>Change the request status from the detail page.</small>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">No requests found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
