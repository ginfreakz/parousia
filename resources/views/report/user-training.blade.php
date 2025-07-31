@extends('admin.layout')

@section('content')

<h1 class="h3 mb-4 text-gray-800">User Training Report</h1>

<form method="GET" action="{{ route('report-user-training') }}" class="mb-3">
    <div class="row">
        <div class="col-md-4">
            <input type="text" name="name" class="form-control" placeholder="Search by user name" value="{{ request('name') }}">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary">Filter</button>
            <button type="button" class="btn btn-success" id="exportExcel">Excel</button>
        </div>
    </div>
</form>

<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Training Progress</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>User Name</th>
                                <th>Branch</th>
                                <th>Current Page</th>
                                <th>Total Pages</th>
                                <th>Completed Pages</th>
                                <th>Progress %</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($report as $row)
                                <tr>
                                    <td>{{ $row['user_name'] }}</td>
                                    <td>{{ $row['branch'] }}</td>
                                    <td>{{ $row['current_page'] }}</td>
                                    <td>{{ $row['total_pages'] }}</td>
                                    <td>{{ $row['completed_pages'] }}</td>
                                    <td>{{ $row['progress_percent'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function () {
    $('#dataTable').DataTable({
        searching: false,
        paging: true,
        pageLength: 10,
        columnDefs: [
            { width: "140px", targets: 0 },
        ]
    });

    $('#exportExcel').click(function () {
        const name = $('input[name="name"]').val();
        let url = '{{ route("training.report.export") }}';
        if (name) {
            url += '?name=' + encodeURIComponent(name);
        }
        window.location.href = url;
    });
});


</script>

@endsection