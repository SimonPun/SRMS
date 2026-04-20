@extends('backend.layouts.main')

@section('title', 'My Requests')

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 app-page-heading">My Service Requests</h4>
        </div>

        {{-- Success Message --}}
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
            <div class="card-header">
                <form method="GET" action="{{ route('requests.index') }}">
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
                        <div class="col-lg-3">
                            <div class="app-form-actions">
                                <button type="submit" class="btn btn-outline-primary">Filter</button>
                                <a href="{{ route('requests.index') }}" class="btn btn-outline-secondary">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table app-table align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Location</th>
                                <th>Created At</th>
                                <th>Actions</th>
                                <th>View</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($requests as $request)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $request->title }}</td>
                                    <td>{{ $request->category }}</td>
                                    <td>
                                        <span class="badge bg-label-warning">
                                            {{ ucfirst($request->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge 
                                        @if ($request->status == 'pending') bg-label-danger
                                        @elseif($request->status == 'in_progress') bg-label-info
                                        @else bg-label-success @endif">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>

                                    <td>
                                        @if ($request->assignedStaff->isNotEmpty())
                                            @foreach ($request->assignedStaff as $staffMember)
                                                <div class="small">
                                                    {{ $staffMember->name }} -
                                                    {{ ucfirst(str_replace('_', ' ', $staffMember->pivot->staff_status ?? 'pending')) }}
                                                </div>
                                            @endforeach
                                        @else
                                            Not Assigned
                                        @endif
                                    </td>
                                    <td>{{ $request->location ?: 'N/A' }}</td>

                                    <td>
                                        {{ $request->created_at->format('d M Y') }}
                                        @if ($request->updates->isNotEmpty())
                                            <div class="small text-muted mt-1">
                                                Last update:
                                                {{ ucfirst(str_replace('_', ' ', $request->updates->first()->new_status)) }}
                                                by {{ $request->updates->first()->updatedBy->name ?? 'System' }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('requests.edit', $request) }}" class="btn btn-sm btn-outline-warning">Edit</a>
                                            <form method="POST" action="{{ route('requests.destroy', $request) }}"
                                                onsubmit="return confirm('Delete this request? This cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('requests.show', $request) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">
                                        No requests found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>
            </div>
        </div>

    </div>

@endsection
