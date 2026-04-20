@extends('backend.layouts.main')

@section('title', 'Request Details')

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

        <div class="row">
            <div class="col-xl-8 mb-4">
                <div class="card">
                    <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                        <div>
                            <h4 class="mb-0 app-page-heading">{{ $serviceRequest->title }}</h4>
                            <small class="text-muted">Request #{{ $serviceRequest->id }}</small>
                        </div>
                        <div class="app-badge-stack">
                            <span class="badge bg-label-primary">{{ ucfirst($serviceRequest->category) }}</span>
                            <span class="badge bg-label-warning">{{ ucfirst($serviceRequest->priority) }}</span>
                            <span class="badge bg-label-{{ $serviceRequest->status === 'pending' ? 'danger' : ($serviceRequest->status === 'in_progress' ? 'info' : 'success') }}">
                                {{ ucfirst(str_replace('_', ' ', $serviceRequest->status)) }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <strong>Client</strong>
                                <div>{{ $serviceRequest->user->name }}</div>
                            </div>
                            <div class="col-md-6">
                                <strong>Assigned Staff</strong>
                                <div>
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
                                </div>
                            </div>
                            <div class="col-md-6">
                                <strong>Location</strong>
                                <div>{{ $serviceRequest->location ?: 'Not provided' }}</div>
                            </div>
                            <div class="col-md-6">
                                <strong>Created</strong>
                                <div>{{ $serviceRequest->created_at->format('d M Y H:i') }}</div>
                            </div>
                        </div>

                        <h6>Description</h6>
                        <p class="mb-0">{{ $serviceRequest->description }}</p>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 mb-4">
                <div class="card mb-4" id="request-actions">
                    <div class="card-header">
                        <h5 class="mb-0">Actions</h5>
                    </div>
                    <div class="card-body">
                        @if (auth()->user()->role === 'admin')
                            <form method="POST" action="{{ route('admin.requests.assign', $serviceRequest) }}" class="mb-4">
                                @csrf
                                <label class="form-label">Assign Staff (Multi Select)</label>
                                <div class="small text-muted mb-2">Hold Ctrl (or Cmd on Mac) to select multiple staff.</div>
                                <div class="d-grid gap-2">
                                    <select name="user_ids[]" class="form-select" multiple size="6" required>
                                        @foreach ($users as $staffMember)
                                            <option value="{{ $staffMember->id }}" @selected($serviceRequest->assignedStaff->contains('id', $staffMember->id))>
                                                {{ $staffMember->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-outline-primary" type="submit">Save Assignment</button>
                                </div>
                            </form>
                        @endif

                        @if (in_array(auth()->user()->role, ['admin', 'service_staff'], true))
                            @php
                                $isStaffUser = auth()->user()->role === 'service_staff';
                                $myAssignment = $serviceRequest->assignedStaff->firstWhere('id', auth()->id());
                                $selectedStatus = $isStaffUser
                                    ? ($myAssignment?->pivot?->staff_status ?? 'pending')
                                    : $serviceRequest->status;
                            @endphp
                            <form method="POST"
                                action="{{ auth()->user()->role === 'admin' ? route('admin.requests.status', $serviceRequest) : route('staff.requests.status', $serviceRequest) }}">
                                @csrf
                                @method('PATCH')
                                <div class="mb-3">
                                    <label class="form-label">
                                        {{ $isStaffUser ? 'Update My Status' : 'Update Overall Status' }}
                                    </label>
                                    <select name="status" class="form-select" required>
                                        <option value="pending" @selected($selectedStatus === 'pending')>Pending</option>
                                        <option value="in_progress" @selected($selectedStatus === 'in_progress')>In Progress</option>
                                        <option value="completed" @selected($selectedStatus === 'completed')>Completed</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Progress Note</label>
                                    <textarea name="note" class="form-control" rows="4"
                                        placeholder="Add an update for the client or admin team"></textarea>
                                </div>
                                <div class="app-form-actions">
                                    <button class="btn btn-primary" type="submit">Save Update</button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
