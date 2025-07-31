@extends('admin.layout')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Set Target Penjangkauan</h1>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('target.update') }}">
            @csrf
            <div class="form-group">
                <label for="target_max">Target Maksimum:</label>
                <input type="number" name="target_max" id="target_max" class="form-control" 
                       value="{{ $target->target_max ?? 10 }}" required>
            </div>
            <div class="form-group">
                <label for="target_normal">Target Normal:</label>
                <input type="number" name="target_normal" id="target_normal" class="form-control" 
                       value="{{ $target->target_normal ?? 7 }}" required>
            </div>
            <div class="form-group">
                <label for="target_min">Target Minimum:</label>
                <input type="number" name="target_min" id="target_min" class="form-control" 
                       value="{{ $target->target_min ?? 4 }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Target</button>
        </form>
    </div>
</div>
@endsection
