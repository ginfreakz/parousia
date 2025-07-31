<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SB Admin Dashboard</title>

    <!-- SB Admin CSS -->
    <link href="{{ asset('admin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <style>
        .alert {
            position: fixed;
            top: 70px;
            right: 20px;
            width: 300px;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            opacity: 0.7;
        }
        .alert .close {
            position: absolute;
            top: 5px;
            right: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body id="page-top">

<!-- Page Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    @include('admin.partials.sidebar')

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <!-- Topbar -->
            @include('admin.partials.topbar')

            <!-- Main Content -->
            <div class="container-fluid">
                @yield('content')
            </div>

        </div>
    </div>

</div>
@if(session('success'))
    <div class="alert alert-success" role="alert">
        {{ session('success') }}
        <span class="close" onclick="this.parentElement.style.display='none';">&times;</span>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger" role="alert">
        {{ session('error') }}
        <span class="close" onclick="this.parentElement.style.display='none';">&times;</span>
    </div>
@endif
<!-- SB Admin JS -->
<script src="{{ asset('admin/vendor/jquery/jquery.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('admin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('admin/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ asset('admin/js/sb-admin-2.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>



<!-- Initialize DataTables -->
<script>
    $(document).ready(function() {
        // console.log("Initializing DataTable...");
        $('#dataTable').DataTable();

        // Initialize Select2 for Update Group Modals
        $('.modal').on('shown.bs.modal', function () {
            $(this).find('.select2-leader').select2({
                placeholder: "Select Leader",
                allowClear: true
            });

            $(this).find('.select2-members').select2({
                placeholder: "Select members",
                allowClear: true
            });
        });
        $('#lokasi_pelayanan').select2({
            placeholder: "Pilih lokasi",
            allowClear: true
        });
        $('#group_id').change(function () {
            var groupId = $(this).val();
            if (groupId) {
                $.ajax({
                    url: '/admin/getGroupMembers/' + groupId,
                    type: 'GET',
                    success: function (response) {
                        var membersHtml = '';
                        response.members.forEach(function (member) {
                            membersHtml += `
                                <div class="form-group">
                                    <label for="skala_${member.id}">Skala for ${member.name}</label>
                                    <select class="form-control" id="skala_${member.id}" name="skala[${member.id}]">
                                        @for ($i = -3; $i <= 3; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="historyDescription_${member.id}">Short Description for ${member.name}</label>
                                    <input type="text" id="historyDescription_${member.id}" name="description[${member.id}]" class="form-control" placeholder="Enter a short description">
                                </div>
                            `;
                        });
                        $('#groupMembers').html(membersHtml);
                    }
                });
            } else {
                $('#groupMembers').html('');
            }
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            let alert = document.querySelector('.alert');
            if (alert) {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            }
        }, 3000);
    });

    $('#resetFilters').on('click', function () {
        $('#filterLembaga, #filterRegion').val('0');
        $('#startDate, #endDate, #filterSkala').val('');
    });
</script>

</body>
</html>
