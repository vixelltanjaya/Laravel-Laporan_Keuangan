@extends('layouts.user_type.auth')

@section('content')

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="card mb-4 w-100">
                <div class="card-header pb-0">
                    <div class="d-flex flex-row justify-content-between">
                        <div>
                            <h5 class="mb-0">Customer</h5>
                        </div>
                        <div class="nav-item d-flex align-self-end">
                            <button class="btn bg-gradient-primary mb-0 me-2" data-bs-toggle="modal" data-bs-target="#addCustomerModal">+&nbsp;Customer</button>
                            <a href="/exportexcel" target="_blank" class="btn btn-dark active mb-0 text-white me-2" role="button" aria-pressed="true">
                                <i class="fas fa-download me-1"></i>Unduh</a>
                        </div>
                    </div>
                </div>

                @include('components.alert-danger-success')

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0" id="customerTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>name</th>
                                    <th>No Telp</th>
                                    <th>Alamat</th>
                                    <th>Email</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($customers as $index => $customer)
                                <tr>
                                    <td>
                                        <p class="text-center">
                                            {{ $index + 1 }}
                                        </p>
                                    </td>
                                    <td>
                                        <p>
                                            {{ $customer->name }}
                                        </p>
                                    </td>
                                    <td>
                                        <p>{{ $customer->no_telp }}</p>
                                    </td>
                                    <td>
                                        <p>{{ $customer->alamat }}</p>
                                    </td>
                                    <td>
                                        <p>{{ $customer->email }}</p>
                                    </td>
                                    <td class="ps-4">
                                        <a class="btn btn-link text-secondary font-weight-bold" data-id="{{ $customer->id }}" data-name="{{ $customer->name }}" data-no_telp="{{ $customer->no_telp }}" data-alamat="{{ $customer->alamat }}" data-email="{{ $customer->email }}" data-bs-toggle="modal" data-bs-target="#editCustomerModal">
                                            <i class="ri-edit-line text-secondary font-weight-bold text-small"></i> Edit
                                        </a>
                                        <button class="btn btn-link text-danger font-weight-bold text-large" data-id="{{ $customer->id }}" data-name="{{ $customer->name }}" data-bs-toggle="modal" data-bs-target="#deleteCustomerModal">
                                            <i class="ri-delete-bin-line"></i> Hapus
                                        </button>
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
</main>

<!-- Modal Tambah Customer -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCustomerModalLabel">Tambah Customer</h5>
            </div>
            <div class="modal-body">
                <form id="addCustomerForm" action="{{ route('customer.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="no_telp">No Telp</label>
                        <input type="text" class="form-control" id="no_telp" name="no_telp" required>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <input type="text" class="form-control" id="alamat" name="alamat" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Customer -->
<div class="modal" id="editCustomerModal" tabindex="-1" role="dialog" aria-labelledby="editCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCustomerModalLabel">Edit Customer</h5>
            </div>
            <div class="modal-body">
                <form id="editCustomerForm" method="POST" action="{{ route('customer.update', ['customer' => ':id']) }}">
                    @method('PATCH')
                    @csrf
                    <input type="hidden" id="edit_customer_id" name="id">
                    <div class="form-group">
                        <label for="edit_name">name</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_no_telp">No Telp</label>
                        <input type="text" class="form-control" id="edit_no_telp" name="no_telp" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_alamat">Alamat</label>
                        <input type="text" class="form-control" id="edit_alamat" name="alamat" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_email">Email</label>
                        <input type="text" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hapus Customer -->
<div class="modal" id="deleteCustomerModal" tabindex="-1" role="dialog" aria-labelledby="deleteCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCustomerModalLabel">Hapus Customer</h5>
            </div>
            <form id="deleteCustomerForm" method="POST">
                @method('DELETE')
                @csrf
                <div class="modal-body">
                    <p id="deleteCustomerMessage">Apakah Anda yakin ingin menghapus data?</p>
                    <input type="hidden" id="delete_customer_id" name="id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger" id="confirmDeleteCustomer">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#customerTable').DataTable();

        // Open Edit Customer Modal and populate data
        $('#editCustomerModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var name = button.data('name');
            var no_telp = button.data('no_telp');
            var alamat = button.data('alamat');
            var email = button.data('email');

            console.log("apa name ? " + name);

            var modal = $(this);
            modal.find('.modal-body #edit_customer_id').val(id);
            modal.find('.modal-body #edit_name').val(name);
            modal.find('.modal-body #edit_no_telp').val(no_telp);
            modal.find('.modal-body #edit_alamat').val(alamat);
            modal.find('.modal-body #edit_email').val(email);

            var form = modal.find('form');
            var action = form.attr('action').replace(':id', id);
            form.attr('action', action);
            console.log("Form action URL: " + form.attr('action'));
            console.log("ID Akun di modal: " + modal.find('.modal-body #edit_customer_id').val());
        });

        // Open Delete Customer Modal and set form action
        $('#deleteCustomerModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var name = button.data('name');

            var modal = $(this);
            var message = 'Apakah Anda yakin ingin menghapus data customer dengan nama ' + name + '?';
            modal.find('.modal-body #deleteCustomerMessage').text(message);
            modal.find('.modal-body #delete_customer_id').val(id);
            $('#deleteCustomerForm').attr('action', '/customer/' + id); //action delete lead to func destroy
        });

        // Handle form submission
        $('#deleteCustomerForm').on('submit', function() {
            $(this).find('button[type="submit"]').prop('disabled', true);
        });
    });
</script>

@endsection