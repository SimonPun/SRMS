@extends('backend.layouts.main')

@section('title', 'Edit Request')

@section('content')

    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 app-page-heading">Edit Request</h4>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Update Request #{{ $serviceRequest->id }}</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('requests.update', $serviceRequest) }}">
                    @csrf
                    @method('PATCH')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control"
                                value="{{ old('title', $serviceRequest->title) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select" required>
                                <option value="">Select category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->name }}"
                                        @selected(old('category', $serviceRequest->category) === $category->name)>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($categories->isEmpty())
                                <div class="text-warning mt-1">No categories available. Ask admin to add categories first.</div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Priority</label>
                            <select name="priority" class="form-select" required>
                                <option value="low" @selected(old('priority', $serviceRequest->priority) === 'low')>Low</option>
                                <option value="medium" @selected(old('priority', $serviceRequest->priority) === 'medium')>Medium</option>
                                <option value="high" @selected(old('priority', $serviceRequest->priority) === 'high')>High</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control"
                                value="{{ old('location', $serviceRequest->location) }}"
                                placeholder="Building, room, office or area">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4" required>{{ old('description', $serviceRequest->description) }}</textarea>
                        </div>

                        <div class="col-12">
                            <div class="app-form-actions">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                <a href="{{ route('requests.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>

    </div>

@endsection
