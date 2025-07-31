@extends('admin.layout')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Chapters</h2>
    <a href="{{ route('training.chapters.create') }}" class="btn btn-success mb-3">➕ Add Chapter</a>

    <table class="table">
        <thead><tr><th>#</th><th>Name</th><th>Order</th><th>Actions</th></tr></thead>
        <tbody>
        @foreach($branches as $branch)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $branch->name }}</td>
                <td>{{ $branch->order }}</td>
                <td>
                    <a href="{{ route('training.chapters.edit', $branch->id) }}" class="btn btn-sm btn-primary">Edit</a>
                    <form method="POST" action="{{ route('training.chapters.destroy', $branch->id) }}" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this chapter?')">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
