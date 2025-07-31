
@extends('admin.layout')

@section('content')

<h1 class="h3 mb-4 text-gray-800">Manage User</h1>
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Manage Users</h6>
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#roleListModal">
                    Role List
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->role ? $user->role->name : 'N/A' }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <button type="button" class="btn btn-primary btn-sm mr-2" data-toggle="modal" data-target="#updateModal{{ $user->id }}">
                                            Update
                                        </button>
                                        <form action="{{ route('admin.deleteUser', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Update Modal -->
                            <div class="modal fade" id="updateModal{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel{{ $user->id }}" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="updateModalLabel{{ $user->id }}">Update Role for {{ $user->name }}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('admin.updateUserRole', $user->id) }}" method="POST">
                                                @csrf
                                                <div class="form-group">
                                                    <label for="role_id">Role</label>
                                                    <select name="role_id" class="form-control">
                                                        @foreach($roles as $role)
                                                            <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <label for="Change Password">Change Password</label>
                                                    <input type="password" name="password" class="form-control" placeholder="New Password">
                                                    <label for="Change Password">Confirm Password</label>
                                                    <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password">
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

<!-- Role List Modal -->
<div class="modal fade" id="roleListModal" tabindex="-1" role="dialog" aria-labelledby="roleListModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleListModalLabel">Role List</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="list-group">
                    @foreach($roles as $role)
                        <li class="list-group-item">{{ $role->name }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection