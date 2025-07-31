@extends('admin.layout')

@section('content')

@php
$currentMonth = \Carbon\Carbon::now()->format('Y-m');
@endphp

<h1 class="h3 mb-4 text-gray-800">Data Contact Region Report</h1>
<meta name="csrf-token" content="{{ csrf_token() }}">

<form action="{{ route('report-contact-region.filteredRegionReport') }}" method="GET" id="filterForm">
    @csrf
    <div class="row mb-2">
        <div class="col-md-2">
            <select id="filterLembaga" name="filterLembaga" class="form-control">
                <option value="0">Select ALL</option>
                @foreach($lembagas as $lembaga)
                    <option value="{{ $lembaga->id }}">{{ $lembaga->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select id="filterRegion" name="filterRegion[]" class="form-control" multiple>
                <option value="0">Select ALL</option>
                @foreach($regions as $region)
                    <option value="{{ $region->id }}">{{ $region->kota }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <input type="month" id="startDate" name="startDate" class="form-control" placeholder="Start Month" max="{{ $currentMonth }}" required>
        </div>
        <div class="col-md-2">
            <input type="month" id="endDate" name="endDate" class="form-control" placeholder="End Month" max="{{ $currentMonth }}" required>
        </div>
        <div class="col-md-2 text-right">
            <button class="btn btn-primary" id="applyFilters">Filter</button>
            <button type="button" class="btn btn-secondary" id="resetFilters">Reset</button>
            <button type="button" class="btn btn-success" id="exportExcel">Excel</button>
        </div>
    </div>
</form>

@if(isset($contacts))
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Data Contact Region List</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Lokasi</th>
                                @foreach($months as $month)
                                    <th>{{ $month }}</th>
                                @endforeach
                                <th>Total</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contacts as $index => $contact)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $contact->kota }}</td>
                                @foreach($months as $month)
                                    <td>{{ $contact->{$month} ?? 0 }}</td>
                                @endforeach
                                <td>{{ $contact->total }}</td>
                                <td>{{ number_format($contact->percentage, 2) }}%</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td colspan="2"><b>Total</b></td>
                                @foreach($months as $month)
                                    <td><b>{{ array_sum(array_column($contacts, $month)) }}</b></td>
                                @endforeach
                                <td><b>{{ $totalSum }}</b></td>
                                <td><b>100%</b></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<script>
    document.getElementById('applyFilters').addEventListener('click', function(event) {
        let form = document.getElementById('filterForm');
        form.action = "{{ route('report-contact-region.filteredRegionReport') }}"; // Reset action for filtering
        form.method = "GET"; // Ensure it's a GET request
    });

    document.getElementById('exportExcel').addEventListener('click', function(event) {
        let form = document.getElementById('filterForm');
        let startDate = form.querySelector('#startDate').value;
        let endDate = form.querySelector('#endDate').value;

        if (!startDate || !endDate) {
            alert('Please fill in both the Start Date and End Date fields.');
            return;
        }

        form.action = "{{ route('report-contact-region.exportRegionReport') }}";
        form.method = "POST"; // Ensure it's a POST request for Excel export
        form.submit();
    });

    document.getElementById('resetFilters').addEventListener('click', function() {
        let form = document.getElementById('filterForm');
        form.reset();
        form.action = "{{ route('report-contact-region.filteredRegionReport') }}"; // Reset form action
    });
</script>
@endsection
