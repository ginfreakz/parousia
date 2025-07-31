@extends('admin.layout')

@section('content')
<div class="container py-4">
    <h2>Edit Chapter</h2>
    <form method="POST" action="{{ route('training.chapters.update', $branch->id) }}">
        @csrf @method('PUT')
        <div class="mb-3">
            <label>Chapter Name</label>
            <input type="text" name="name" value="{{ $branch->name }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Order</label>
            <input type="number" name="order" value="{{ $branch->order }}" class="form-control" required>
        </div>
        <button class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
