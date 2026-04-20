@extends('backend.layouts.main')

@section('title', 'Create a Request')

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 app-page-heading">Create a Request</h4>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Create New Request</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('requests.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select" required>
                                <option value="">Select category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->name }}" @selected(old('category') === $category->name)>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                            @if ($categories->isEmpty())
                                <div class="text-warning mt-1">No categories available. Ask admin to add categories first.</div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Priority</label>
                            <select name="priority" class="form-select" required>
                                <option value="low" @selected(old('priority') === 'low')>Low</option>
                                <option value="medium" @selected(old('priority', 'medium') === 'medium')>Medium</option>
                                <option value="high" @selected(old('priority') === 'high')>High</option>
                            </select>
                            @error('priority')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control" value="{{ old('location') }}"
                                placeholder="Building, room, office or area">
                            @error('location')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <div class="app-form-actions">
                                <button type="submit" class="btn btn-primary">Submit Request</button>
                                <a href="{{ route('requests.index') }}" class="btn btn-outline-secondary">Back to My
                                    Requests</a>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>

    </div>

@endsection
