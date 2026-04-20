@extends('backend.layouts.main')

@section('title', 'Staff Dashboard')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-primary app-page-heading">
                            Welcome {{ $user->name }} (Service Staff)
                        </h5>

                        <p class="mb-4">
                            Review the requests assigned to you, update their status, and keep the client informed.
                        </p>

                        <a href="{{ route('staff.requests') }}" class="btn btn-primary">
                            View Assigned Requests
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-lg-4 mb-4">
                <div class="card mb-3 mb-lg-0">
                    <div class="card-body">
                        <h6>Assigned Requests</h6>
                        <h3>{{ $assignedRequests }}</h3>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h6>In Progress</h6>
                        <h3>{{ $inProgressRequests }}</h3>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h6>Completed</h6>
                        <h3>{{ $completedRequests }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
