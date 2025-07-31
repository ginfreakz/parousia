@extends('admin.layout')

@section('content')

@php
$currentMonth = \Carbon\Carbon::now()->format('Y-m');
$hasFilters = isset($contacts);
@endphp

<h1 class="h3 mb-4 text-gray-800">Data Contact Report</h1>
<meta name="csrf-token" content="{{ csrf_token() }}">

<form action="{{ route('report-contact-list.showFilteredDataContacts') }}" method="GET" id="filterForm">
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
            <select id="filterName" name="filterName" class="form-control">
                <option value="0">Select ALL</option>
                @foreach($names as $name)
                    <option value="{{ $name->id }}">{{ $name->name }}</option>
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
                <h6 class="m-0 font-weight-bold text-primary">Data Contact List</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Utusan</th>
                                @foreach($months as $month)
                                    <th>{{ $month }}</th>
                                @endforeach
                                <th>Total</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contacts as $contact)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $contact->utusan }}</td>
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
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('exportExcel').addEventListener('click', function () {
                const form = document.getElementById('filterForm');
                const startDate = document.getElementById('startDate').value;
                const endDate = document.getElementById('endDate').value;

                // Check if startDate and endDate are filled
                if (!startDate || !endDate) {
                    alert('Please fill in both the Start Date and End Date fields.');
                    return;
                }

                const formData = new FormData(form);
                const url = '{{ route('report-contact-list.exportToExcel') }}';
                const xhr = new XMLHttpRequest();
                xhr.open('POST', url, true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.responseType = 'blob';
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        const disposition = xhr.getResponseHeader('Content-Disposition');
                        const matches = /"([^"]*)"/.exec(disposition);
                        const filename = (matches != null && matches[1] ? matches[1] : 'export') + '.xlsx';
                        const blob = new Blob([xhr.response], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                        const downloadUrl = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = downloadUrl;
                        a.download = filename;
                        document.body.appendChild(a);
                        a.click();
                        URL.revokeObjectURL(downloadUrl);
                    }
                };
                xhr.send(JSON.stringify(Object.fromEntries(formData)));
            });

            document.getElementById('resetFilters').addEventListener('click', function() {
                let form = document.getElementById('filterForm');
                form.reset();
            });
        });
    </script>
@endsection