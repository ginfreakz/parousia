@extends('admin.layout')

@section('content')

<h1 class="h3 mb-4 text-gray-800">Data Contact</h1>

<div class="row mb-2">
    <div class="col-md-2">
        <input type="text" id="filterName" class="form-control" placeholder="Filter Name">
    </div>
    <div class="col-md-2">
        <select id="filterSkala" class="form-control">
            <option value="">All Skala</option>
            @for($i = -3; $i <= 3; $i++)
                <option value="{{ $i }}">{{ $i }}</option>
            @endfor
        </select>
    </div>
    <div class="col-md-2">
        <input type="text" id="filterUtusan" class="form-control" placeholder="Filter Utusan">
    </div>
    <div class="col-md-2">
        <input type="text" id="filterRegion" class="form-control" placeholder="Filter Region">
    </div>
    <div class="col-md-2">
        <input type="date" id="startDate" class="form-control" placeholder="Start Date">
    </div>
    <div class="col-md-2">
        <input type="date" id="endDate" class="form-control" placeholder="End Date">
    </div>
</div>
<div class="row mb-2">
    <div class="col-md-2 offset-md-10 text-right">
        <button class="btn btn-primary" id="applyFilters">Filter</button>
        <button class="btn btn-secondary" id="resetFilters">Reset</button>
        <button class="btn btn-success" id="exportExcel">Excel</button>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Data Contact List</h6>
                @if(!in_array($userRole, [3, 4, 5]))
                <button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal"
                    data-target="#addContactModal">
                    Add New
                </button>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Kontak</th>
                                <th>Skala</th>
                                <th>Tanggal Kontak</th>
                                <th>Catatan</th>
                                <th>Utusan</th>
                                <th>Lokasi</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contacts as $contact)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        @if(!in_array($userRole, [3, 4, 5]))
                                            <a href="#" class="view-details-link" data-id="{{ $contact->id }}" data-name="{{ $contact->name }}">
                                                {{ $contact->name }}
                                            </a>
                                        @else
                                            {{ $contact->name }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($contact->status != 0 && !in_array($userRole, [3, 4, 5]))
                                            <a href="#" class="view-history-link" data-id="{{ $contact->id }}" data-name="{{ $contact->name }}" data-toggle="modal" data-target="#historyModal">
                                                {{ $contact->skala }}
                                            </a>
                                        @else
                                            {{ $contact->skala }}
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($contact->tanggal)->format('d-m-Y') }}</td>
                                    <td>{{ $contact->description }}</td>
                                    <td>{{ $contact->utusan }}</td>
                                    <td>{{ $contact->kota }}, {{ $contact->provinsi }}</td>
                                    <td>
                                        @if(!in_array($userRole, [3, 4, 5]))
                                        <a href="{{ route('contact.edit', $contact->id) }}"
                                            class="btn btn-primary btn-sm mr-2">Edit</a>
                                        @endif
                                        @if(isset($userRole) && ($userRole == 7))
                                        <form
                                            action="{{ route('contact.destroy', $contact->id) }}"
                                            method="POST" style="display:inline;"
                                            onsubmit="return confirm('Konfirmasi untuk menghapus contact ini:');">
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


<!-- Contact Details Modal -->
<div class="modal fade" id="contactModal" tabindex="-1" role="dialog" aria-labelledby="contactModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contactModalLabel">Contact Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="contactDetails">
                    <!-- Picture Preview -->
                    <div class="text-center mb-3">
                        <img id="picture_path" src="" alt="Contact Picture" class="img-thumbnail img-fluid"
                            style="max-width: 100%;">
                    </div>

                    <!-- Upload Picture Form -->
                    <form id="uploadPictureForm" enctype="multipart/form-data" method="POST">
                        @csrf
                        <input type="hidden" id="contact_id" name="contact_id">
                        <div class="form-group">
                            <label for="uploadPicture">Upload Picture</label>
                            <input type="file" name="picture" id="uploadPicture" class="form-control" accept="image/png"
                                required>
                        </div>
                        <button type="submit" class="btn btn-primary">Upload</button>
                        @if(isset($userRole) && ($userRole == 7))
                            <button id="deletePictureBtn" class="btn btn-danger">Delete Picture</button>
                        @endif
                    </form>

                    <!-- Data Table -->
                    <table class="table table-bordered mt-3">
                        <tr>
                            <th>Name</th>
                            <td id="contactName"></td>
                        </tr>
                        <tr>
                            <th>Skala</th>
                            <td id="contactSkala"></td>
                        </tr>
                        <tr>
                            <th>Jenis Kelamin</th>
                            <td id="contactKelamin"></td>
                        </tr>
                        <tr>
                            <th>Suku</th>
                            <td id="contactSuku"></td>
                        </tr>
                        <tr>
                            <th>Usia</th>
                            <td id="contactUsia"></td>
                        </tr>
                        <tr>
                            <th>Ulang Tahun</th>
                            <td id="contactUlangTahun"></td>
                        </tr>
                        <tr>
                            <th>Phone</th>
                            <td id="contactPhone"></td>
                        </tr>
                        <tr>
                            <th>Pekerjaan</th>
                            <td id="contactPekerjaan"></td>
                        </tr>
                        <tr>
                            <th>Region</th>
                            <td id="contactRegion"></td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td id="contactDescription"></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- History Modal -->
<div class="modal fade" id="historyModal" tabindex="-1" role="dialog" aria-labelledby="historyModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historyModalLabel">Contact History</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5 id="contactName"></h5>
                <table id="historyTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kontak</th>
                            <th>Skala</th>
                            <th>Tanggal Kontak</th>
                            <th>Catatan</th>
                            <th>Utusan</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="historyTableBody">
                        <!-- History rows will be appended here dynamically -->
                    </tbody>
                </table>

                <!-- New History Entry Form -->
                <h5>Add New History</h5>
                <form id="historyForm">
                    <div class="form-group">
                        <label for="historyDate">Date</label>
                        <input type="date" id="historyDate" class="form-control" max="">
                    </div>
                    <div class="form-group">
                        <label for="skala">Skala</label>
                        <select class="form-control" id="skala" name="skala">

                        </select>
                    </div>
                    <div class="form-group">
                        <label for="historyDescription">Short Description</label>
                        <input type="text" id="historyDescription" class="form-control"
                            placeholder="Enter a short description">
                    </div>
                    <button type="button" id="updateHistoryBtn" class="btn btn-primary">Update History</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Contact Modal -->
