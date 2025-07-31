@extends('admin.layout')

@section('content')

<h1 class="h3 mb-4 text-gray-800">Region List</h1>
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Region List</h6>
                <button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal"
                    data-target="#addRegionModal">
                    Add New
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kota</th>
                                <th>Provinsi</th>
                                <th class="actions-column">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($regions as $region)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $region->kota }}</td>
                                    <td>{{ $region->provinsi }}</td>
                                    <td class="actions-column">
                                        <div class="d-flex align-items-center">
                                            <button type="button" class="btn btn-secondary btn-sm mr-2" data-toggle="modal"
                                                data-target="#editRegionModal{{ $region->id }}">
                                                Edit
                                            </button>
                                            <form action="{{ route('region.destroy', $region->id) }}" method="POST"
                                                style="display:inline;"
                                                onsubmit="return confirm('Are you sure you want to delete this region?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>


                                <!-- Edit Region Modal -->
                                <div class="modal fade" id="editRegionModal{{ $region->id }}" tabindex="-1" role="dialog"
                                    aria-labelledby="editRegionModalLabel{{ $region->id }}" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editRegionModalLabel{{ $region->id }}">Edit
                                                    Region</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('region.update', $region->id) }}" method="POST">
                                                    @csrf
                                                    @method('POST')
                                                    <div class="form-group">
                                                        <label for="kota">Kota</label>
                                                        <input type="text" class="form-control" id="kota" name="kota"
                                                            value="{{ $region->kota }}" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="provinsi">Provinsi</label>
                                                        <input type="text" class="form-control" id="provinsi"
                                                            name="provinsi" value="{{ $region->provinsi }}" required>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End of Edit Region Modal -->

                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Region Modal -->
<div class="modal fade" id="addRegionModal" tabindex="-1" role="dialog" aria-labelledby="addRegionModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRegionModalLabel">Add New Region</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('region.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="kota">Kota</label>
                        <input type="text" class="form-control" id="kota" name="kota" required>
                    </div>
                    <div class="form-group">
                        <label for="provinsi">Provinsi</label>
                        <input type="text" class="form-control" id="provinsi" name="provinsi" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>


<style>
    .actions-column {
        width: 150px;
        /* Adjust the width as needed */
    }
</style>

<script>
    $(document).ready(function () {
        $('#dataTable').DataTable();
    });

    function confirmDelete(form) {
        return confirm('Are you sure you want to delete this region?');
    }
</script>

@endsection