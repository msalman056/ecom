@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h2>Edit Brand</h2>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('admin.brand.update', $brand->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Brand Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $brand->name) }}" required>
        </div>
        <div class="mb-3">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" class="form-control" id="slug" name="slug" value="{{ old('slug', $brand->slug) }}" required>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Brand Image</label>
            <input type="file" class="form-control" id="image" name="image">
            @if ($brand->image)
                <div class="mt-2">
                    <img src="{{ asset('uploads/brands/' . $brand->image) }}" alt="Brand Image" width="100">
                </div>
            @endif
        </div>
        <button type="submit" class="btn btn-primary">Update Brand</button>
        <a href="{{ route('admin.brand') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
