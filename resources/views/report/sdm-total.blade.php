@extends('admin.layout')

@section('content')

<h1 class="h3 mb-4 text-gray-800">Jumlah SDM Report</h1>

<form method="GET" action="{{ route('report-total-sdm') }}">
    @csrf
    <div class="row mb-2">
        <div class="col-md-2">
            <select id="lembaga_id" name="lembaga_id" class="form-control">
                <option value="0">Select ALL</option>
                @foreach($lembagas as $lembaga)
                    <option value="{{ $lembaga->id }}">{{ $lembaga->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary" id="applyFilters">Filter</button>
            <button type="button" class="btn btn-success" id="exportExcel">Excel</button>
        </div>
    </div>
</form>

<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Jumlah SDM</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Lembaga</th>
                                <th>Kategori SDM</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($counts as $row)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ optional($lembagas->firstWhere('id', $row->lembaga_id))->name ?? '-' }}</td>
                                <td>{{ $row->kategori_sdm }}</td>
                                <td>{{ $row->total }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-3">
                        <h5>Total Penerima Beasiswa Parousia: <strong>{{ $beasiswaCount }}</strong></h5>
                    </div>
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
            { width: "40px", targets: 0 }
        ]
    });

    $('#resetFilters').click(function (e) {
        e.preventDefault();
        window.location.href = '{{ route("report-total-sdm") }}';
    });

    $('#applyFilters').click(function (e) {
        e.preventDefault();
        $(this).closest('form').submit();
    });

    $('#exportExcel').click(function () {
        const lembagaId = $('select[name="lembaga_id"]').val();
        let url = '{{ route("sdm.report.export") }}';
        if (lembagaId) {
            url += '?lembaga_id=' + lembagaId;
        }
        window.location.href = url;
    });
});


</script>

@endsection