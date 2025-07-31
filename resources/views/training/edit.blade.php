@extends('admin.layout')

@section('content')
<div class="container">
    <h1>Edit Training Page {{ $trainingPage->page_number }}</h1>

    <form method="POST" action="{{ route('training.update', $trainingPage->page_number) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" value="{{ old('title', $trainingPage->title) }}" 
                   class="form-control" required>
        </div>

        <div class="mb-3">
            <label>YouTube Video ID</label>
            <input type="text" name="video_id" value="{{ old('video_id', $trainingPage->video_id) }}"
                   class="form-control">
            <small class="text-muted">e.g. "dQw4w9WgXcQ" from https://youtu.be/dQw4w9WgXcQ</small>
        </div>

        <div class="mb-3">
            <label>Content</label>
            <textarea name="content" class="form-control" rows="10" 
                      required>{{ old('content', $trainingPage->content) }}</textarea>
        </div>

        <div class="mb-3">
            <input type="hidden" name="branch_id" value="{{ $trainingPage->branch_id }}">
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="{{ route('training.show', $trainingPage->page_number) }}" 
           class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection