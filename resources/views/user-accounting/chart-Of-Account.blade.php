@extends('layouts.user_type.auth')

@section('content')

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>

<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="card mb-4 w-100">
                <div class="card-header pb-0">
                    <div class="d-flex flex-row justify-content-between">
                        <div>
                            <h5 class="mb-0">Master Akun</h5>
                        </div>
                        <div class="nav-item d-flex align-self-end">
                            <a href="#" class="btn bg-gradient-primary mb-0 me-2" data-bs-toggle="modal" data-bs-target="#addAccountModal">+&nbsp;Akun</a>
                            <a href="/exportMasterAccountToExcel" target="_blank" class="btn btn-dark active mb-0 text-white me-2" role="button" aria-pressed="true">
                                <i class="fas fa-download me-1"></i>Export</a>
                            <a href="/" target="_blank" class="btn btn-default active mb-0 text-black me-2" role="button" aria-pressed="true" data-bs-toggle="modal" data-bs-target="#importAccountModal">
                                <i class="fas fa-file-import me-1"></i>Import</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @include('components.alert-danger-success');
                    <div class="table-responsive">
                        <table class="hover compact stripe" style="width:100%" id="accountTable">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Kode Akuntansi</th>
                                    <th>Nama Akun</th>
                                    <th>Posisi Akun</th>
                                    <th>Tipe Akun</th>
                                    <th>Akun Grup</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($chartOfAccounts as $coa)
                                <tr>
                                    <td class="text-center">{{ $loop -> iteration }}</td>
                                    <td>
                                        <p class="text-center">
                                            {{ $coa->account_id }}
                                        </p>
                                    </td>
                                    <td>
                                        <p>{{ $coa->account_name }}</p>
                                    </td>
                                    <td>
                                        <p>{{ $coa->account_sign }}</p>
                                    </td>
                                    <td>
                                        <p>{{ $coa->account_type }}</p>
                                    </td>
                                    <td>
                                        <p>{{ $coa->account_group }}</p>
                                    </td>
                                    <td class="d-flex justify-content-between">
                                        <button class="btn btn-link text-secondary" data-id="{{$coa->id}}" data-account_id="{{ $coa->account_id }}" data-account_name="{{ $coa->account_name }}" data-account_sign="{{ $coa->account_sign }}" data-account_type="{{ $coa->account_type }}" data-account_group="{{ $coa->account_group }}" data-bs-toggle="modal" data-bs-target="#editAccountModal">
                                            <i class="ri-edit-line"></i> Edit
                                        </button>
                                        <button class="btn btn-link text-danger" data-id="{{ $coa->id }}" data-account_id="{{ $coa->account_id }}" data-account_name="{{ $coa->account_name }}" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
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


