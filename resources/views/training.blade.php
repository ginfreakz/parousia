@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Missionary Training Videos & Articles</h1>

    @foreach($trainings as $training)
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">{{ $training->title }}</h5>
                <p class="card-text">{{ $training->description }}</p>
                
                @if($training->video_url)
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe class="embed-responsive-item" src="{{ $training->video_url }}" allowfullscreen></iframe>
                    </div>
                @endif
            </div>
        </div>
    @endforeach

    <!-- Pagination Links -->
    <div class="d-flex justify-content-center">
        {{ $trainings->links() }}
    </div>
</div>
@endsection
