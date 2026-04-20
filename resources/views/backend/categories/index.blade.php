@extends('backend.layouts.main')

@section('title', 'Categories')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
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

        <div class="card">
            <div class="card-body">
                <h4 class="text-primary mb-1 app-page-heading">Manage Categories</h4>
                <p class="text-muted mb-3">Categories added here will appear in the client "Create a Request" form.</p>

                <form method="POST" action="{{ route('admin.categories.store') }}" class="row g-2 mb-4">
                    @csrf
                    <div class="col-md-8">
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                            placeholder="e.g. IT Support, Housekeeping, Maintenance" required>
                        @error('name', 'createCategory')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">Add Category</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered app-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Category Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $category)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $category->name }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                                data-bs-target="#editCategoryModal{{ $category->id }}">
                                                Edit
                                            </button>
                                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                                onsubmit="return confirm('Delete this category?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No categories added yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @foreach ($categories as $category)
                    <div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1"
                        aria-labelledby="editCategoryLabel{{ $category->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editCategoryLabel{{ $category->id }}">Edit Category</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="POST" action="{{ route('admin.categories.update', $category) }}">
                                    @csrf
                                    @method('PATCH')
                                    <div class="modal-body">
                                        <label class="form-label">Category Name</label>
                                        <input type="text" name="update_name" class="form-control" value="{{ $category->name }}"
                                            required>
                                        @if ($errors->updateCategory->has('update_name') && old('category_id') == $category->id)
                                            <div class="text-danger mt-1">{{ $errors->updateCategory->first('update_name') }}</div>
                                        @endif
                                        <input type="hidden" name="category_id" value="{{ $category->id }}">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Update Category</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
