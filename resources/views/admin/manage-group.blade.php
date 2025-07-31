@extends('admin.layout')

@section('content')

<h1 class="h3 mb-4 text-gray-800">Manage Group</h1>
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Manage Group</h6>
                <div class="d-flex">
                    @if(isset($userRole) && in_array($userRole, [1, 2, 6, 7]))
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                            data-target="#addGroupModal">
                            Add Group List
                        </button>
                        <a href="{{ route('massUpdateGroup') }}" class="btn btn-success btn-sm" style="margin-left: 10px">
                            Mass Update
                        </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Group Name</th>
                                <th>Group Leader</th>
                                <th>Members</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groups as $group)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $group->name }}</td>
                                    <td>{{ $group->leader }}</td>
                                    <td>{{ implode(', ', $group->member_names) }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if(isset($userRole) && $userRole == 7)
                                            <button type="button" class="btn btn-primary btn-sm mr-2"
                                                data-toggle="modal" data-target="#updateModal{{ $group->id }}">
                                                Update
                                            </button>
                                            <form
                                                action="{{ route('admin.deleteGroup', $group->id) }}"
                                                method="POST" style="display:inline;"
                                                onsubmit="return confirm('Are you sure you want to delete this group?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
                                            @elseif(isset($userRole) && $userRole == 2)
                                            <button type="button" class="btn btn-primary btn-sm mr-2"
                                                data-toggle="modal" data-target="#updateModal{{ $group->id }}">
                                                Update
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                <!-- Update Modal -->
                                <div class="modal fade" id="updateModal{{ $group->id }}" role="dialog"
                                    aria-labelledby="updateModalLabel{{ $group->id }}" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="updateModalLabel{{ $group->id }}">Update
                                                    Group for {{ $group->name }}</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form
                                                    action="{{ route('admin.updateGroup', $group->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <div class="form-group">
                                                        <label for="update_name">Group Name</label>
                                                        <input type="text" name="name" id="update_name"
                                                            class="form-control" value="{{ $group->name }}" readonly>
                                                        <input type="hidden" name="update_group_id"
                                                            value="{{ $group->id }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="update_leader_id">Group Leader</label>
                                                        <select name="leader_id" class="form-control select2-leader">
                                                            <option value="" selected>Select Leader</option>
                                                            @foreach($leaders as $leader)
                                                                <option
                                                                    value="{{ $leader->id }} - {{ $leader->name }}"
                                                                    {{ $group->leader_id == $leader->id ? 'selected' : '' }}>
                                                                    {{ $leader->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="update_members">Members</label>
                                                        <select name="members[]" class="form-control select2-members"
                                                            multiple="multiple" data-placeholder="Select members"
                                                            style="width: 100%;">
                                                            @foreach($contacts as $contact)
                                                                <option value="{{ $contact->id }}"
                                                                    {{ in_array($contact->id, json_decode($group->members, true)) ? 'selected' : '' }}>
                                                                    {{ $contact->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End of Update Modal -->

                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Group Modal -->
<div class="modal fade" id="addGroupModal" role="dialog" aria-labelledby="addGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addGroupModalLabel">Add Group</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.storeGroup') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="name">Group Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="">
                    </div>
                    <div class="form-group">
                        <label for="leader">Group Leader</label>
                        <select name="leader_id" id="leader_id" class="form-control select2-leader">
                            <option value="" selected>Select Leader</option>
                            @foreach($leaders as $leader)
                                <option value="{{ $leader->id }} - {{ $leader->name }}">
                                    {{ $leader->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="members">Members</label>
                        <select name="members[]" id="members" class="form-control select2-members" multiple="multiple"
                            data-placeholder="Select members" style="width: 100%;">
                            @foreach($contacts as $contact)
                                <option value="{{ $contact->id }}">{{ $contact->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Add</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Mass Update Modal -->
<div class="modal fade" id="massUpdateModal" role="dialog" aria-labelledby="massUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="massUpdateModalLabel">Mass Update</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="massUpdateForm" action="{{ route('massUpdateHistory') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="group_id">Select Group</label>
                        <select name="group_id" id="group_id" class="form-control" required>
                            <option value="" selected>Select Group</option>
                            @foreach($groups as $group)
                                @if($group->leader_id == auth()->user()->id)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tanggal">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="skala">Skala</label>
                        <input type="number" name="skala" class="form-control" min="-3" max="3" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
