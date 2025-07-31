@extends('admin.layout')

@section('content')
<div class="container py-4">
    <h2>Add Chapter</h2>
    <form method="POST" action="{{ route('training.chapters.store') }}">
        @csrf
        <div class="mb-3">
            <label>Chapter Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Order</label>
            <input type="number" name="order" class="form-control" value="0" required>
        </div>
        <button class="btn btn-primary">Save</button>
    </form>
</div>
@endsection
