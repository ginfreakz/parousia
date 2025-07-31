@extends('admin.layout')

@section('content')
<div class="container py-5">
    <h1 class="mb-4 text-primary fw-bold">Select a Training Chapter</h1>
    <div class="row">
        @foreach($branchPages as $entry)
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $entry['branch']->name }}</h4>
                        </div>

                        @if($entry['first_page'])
                            <form action="{{ route('training.start') }}" method="POST">
                            @csrf
                            <input type="hidden" name="branch_id" value="{{ $entry['branch']->id }}">
                            <button type="submit" class="btn btn-primary mt-3">
                                Start Chapter
                            </button>
                        </form>
                        @else
                            <span class="text-muted">No pages available</span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
