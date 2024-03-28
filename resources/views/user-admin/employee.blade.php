@extends('layouts.user_type.auth')

@section('content')

<div>
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-4">
                <div class="card-header pb-0">

                    <div class="d-flex flex-row justify-content-between">
                        <div>
                            <h5 class="mb-0">Employee</h5>
                        </div>

                        <div class="nav-item d-flex align-self-end">
                            <a href="#" class="btn bg-gradient-primary mb-0 me-2" type="button" data-bs-toggle="modal" data-bs-target="#modalAddEmployee">+&nbsp;Employee</a>
                            <a href="/exportexcel" target="_blank" class="btn btn-primary active mb-0 text-white me-2 " role="button" aria-pressed="true">
                            <i class="fas fa-download me-1"></i>Download</a>
                            <a href="/importexcel" target="_blank" class="btn btn-primary active mb-0 text-white" role="button" aria-pressed="true">Import Data</a>
                        </div>
                    </div>
                </div>

                <!-- search bar -->
                <div class="col-md-7 ms-md-3 pe-md-3 align-items-center">
                    <form action="{{url ('employee')}}" class="form-inline" method="GET" value="{{request('search')}}">
                        <div class="input-group mb-3">
                            <input class="form-control mr-sm-2" type="search" name="search" placeholder="Search" aria-label="Search">
                            <button class="btn btn-primary active mb-0 text-white my-2 my-sm-0" type="submit">Search</button>
                        </div>
                    </form>
                </div>

                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif


                @if (session('berhasil'))
                <div class="alert alert-success">
                    <ul>
                        <li>{{ session('berhasil') }}</li>
                    </ul>
                </div>
                @endif


                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        NO
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Name
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Email
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Role
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Creation Date
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employees as $employee)
                                <tr>
                                    <td class="ps-4">
                                        <p class="text-xs font-weight-bold mb-0">{{ $loop->iteration }}</p>
                                    </td>
                                    <td class="text-center">
                                        <p class="text-xs font-weight-bold mb-0">{{ $employee->username }}</p>
                                    </td>
                                    <td class="text-center">
                                        <p class="text-xs font-weight-bold mb-0">{{ $employee->email }}</p>
                                    </td>
                                    <td class="text-center">
                                        <p class="text-xs font-weight-bold mb-0">{{ $employee->role }}</p>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-secondary text-xs font-weight-bold">{{ $employee->created_at->format('d/m/Y') }}</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="#" class="mx-3" data-bs-toggle="modal" data-bs-target="#editUserModal{{$employee->id}}" data-bs-original-title="Edit user">
                                            <i class="fas fa-user-edit text-secondary"></i>
                                        </a>
                                        <span data-bs-toggle="modal" data-bs-target="#deleteUserModal{{$employee->id}}">
                                            <i class="cursor-pointer fas fa-trash text-secondary"></i>
                                        </span>
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
</div>

<div class="align text-center">
    {{$employees->links()}}
</div>

<!-- Add Modal -->
<div class="modal fade" id="modalAddEmployee" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('employee') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="username" class="form-label">Nama</label>
                        <input type="text" class="form-control" name="username" id="username" value="{{ auth()->user()->name }}">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" class="form-control" id="email" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <input type="text" class="form-control" id="role" name="role">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">+&nbsp; Add</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal for Editing User -->
@foreach ($employees as $employee)
<div class="modal fade" id="editUserModal{{$employee->id}}" tabindex="-1" aria-labelledby="editUserModalLabel{{$employee->id}}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel{{$employee->id}}">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ url('employee/'.$employee->id)}}" method="post">
                    @method('PUT')
                    @csrf
                    <div class="mb-3">
                        <label for="username" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="username" name="username" value="{{ old('username', $employee->username) }}">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" class="form-control" id="email" name="email" value="{{ old('email', $employee->email) }}" readonly="true">
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <input type="text" class="form-control" id="role" name="role" value="{{ old('role', $employee->role) }}">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach



<!-- Modal for Deleting User -->
@foreach ($employees as $employee)
<div class="modal fade" id="deleteUserModal{{$employee->id}}" tabindex="-1" aria-labelledby="deleteUserModalLabel{{$employee->id}}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserModalLabel{{$employee->id}}">Delete User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ url('employee/'.$employee->id)}}" method="post">
                    @method('DELETE')
                    @csrf
                    <div class="mb-3">
                        <p> Apakah anda yakin akan menghapus data {{$employee->username}}?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                        <button type="submit" class="btn btn-danger">Yes, delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach


<!-- Bootstrap JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

@endsection