<!-- Add Account Modal -->
<div class="modal fade" id="addAccountModal" tabindex="-1" role="dialog" aria-labelledby="addAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAccountModalLabel">Tambah Akun</h5>
            </div>
            <div class="modal-body">
                <form id="addAccountModal" action="{{ route('chart-of-account.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="account_id" class="form-label">Kode Akuntansi</label>
                        <input type="text" class="form-control" id="account_id" name="account_id">
                    </div>
                    <div class="mb-3">
                        <label for="account_name" class="form-label">Nama Akun</label>
                        <input type="text" class="form-control" id="account_name" name="account_name">
                    </div>
                    <div class="mb-3">
                        <label for="account_sign" class="form-label">Posisi Akun</label>
                        <select class="form-select" id="account_sign" name="account_sign">
                            <option value="debit">Debit</option>
                            <option value="kredit">Kredit</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_account_type">Tipe Akun</label>
                        <select class="form-control" id="edit_account_type" name="account_type" required>
                            <option value="">Pilih Tipe Akun</option>
                            <option value="Aset">Aset</option>
                            <option value="Kewajiban">Kewajiban</option>
                            <option value="Pendapatan">Pendapatan</option>
                            <option value="Beban">Beban/Biaya</option>
                            <option value="Ekuitas">Ekuitas</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="account_group" class="form-label">Grup Akun</label>
                        <input type="text" class="form-control" id="account_group" name="account_group">
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Account Modal -->
<div class="modal" id="editAccountModal" tabindex="-1" role="dialog" aria-labelledby="editAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAccountModalLabel">Edit Account</h5>
            </div>
            <div class="modal-body">
                <form id="editAccountForm" method="POST" action="{{ route('chart-of-account.update', ['chart_of_account' => ':id']) }}">
                    @method('PATCH')
                    @csrf
                    <input type="hidden" id="edit_account_code" name="id">
                    <div class="form-group">
                        <label for="edit_account_id">Kode Akuntansi</label>
                        <input type="text" class="form-control" id="edit_account_id" name="account_id">
                    </div>
                    <div class="form-group">
                        <label for="edit_account_name">Nama Akun</label>
                        <input type="text" class="form-control" id="edit_account_name" name="account_name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_account_sign">Posisi Akun</label>
                        <select class="form-control" id="edit_account_sign" name="account_sign" required>
                            <option value="debit">Debit</option>
                            <option value="kredit">Kredit</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_account_type">Tipe Akun</label>
                        <select class="form-control" id="edit_account_type" name="account_type" required>
                            <option value="">Pilih Tipe Akun</option>
                            <option value="Aset">Aset</option>
                            <option value="Kewajiban">Kewajiban</option>
                            <option value="Pendapatan">Pendapatan</option>
                            <option value="Beban">Beban/Biaya</option>
                            <option value="Ekuitas">Ekuitas</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_account_group">Akun Grup</label>
                        <input type="text" class="form-control" id="edit_account_group" name="account_group">
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal" id="deleteAccountModal" tabindex="-1" role="dialog" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteAccountModalLabel">Hapus Akun</h5>
            </div>
            <form id="deleteAccountForm" method="POST" action="{{route('chart-of-account.destroy', ['chart_of_account' => ':id'])}}">
                @method('DELETE')
                @csrf
                <div class="modal-body">
                    <p id="deleteAccountMessage">Apakah Anda yakin ingin menghapus data?</p>
                    <input type="hidden" id="delete_account_id" name="id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger" id="confirmDeleteAccount">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Import akun modal -->
<div class="modal" id="importAccountModal" tabindex="-1" role="dialog" aria-labelledby="importAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importAccountModalLabel">Import Akun</h5>
            </div>
            <form id="importAccountForm" method="POST" action="{{route('import-account')}}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="import_account">Upload File</label>
                        <input type="file" class="form-control-file" id="import_account" name="import_account" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
    $(document).ready(function() {
        $('#accountTable').DataTable();

        $('#editAccountModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var account_id = button.data('account_id');
            var account_name = button.data('account_name');
            var account_sign = button.data('account_sign');
            var account_type = button.data('account_type');
            var account_group = button.data('account_group');

            var modal = $(this);
            modal.find('.modal-body #edit_account_code').val(id);
            modal.find('.modal-body #edit_account_id').val(account_id);
            modal.find('.modal-body #edit_account_name').val(account_name);
            modal.find('.modal-body #edit_account_type').val(account_type);
            modal.find('.modal-body #edit_account_sign').val(account_sign);
            modal.find('.modal-body #edit_account_group').val(account_group);

            var form = modal.find('form');
            var action = form.attr('action').replace(':id', id);
            form.attr('action', action);

            console.log("Form action URL: " + form.attr('action'));
            console.log("ID Akun di modal: " + modal.find('.modal-body #edit_account_id').val());
        });

        // Open Delete Modal and set form action
        $('#deleteAccountModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var account_id = button.data('account_id');
            var account_name = button.data('account_name');

            console.log('account id nya apa? ' + account_id);

            var modal = $(this);
            var message = 'Apakah Anda yakin ingin menghapus nomor akun ' + account_id + ' - ' + account_name + ' ? Hal ini dapat berefek pada balance perusahaan';
            modal.find('.modal-body #deleteAccountMessage').text(message);
            modal.find('.modal-body #delete_account_id').val(account_id);
            $('#deleteAccountForm').attr('action', '/chart-of-account/' + id);
        });

        // Handle form submission
        $('#deleteAccountForm').on('submit', function() {
            $(this).find('button[type="submit"]').prop('disabled', true);
        });
    });
</script>

@endsection