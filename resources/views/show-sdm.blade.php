@extends('admin.layout')

@section('content')

<h1 class="h3 mb-4 text-gray-800">SDM</h1>

<div class="row mb-3">
<div class="col-md-2">
    <input type="text" id="filterKategori" class="form-control" placeholder="Filter by Kategori">
</div>
<div class="col-md-2">
    <input type="text" id="filterLembaga" class="form-control" placeholder="Filter by Lembaga">
</div>
    <div class="col-md-2">
        <input type="text" id="filterPeran" class="form-control" placeholder="Filter by Peran">
    </div>
    <div class="col-md-2">
        <select id="filterStatus" class="form-control">
            <option value="">All Status</option>
            <option value="Aktif">Aktif</option>
            <option value="Tidak Aktif">Tidak Aktif</option>
        </select>
    </div>
    <div class="col-md-2">
        <select id='filterBeasiswa' class="form-control">
            <option value="">All Beasiswa</option>
            <option value="Ya">Ya</option>
            <option value="Tidak">Tidak</option>
        </select>
    </div>
    <div class="col-md-2">
        <input type="text" id="filterLokasi" class="form-control" placeholder="Filter by Lokasi">
    </div>
</div>
<div class="row mt-2">
    <div class="col-md-2 offset-md-10 d-flex justify-content-end">
        <button class="btn btn-primary mr-2" id="applyFilters">Filter</button>
        <button class="btn btn-secondary" id="resetFilters">Reset</button>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">SDM List</h6>
                <a href="/create-sdm" class="btn btn-primary btn-sm float-right">
                    Add New
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Kategori SDM</th>
                                <th>Lembaga</th>
                                <th>Peran</th>
                                <th>Status SDM</th>
                                <th>Lokasi Pelayanan</th>
                                <th>Beasiswa Parousia</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($sdm as $s)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $s->nama_lengkap ?? 'N/A' }}</td>
                            <td>{{ $s->kategori_sdm ?? 'N/A' }}</td>
                            <td>{{ $s->lembaga_name ?? 'N/A' }}</td>
                            <td>{{ $s->peran ?? 'N/A' }}</td>
                            <td>{{ $s->status_sdm ?? 'N/A' }}</td>
                            <td>{{ $s->lokasi_pelayanan ?? 'N/A' }}</td>
                            <td>{{ data_get($s, 'pendidikan.beasiswa_parousia.status', 'N/A') }}</td>
                            <td>
                                <a href="{{ route('view-sdm', $s->id) }}" class="btn btn-primary btn-sm mr-2">View</a>
                                @if($roleId == 7)
                                <a href="{{ route('edit-sdm', $s->id) }}" class="btn btn-secondary btn-sm mr-2">Edit</a>
                                <form action="{{ route('delete-sdm', $s->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                                @endif
                            </td>
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
    const table = $('#dataTable').DataTable({
        searching: false,
        paging: true,
        pageLength: 10,
    });

    // Apply Filters
    $('#applyFilters').on('click', function () {
        let filters = {
            name: $('#filterName').val(),
            kategori: $('#filterKategori').val(),
            lembaga: $('#filterLembaga').val(),
            peran: $('#filterPeran').val(),
            status: $('#filterStatus').val(),
            beasiswa: $('#filterBeasiswa').val(),
            lokasi: $('#filterLokasi').val(),
        };

        loadFilteredData(filters);
    });

    // Reset Filters
    $('#resetFilters').on('click', function () {
        $('#filterName, #filterKategori, #filterLembaga, #filterPeran, #filterStatus, #filterBeasiswa, #filterLokasi').val('');
        loadFilteredData({});
    });

    // Function to Load Filtered Data
    function loadFilteredData(filters) {
        $.ajax({
            url: "{{ route('sdm.filter') }}",
            method: 'GET',
            data: filters,
            success: function (response) {
                table.clear();
                if (response.sdm.length > 0) {
                    response.sdm.forEach((s, index) => {
                        let actions = `<a href="/view-sdm/${s.id}" class="btn btn-primary btn-sm mr-2">View</a>`;

                        // Show Edit & Delete buttons only if roleId is 7
                        if (response.roleId == 7) {
                            actions += `
                                <a href="/edit-sdm/${s.id}" class="btn btn-secondary btn-sm mr-2">Edit</a>
                                <form action="/delete-sdm/${s.id}" method="POST" style="display:inline;" 
                                    onsubmit="return confirm('Are you sure you want to delete this item?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>`;
                        }

                        table.row.add([
                            index + 1,
                            s.nama_panggilan ?? 'N/A',
                            s.kategori_sdm ?? 'N/A',
                            s.lembaga_name ?? 'N/A',
                            s.peran ?? 'N/A',
                            s.status_sdm ?? 'N/A',
                            s.lokasi_pelayanan ?? 'N/A',
                            s.pendidikan?.beasiswa_parousia?.status ?? 'N/A',
                            actions
                        ]);
                    });
                } else {
                    table.row.add(['', 'No data found', '', '', '', '', '', '', '']);
                }
                table.draw();
            },
            error: function () {
                alert('Failed to fetch filtered data.');
            }
        });
    }

});


</script>

@endsection