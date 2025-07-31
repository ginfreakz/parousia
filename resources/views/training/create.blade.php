@extends('admin.layout')

@section('content')
<div class="container">
    <h1>Create New Training Page</h1>
    
    <form method="POST" action="{{ route('training.store') }}">
        @csrf

        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Select Chapter</label>
            <select name="branch_id" class="form-control" required>
                @foreach(DB::table('training_branches')->orderBy('order')->get() as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>YouTube Video ID</label>
            <input type="text" name="video_id" class="form-control">
        </div>

        <div class="mb-3">
            <label>Content</label>
            <textarea name="content" class="form-control" rows="10" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Create Page</button>
    </form>
</div>
@endsection