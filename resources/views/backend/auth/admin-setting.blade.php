@extends('backend.layouts.main')

@section('title', 'Account Settings')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card">
                <!-- Card Header -->
                <div class="card-header">
                    <h5 class="card-title mb-0">Account Settings</h5>
                </div>
                <!-- Account Form -->
                <div class="card-body pt-4">
                    <!-- Display Flash Messages --> 
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert" id="flashMessage">
                            {{ session('success') }}
                        </div>
                    @elseif(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="flashMessage">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form id="formAccountSettings" method="POST" action="{{ $updateRoute ?? route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Name</label>
                                <input class="form-control" type="text" id="name" name="name" value="{{ old('name', $user->name) }}" autofocus />
                                @error('name')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">E-mail</label>
                                <input class="form-control" type="email" id="email" name="email" value="{{ old('email', $user->email) }}" placeholder="john.doe@example.com" />
                                @error('email')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label">New Password</label>
                                <input class="form-control" type="password" id="password" name="password" placeholder="Your New Password" />
                                @error('password')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" />
                                @error('password_confirmation')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mt-4 app-form-actions justify-content-end">
                            <button type="submit" class="btn btn-primary me-2">Save Changes</button>
                            <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const flashMessage = document.getElementById('flashMessage');
        if (flashMessage) {
            setTimeout(function() {
                flashMessage.classList.add('fade');
                flashMessage.classList.remove('show');

                setTimeout(() => {
                    flashMessage.remove();
                }, 500);
            }, 5000);
        }
    });
</script>

@endsection
