@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Fixed Sidebar -->
        <div class="col-md-2 px-0">
            <div class="list-group">
                @foreach(DB::table('training_pages')->where('branch_id', $branchId)->orderBy('order')->get() as $trainingPage)
                <a href="{{ route('training.show', $trainingPage->page_number) }}" 
                   class="list-group-item list-group-item-action rounded-0 
                          {{ $trainingPage->page_number > $currentPage ? 'disabled' : '' }} 
                          {{ $trainingPage->page_number == $pageNumber ? 'active bg-primary' : '' }}">
                    {{ $trainingPage->title }}
                    @if(in_array($trainingPage->page_number, $completedPages))
                        <span class="badge bg-success float-end">✓</span>
                    @endif
                </a>
                @endforeach
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-md-10 p-4">
            <div class="card shadow-lg">
                <!-- Card Header -->
                <div class="card-header bg-white border-bottom-0 d-flex justify-content-between align-items-center pb-3">
                    <h1 class="mb-0 fw-bold display-6 text-primary">{{ $page->title }}</h1>
                    
                    @if(isset($userRole) && $userRole == 7)
                    <div class="btn-group gap-2">
                        <a href="{{ route('training.edit', [$page->branch_id, $page->page_number]) }}" 
                        class="btn btn-outline-primary">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <a href="{{ route('training.create') }}" 
                           class="btn btn-success">
                            <i class="bi bi-plus-circle"></i> New Page
                        </a>
                        <form method="POST" action="{{ route('training.destroy', [$page->branch_id, $page->page_number]) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger"
                                    onclick="return confirm('Delete this page permanently?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                    @endif
                </div>

                <!-- Page Content -->
                <div class="training-content pt-4 pb-3 px-lg-4" >  
                {!! nl2br(e($page->content)) !!}  {{-- Convert newlines to <br> --}}  
                </div>  

                <!-- Card Body Content -->
                <div class="card-body px-5">
                    @if(isset($page->video_id) && $page->video_id)
                        <div class="w-full h-auto overflow-hidden">
                            <iframe 
                                src="https://www.youtube.com/embed/{{ $page->video_id }}" 
                                class="w-100 h-100"
                                title="{{ $page->title }}"
                                allowfullscreen>
                            </iframe>
                        </div>
                    @endif

                @if($pageNumber == $currentPage)
                    <form method="POST" action="{{ route('training.complete-page') }}" class="mt-5">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fs-4">
                            <i class="bi bi-check-circle me-2"></i>Mark as Complete
                        </button>
                    </form>
                @endif


                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Improved Content Styling */
.training-content {
    font-size: 1.15rem;
    line-height: 1.8;
    color: #374151;
    padding: 1.5rem 0;
}

.training-content h2 {
    font-size: 1.8rem;
    color: #1e3a8a;
    margin: 2rem 0 1rem;
    border-bottom: 2px solid #e5e7eb;
    padding-bottom: 0.5rem;
}

.training-content p {
    margin-bottom: 1.5rem;
}

.training-content ul,
.training-content ol {
    padding-left: 2rem;
    margin: 1.5rem 0;
}

.training-content li {
    margin-bottom: 0.5rem;
}

/* Enhanced Video Container */
.ratio-16x9 { 
  border: 3px solid #00f !important; 
  position: relative !important;
}

.ratio-16x9:hover {
    transform: scale(1.02);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.15);
}

.ratio-16x9 iframe {
    border: none; /* Remove default iframe border */
}

/* Improved Card Styling */
.card {
    border-radius: 12px;
    background: #fff;
    border: 1px solid #e5e7eb;
}

.card-header {
    padding: 1.5rem 2rem;
    background: #f8fafc;
}

/* Better Button Group Spacing */
.btn-group.gap-2 > * {
    margin-right: 0.5rem;
}
</style>
@endpush