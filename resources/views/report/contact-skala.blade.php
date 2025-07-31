@extends('admin.layout')

@section('content')

@php
    $currentMonth = \Carbon\Carbon::now()->format('Y-m');
@endphp

<h1 class="h3 mb-4 text-gray-800">Contact Skala Report</h1>
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Filter Form -->
<form action="{{ route('report-contact-skala.filteredSkalaReport') }}" method="GET" id="filterForm">
    @csrf
    <div class="row mb-2">
        <!-- Lembaga Filter -->
        <div class="col-md-2">
            <select id="filterLembaga" name="filterLembaga" class="form-control">
                <option value="0">Select ALL</option>
                @foreach($lembagas as $lembaga)
                    <option value="{{ $lembaga->id }}" {{ request('filterLembaga') == $lembaga->id ? 'selected' : '' }}>
                        {{ $lembaga->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Region Filter -->
        <div class="col-md-2">
            <select id="filterRegion" name="filterRegion[]" class="form-control" multiple>
                <option value="0">Select ALL</option>
                @foreach($regions as $region)
                    <option value="{{ $region->id }}">{{ $region->kota }}</option>
                @endforeach
            </select>
        </div>

        <!-- Skala Filter -->
        <div class="col-md-2">
            <select id="filterSkala" name="filterSkala" class="form-control">
                <option value="">Select Skala</option>
                @for ($i = -3; $i <= 3; $i++)
                    <option value="{{ $i }}" {{ request('filterSkala') == (string) $i ? 'selected' : '' }}>
                        {{ $i }}
                    </option>
                @endfor
            </select>
        </div>

        <!-- Start Month -->
        <div class="col-md-2">
            <input type="month" id="startDate" name="startDate" class="form-control" max="{{ $currentMonth }}" value="{{ request('startDate') }}" required>
        </div>

        <!-- End Month -->
        <div class="col-md-2">
            <input type="month" id="endDate" name="endDate" class="form-control" max="{{ $currentMonth }}" value="{{ request('endDate') }}" required>
        </div>

        <!-- Action Buttons -->
        <div class="col-md-2 text-right">
            <button type="submit" class="btn btn-primary">Filter</button>
            <button type="button" class="btn btn-secondary" id="resetFilters">Reset</button>
            <button type="button" class="btn btn-success" id="exportExcel">Excel</button>
        </div>
    </div>
</form>

@if(isset($contacts) && count($contacts) > 0)
@php
    // Generate months array dynamically
    $months = [];
    $start = \Carbon\Carbon::parse(request('startDate'))->startOfMonth();
    $end = \Carbon\Carbon::parse(request('endDate'))->endOfMonth();

    while ($start <= $end) {
        $months[] = $start->format('M-y'); // Example: Apr-24
        $start->addMonth();
    }
@endphp

<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Contact Skala Report</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Skala</th>
                                @foreach ($months as $month)
                                    <th>{{ $month }}</th>
                                @endforeach
                                <th>Total</th>
                                <th>%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $index = 1; @endphp
                            @foreach ($contacts as $contact)
                            <tr>
                                <td>{{ $index++ }}</td>
                                <td>Skala {{ $contact['skala'] }}</td>
                                @foreach ($months as $month)
                                    <td>{{ $contact['monthly_counts'][$month] ?? 0 }}</td>
                                @endforeach
                                <td>{{ $contact['total'] }}</td>
                                <td>{{ number_format($contact['percentage'], 2) }}%</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td colspan="{{ 2 + count($months) }}"><b>Total</b></td>
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
@else
<div class="alert alert-warning mt-3">No data available for the selected filters.</div>
@endif
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('exportExcel').addEventListener('click', function () {
            const form = document.getElementById('filterForm');

            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const lembaga = document.getElementById('filterLembaga').value;
            const region = document.getElementById('filterRegion').value;
            const skala = document.getElementById('filterSkala').value;

            // Check if startDate and endDate are filled
            if (!startDate || !endDate) {
                alert('Please fill in both the Start Date and End Date fields.');
                return;
            }

            const url = "{{ route('report-contact-skala.exportSkalaReport') }}";
            const queryParams = new URLSearchParams({
                filterLembaga: lembaga,
                filterRegion: region,
                filterSkala: skala,
                startDate: startDate,
                endDate: endDate
            });

            window.location.href = `${url}?${queryParams.toString()}`;
        });
    });
</script>
@endsection