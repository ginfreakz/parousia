@extends('admin.layout')

@section('content')

<h1 class="h3 mb-4 text-gray-800">Catatan SDM Lists</h1>

<div class="row mb-3">
    <div class="col-md-2">
        <select id="filterKategori" class="form-control">
            <option value="">All Kategori</option>
            <option value="1">Pelayanan</option>
            <option value="2">Kesehatan</option>
            <option value="3">Lain - Lain</option>
        </select>
    </div>
    <div class="col-md-2">
        <input type="text" id="filterNama" class="form-control" placeholder="Filter by Nama">
    </div>
    <div class="col-md-2 offset-md-10 d-flex justify-content-end">
            <button class="btn btn-primary mr-2" id="applyFilters">Filter</button>
            <button class="btn btn-secondary mr-2" id="resetFilters">Reset</button>
            <button class="btn btn-success" id="exportExcel">Excel</button>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Kategori</th>
                                <th>Tanggal</th>
                                <th>Catatan</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($catatan_sdm as $c)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $c->sdm_name }}</td>
                                @if($c->kategori_sdm == 1)
                                <td>Pelayanan</td>
                                @elseif($c->kategori_sdm == 2)
                                <td>Kesehatan</td>
                                @else
                                <td>Lain - Lain</td>
                                @endif
                                <td>{{ $c->tanggal }}</td>
                                <td>{{ $c->catatan }}</td>
                                <td>
                                    @if($user->role_id == 7)
                                    <a href="/edit-catatan-sdm/{{ $c->id }}" class="btn btn-primary btn-sm mr-2">Edit</a>
                                    <form action="/delete-catatan-sdm/{{ $c->id }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                    @elseif($user->role_id == 3)
                                    <a href="/edit-catatan-sdm/{{ $c->id }}" class="btn btn-primary btn-sm mr-2">Edit</a>
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
                name: $('#filterNama').val(),
                kategori: $('#filterKategori').val(),
            };
    
            loadFilteredData(filters);
        });
    
        // Reset Filters
        $('#resetFilters').on('click', function () {
            $('#filterNama, #filterKategori').val('');
            loadFilteredData({});
        });

        $('#exportExcel').on('click', function () {
            const queryParams = new URLSearchParams({
                name: $('#filterNama').val(),
                kategori: $('#filterKategori').val(),
            });

            const url = '{{ route("catatan-sdm.export") }}' + '?' + queryParams.toString();
            window.location.href = url;
        });
    
        function loadFilteredData(filters) {
            $.ajax({
                url: "{{ route('catatan-sdm.filter') }}",
                method: 'GET',
                data: filters,
                success: function (response) {
                    table.clear().draw();
                    if (response.catatan_sdm.length > 0) {
                        response.catatan_sdm.forEach((c, index) => {
                            let actions = '';
                            if (response.userRole == 7) {
                                actions = `
                                    <a href="/edit-catatan-sdm/${c.id}" class="btn btn-primary btn-sm mr-2">Edit</a>
                                    <form action="/delete-catatan-sdm/${c.id}" method="POST" class="d-inline">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                `;
                            }
                            else if (response.userRole == 3){
                                actions = `
                                    <a href="/edit-catatan-sdm/${c.id}" class="btn btn-primary btn-sm mr-2">Edit</a>
                                `;
                            }
                            table.row.add([
                                index + 1,
                                c.sdm_name ?? '-',
                                getKategoriName(c.kategori_sdm),
                                c.tanggal ?? '-',
                                c.catatan ?? '-',
                                actions
                            ]);
                        });
                    } else {
                        table.row.add(['', 'No data found', '', '', '', '']);
                    }
                    table.draw();
                },
                error: function () {
                    alert('Failed to fetch filtered data.');
                }
            });
        }
        function getKategoriName(kategori) {
            if (kategori == 1) {
                return 'Pelayanan';
            } else if (kategori == 2) {
                return 'Kesehatan';
            } else {
                return 'Lain - Lain';
            }
        }
    });
</script>
@endsection