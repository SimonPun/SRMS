@extends('backend.layouts.main')

@section('title', 'Manage Users')

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
                <h4 class="mb-0 app-page-heading">Manage Users</h4>
                <span class="badge bg-label-primary">{{ $users->count() }} users</span>
            </div>

            <div class="card-body">
                @php
                    $roleFilter = request('role');
                @endphp
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <a href="{{ route('admin.manage-users', ['role' => 'client']) }}"
                        class="btn {{ $roleFilter === 'client' ? 'btn-primary' : 'btn-outline-primary' }}">
                        Client
                    </a>
                    <a href="{{ route('admin.manage-users', ['role' => 'service_staff']) }}"
                        class="btn {{ $roleFilter === 'service_staff' ? 'btn-primary' : 'btn-outline-primary' }}">
                        Staff
                    </a>
                    <a href="{{ route('admin.manage-users') }}" class="btn btn-outline-secondary">
                        Reset
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table app-table align-middle">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Created</th>
                                <th>Change Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $managedUser)
                                <tr>
                                    <td>{{ $managedUser->name }}</td>
                                    <td>{{ $managedUser->email }}</td>
                                    <td>
                                        <span class="badge bg-label-{{ $managedUser->role === 'admin' ? 'danger' : 'primary' }}">
                                            {{ ucfirst($managedUser->role) }}
                                        </span>
                                    </td>
                                    <td>{{ $managedUser->created_at?->format('d M Y') ?? 'N/A' }}</td>
                                    <td>
                                        @if ($managedUser->role === 'admin')
                                            <span class="badge bg-label-secondary">Locked</span>
                                        @else
                                            <form method="POST" action="{{ route('admin.users.role', $managedUser) }}">
                                                @csrf
                                                @method('PATCH')
                                                <div class="input-group input-group-sm">
                                                    <select name="role" class="form-select" required>
                                                        <option value="service_staff" @selected($managedUser->role === 'service_staff')>Service Staff</option>
                                                        <option value="client" @selected(in_array($managedUser->role, ['client', 'requester', 'user']))>Client</option>
                                                    </select>
                                                    <button type="submit" class="btn btn-outline-primary">Save</button>
                                                </div>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
