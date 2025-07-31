<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
        <div class="sidebar-brand-icon rotate-n-10">
            <i class="fas fa-church"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Parousia</div>
    </a>

    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="{{ url('/dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <!-- Missions -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMissions"
                aria-expanded="false" aria-controls="collapseMissions">
                <i class="fas fa-fw fa-globe"></i>
                <span>Missions</span>
            </a>
            <div id="collapseMissions" class="collapse" aria-labelledby="headingMissions"
                data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Missions:</h6>
                    <a class="collapse-item" href="{{ route('contactlist.index') }}">Data Contact
                        List</a>
                </div>
            </div>
        </li>

    @if($user->role_id == 7)
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAdmin"
                aria-expanded="false" aria-controls="collapseAdmin">
                <i class="fas fa-fw fa-user-shield"></i>
                <span>Admin Page</span>
            </a>
            <div id="collapseAdmin" class="collapse" aria-labelledby="headingAdmin" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Admin:</h6>
                    <a class="collapse-item" href="{{ route('manage-user.index') }}">Manage Users</a>
                    {{-- <a class="collapse-item" href="{{ route('create-sdm.index') }}">Create SDM</a> --}}
                    <a class="collapse-item" href="{{ route('show-sdm.index') }}">Manage SDM</a>
                    <a class="collapse-item" href="{{ route('target.index') }}">Manage Target</a>
                    <a class="collapse-item" href="{{ route('training.chapters.index') }}">Manage Chapters</a>
                </div>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/region-list') }}">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Region</span>
            </a>
        </li>
    @endif
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseReport"
                aria-expanded="false" aria-controls="collapseReport">
                <i class="fas fa-fw fa-user-shield"></i>
                <span>Report Page</span>
            </a>
            <div id="collapseReport" class="collapse" aria-labelledby="headingReport" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Report:</h6>
                    <a class="collapse-item" href="{{ route('report-contact-list.index') }}">Data
                        Contact Report</a>
                    <a class="collapse-item" href="{{ route('report-contact-skala.index') }}">Skala
                        Contact Report</a>
                    <a class="collapse-item" href="{{ route('report-contact-region.index') }}">Region
                        Contact Report</a>
                    <a class="collapse-item" href="{{ route('show-sdm.index') }}">View SDM</a>
                    @if($user->role_id == 7)
                    <a class="collapse-item" href="{{ route('report-total-sdm') }}">Jumlah SDM</a>
                    <a class="collapse-item" href="{{ route('report-user-training') }}">User Training Progress</a>
                    @endif
                </div>
            </div>
        </li>
        @if(!in_array($user->role_id, [1, 2, 6])) 
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCatatanSDM"
                aria-expanded="false" aria-controls="collapseCatatanSDM">
                <i class="fas fa-fw fa-calendar-alt"></i>
                <span>Catatan SDM</span>
            </a>
            <div id="collapseCatatanSDM" class="collapse" aria-labelledby="headingCatatanSDM" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Catatan SDM:</h6>
                    <a class="collapse-item" href="{{ route('catatan-sdm.index') }}">Catatan SDM</a>
                    <a class="collapse-item" href="{{ route('catatan-sdm.lists') }}">Catatan SDM Lists</a>
                </div>
            </div>
        </li>
        @endif

    <li class="nav-item">
        <a class="nav-link" href="{{ route('manage-group.index') }}">
            <i class="fas fa-fw fa-user-shield"></i>
            <span>Manage Group</span>
        </a>
    </li>

    <li class="nav-item">
        {{-- <a class="nav-link" href="{{ route('training.show', ['page' => 1]) }}">
            <i class="fas fa-fw fa-chart-area"></i>
            <span>Training</span>
        </a> --}}
        <a class="nav-link" href="{{ route('training.chapters') }}">
            <i class="fas fa-fw fa-chart-area"></i>
            <span>Training</span>
        </a>
    </li>
</ul>