<div class="modal fade" id="addContactModal" tabindex="-1" role="dialog" aria-labelledby="addContactModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addContactModalLabel">Add New Contact</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <form id="addContactForm" action="{{ route('contact.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div class="invalid-feedback" id="nameError"></div>
                    </div>
                    <div class="form-group">
                        <label for="kelamin">Jenis Kelamin</label>
                        <select class="form-control" id="kelamin" name="kelamin" required>
                            <option discardable disabled selected>Pilih</option>
                            <option value="Perempuan">Perempuan</option>
                            <option value="Laki-laki">Laki-laki</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="suku">Suku</label>
                        <input type="text" class="form-control" id="suku" name="suku">
                    </div>
                    <div class="form-group">
                        <label for="usia">Usia</label>
                        <input type="number" class="form-control" id="usia" name="usia">
                    </div>
                    <div class="form-group">
                        <label for="ulang_tahun">Ulang Tahun</label>
                        <input type="date" class="form-control" id="ulang_tahun" name="ulang_tahun">
                    </div>
                    <div class="form-group">
                        <label for="pekerjaan">Pekerjaan</label>
                        <input type="text" class="form-control" id="pekerjaan" name="pekerjaan">
                    </div>
                    </select>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone">
                    </div>
                    <div class="form-group">
                        <label for="region">Region</label>
                        <select class="form-control" id="region" name="region_id" required>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}-{{ $region-> kota }}">
                                    {{ $region->kota }}-{{ $region->provinsi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mt-3">
                        <!-- Added mt-3 class for top margin -->
                        <label for="tanggal">Tanggal</label>
                        @if(isset($userRole) && ($userRole == 7))
                            <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                        @else
                            <input type="date" class="form-control" id="tanggal" name="tanggal" required
                                min="{{ \Carbon\Carbon::now()->subWeek()->toDateString() }}">
                        @endif
                    </div>
                    @if(isset($userRole) && ($userRole == 7))
                        <div class="form-group
                            <label for="utusan">Utusan</label>
                            <select class="form-control" id="utusan" name="utusan" required>
                                <option value="" disabled selected>Pilih Utusan</option>
                                @foreach($listUtusan as $utusan)
                                    <option value="{{ $utusan->utusan_id }}-{{ $utusan->utusan }}">
                                        {{ $utusan->utusan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function () {

        const userRole = @json($userRole);
        const table = $('#dataTable').DataTable({
            searching: false,
            paging: true,
            pageLength: 10,
            columnDefs: [
                { width: "5%", targets: 0 },  /* No. */
                { width: "10%", targets: 1 }, /* Name */
                { width: "5%", targets: 2 }, /* Skala */
                { width: "10%", targets: 3 }, /* Tanggal */
                { width: "30%", targets: 4 }, /* Description */
                { width: "15%", targets: 5 }, /* Utusan */
                { width: "15%", targets: 6 }, /* Kota, Provinsi */
                { width: "20%", targets: 7 }, /* Actions */
            ],
        });

        const historyTable = $('#historyTable').DataTable({
            searching: false,
            paging: true,
            pageLength: 5,
            info: false,
            ordering: false,
        });

        $('#exportExcel').on('click', function () {
            const queryParams = new URLSearchParams({
                name: $('#filterName').val(),
                skala: $('#filterSkala').val(),
                utusan: $('#filterUtusan').val(),
                region: $('#filterRegion').val(),
                startDate: $('#startDate').val(),
                endDate: $('#endDate').val(),
            });

            const url = '{{ route("contact-list.export") }}' + '?' + queryParams.toString();
            window.location.href = url;
        });

        // Handle Contact Details Modal
        $('#dataTable tbody').on('click', '.view-details-link', function (e) {
            e.preventDefault();

            const contactId = $(this).data('id');
            const defaultPicturePath =
                '/picture_path/default.jpg'; // Replace with your actual default image path

            // Clear existing image to avoid stale data
            $('#picture_path').attr('src', '');
            $('#contactName, #contactSkala, #contactKelamin, #contactSuku, #contactUsia, #contactUlangTahun,#contactPhone, #contactPekerjaan, #contactRegion, #contactDescription')
                .text('');

            // Fetch contact details via AJAX
            $.ajax({
                url: `/contacts/${contactId}`, // API endpoint to fetch contact details
                method: 'GET',
                success: function (response) {
                    $('#contactName').text(response.name);
                    $('#contactSkala').text(response.skala);
                    $('#contactKelamin').text(response.kelamin);
                    $('#contactSuku').text(response.suku);
                    $('#contactUsia').text(response.usia);
                    $('#contactUlangTahun').text(response.ulang_tahun);
                    $('#contactPhone').text(response.phone);
                    $('#contactPekerjaan').text(response.pekerjaan);
                    $('#contactRegion').text(response.region);
                    $('#contactDescription').text(response.description);

                    // Use picture_path if it exists; fallback to default image
                    const picturePath = response.picture_path ?
                        `/picture_path/${response.picture_path}` :
                        defaultPicturePath;

                    $('#picture_path').attr('src', picturePath);
                },
                error: function () {
                    alert('Failed to fetch contact details.');
                },
            });

            // Show the modal
            $('#contact_id').val(contactId);
            $('#contactModal').modal('show');
        });


        $('#uploadPictureForm').on('submit', function (e) {
            e.preventDefault();

            const contactId = $('#contactModal').data('contact-id'); // Pass the contact ID dynamically
            const formData = new FormData(this); // Use FormData to send the file

            $.ajax({
                url: `/contacts/${contactId}/upload-picture`, // Endpoint for uploading picture
                type: 'POST',
                data: formData,
                contentType: false, // Required for FormData
                processData: false, // Required for FormData
                success: function (response) {
                    alert('Picture uploaded successfully!');
                    const picturePath = `/picture_path/${response.picture_path}`;
                    $('#picture_path').attr('src',
                        picturePath); // Update the image in the modal
                    $('#contactModal').modal('hide');
                },
                error: function (xhr) {
                    alert(
                        `Failed to upload picture: ${xhr.responseJSON?.message || 'Unknown error'}`
                    );
                },
            });
        });

        $('#deletePictureBtn').on('click', function (e) {
            e.preventDefault();

            const contactId = $('#contact_id').val(); // Get the contact ID

            $.ajax({
                url: `/contacts/${contactId}/delete-picture`, // Endpoint for deleting picture
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}', // CSRF token for security
                },
                success: function (response) {
                    alert('Picture deleted successfully!');
                    $('#picture_path').attr('src', '/picture_path/default.jpg'); // Reset to default image
                    $('#contactModal').modal('hide');
                },
                error: function (xhr) {
                    alert(
                        `Failed to delete picture: ${xhr.responseJSON?.message || 'Unknown error'}`
                    );
                },
            });
        });

        // Handle History Modal
        $('#dataTable tbody').on('click', '.view-history-link', function (e) {
            e.preventDefault();

            const contactId = $(this).data('id');

            // Fetch contact history via AJAX
            $.ajax({
                url: `/contacts/history/${contactId}`, // API endpoint to fetch history
                method: 'GET',
                success: function (response) {
                    historyTable.clear();
                    
                    if (response.history && response.history.length > 0) {
                        response.history.forEach((item, index) => {
                            let deleteButton = '';
                            let formattedDate = '';
                            if (userRole == 7) {
                                deleteButton = `<button class="btn btn-danger btn-sm delete-history" data-id="${item.id}">Delete</button>`;
                            }
                            if (item.tanggal) {
                                const date = new Date(item.tanggal);
                                formattedDate = date.toLocaleDateString('id-ID', {
                                    day: '2-digit',
                                    month: '2-digit',
                                    year: 'numeric',
                                });
                            }
                            historyTable.row.add([
                                index + 1,
                                item.name,
                                item.skala,
                                formattedDate,
                                item.description,
                                item.utusan,
                                deleteButton,
                            ]);
                        });
                    } else {
                        historyTable.row.add(['', 'No history available', '', '', '', '',
                            ''
                        ]);
                    }

                    historyTable.draw();

                    // Set max date for input (7 days before today)
                    let today = new Date();
                    let sevenDaysAgo = new Date();
                    sevenDaysAgo.setDate(today.getDate() - 7);

                    let historyDateInput = document.getElementById('historyDate');

                    if (userRole !== 7) {
                        historyDateInput.max = today.toISOString().split('T')[0]; // Max is today
                        historyDateInput.min = sevenDaysAgo.toISOString().split('T')[0]; // Min is 7 days ago
                        historyDateInput.value = today.toISOString().split('T')[0];
                    }
                    // Populate the skala dropdown with values >= latest_skala
                    const skalaSelect = $('#skala');
                    skalaSelect.empty();
                    const latestSkala = response.latest_skala !== null ? response.latest_skala : -3;
                    for (let i = latestSkala; i <= 3; i++) {
                        skalaSelect.append(new Option(i, i));
                    }
                    // Show the history modal
                    $('#historyModal').modal('show');
                },
                error: function () {
                    alert('Failed to fetch contact history.');
                },
            });

            // Store contactId for later use
            $('#updateHistoryBtn').data('id', contactId);
        });

        $('#historyModal').on('click', '.delete-history', function (e) {
            e.preventDefault();
            
            const historyId = $(this).data('id');

            if (confirm('Are you sure you want to delete this history?')) {
                // Send delete request via AJAX if confirmed
                $.ajax({
                    url: `/contacts/history/delete/${historyId}`,
                    method: 'DELETE',
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('History record deleted successfully.');
                            window.location.reload();
                        } else {
                            alert('Failed to delete history record.');
                        }
                    },
                    error: function() {
                        alert('An error occurred while trying to delete the history record.');
                    }
                });
            } else {
                // If the user cancels the action, just log a message or do nothing
                console.log('Delete action was canceled.');
            }
        });

        // Handle Update History Button Click
        $('#updateHistoryBtn').on('click', function () {
            const contactId = $(this).data('id');
            const historyDate = $('#historyDate').val();
            const historySkala = $('#skala').val();
            const historyDescription = $('#historyDescription').val();

            if (!historyDate || !historyDescription) {
                alert('Please fill in all fields.');
                return;
            }

            // Send data to backend via AJAX
            $.ajax({
                url: `/contacts/history/update`,
                method: 'POST',
                contentType: 'application/json',
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: JSON.stringify({
                    contact_id: contactId,
                    tanggal: historyDate,
                    skala: historySkala,
                    description: historyDescription,
                }),
                success: function (response) {
                    alert('History updated successfully!');
                    $('#historyModal').modal('hide');
                    window.location.reload();
                },
                error: function () {
                    alert('Failed to update history.');
                },
            });
        });

        $('#addContactForm').on('submit', function (e) {
            e.preventDefault();

            const form = $(this);
            const url = form.attr('action');
            const formData = form.serialize();

            // Clear old errors
            form.find('.form-control').removeClass('is-invalid');
            form.find('.invalid-feedback').text('');

            $.ajax({
                type: 'POST',
                url: url,
                data: formData,
                success: function (response) {
                    alert('Contact added successfully!');
                    $('#addContactModal').modal('hide');
                    window.location.reload(); // Or reload table via AJAX
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(function (key) {
                            $(`[name="${key}"]`).addClass('is-invalid');
                            $(`#${key}Error`).text(errors[key][0]);
                        });
                    } else {
                        alert('Unexpected error. Please try again.');
                    }
                }
            });
        });


        //Apply filters
        $('#applyFilters').on('click', function () {
            const filters = {
                name: $('#filterName').val(),
                skala: $('#filterSkala').val(),
                utusan: $('#filterUtusan').val(),
                region: $('#filterRegion').val(),
                startDate: $('#startDate').val(),
                endDate: $('#endDate').val(),
            };

            loadTableData({
                name: $('#filterName').val(),
                skala: $('#filterSkala').val(),
                utusan: $('#filterUtusan').val(),
                region: $('#filterRegion').val(),
                startDate: $('#startDate').val(),
                endDate: $('#endDate').val(),
            });
        });

        // Reset filters
        $('#resetFilters').on('click', function () {
            $('#filterName, #filterSkala, #filterUtusan, #filterRegion, #startDate, #endDate').val('');
            loadTableData();
        });

        // Function to load filtered table data
        function loadTableData(filters = {}) {
            $.ajax({
                url: "{{ route('contactlist.index') }}",
                method: 'GET',
                data: filters,
                success: function (response) {
                    table.clear();
                    if (response.contacts && response.contacts.length > 0) {
                        response.contacts.forEach((contact, index) => {
                            let actions = '';
                            if (!([3, 4, 5].includes(userRole))) {
                                actions += `<a href="/data-contact/edit/${contact.id}" class="btn btn-primary btn-sm mr-2">Edit</a>`;
                            }
                            if (userRole == 7) {
                                actions += `<form action="/data-contact/delete/${contact.id}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this contact?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>`;
                            }
                            let historyLink = '';
                            if ((contact.status ?? 0) != 0 && !([3, 4, 5].includes(userRole))) {
                                historyLink = `<a href="#" class="view-history-link" 
                                    data-id="${contact.id}" 
                                    data-name="${contact.name}">${contact.skala}</a>`;
                            } else {
                                historyLink = contact.skala;
                            }
                            let detailsLink = '';
                            if (!([3, 4, 5].includes(userRole))) {
                                detailsLink = `<a href="#" class="view-details-link" 
                                    data-id="${contact.id}" 
                                    data-name="${contact.name}">${contact.name}</a>`;
                            } else {
                                detailsLink = contact.name;
                            }
                            table.row.add([
                                index + 1,
                                detailsLink,
                                historyLink,
                                contact.tanggal,
                                contact.description,
                                contact.utusan,
                                `${contact.kota}, ${contact.provinsi}`,
                                actions,
                            ]);
                        });
                    } else {
                        table.row.add(['', 'No data found', '', '', '', '', '', '']);
                        $('#skala').val(-3).change();
                    }

                    table.draw();
                },
                error: function () {
                    alert('Failed to fetch data.');
                },
            });
        }

        // // Initial data load
        // loadTableData();
    });
</script>
@endsection
