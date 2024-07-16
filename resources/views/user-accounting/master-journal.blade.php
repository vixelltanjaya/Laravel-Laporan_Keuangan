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
                            <h5 class="mb-0">Jurnal Master</h5>
                        </div>
                        <div class="nav-item d-flex align-self-end">
                            <a href="{{ route('add-master-journal.index') }}" class="btn bg-gradient-primary mb-0 me-2">+&nbsp;Add</a>
                        </div>
                    </div>
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

                @if (session('gagal'))
                <div class="alert alert-danger">
                    <ul>
                        <li>{{ session('gagal') }}</li>
                    </ul>
                </div>
                @endif

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0" id="masterJournalTable">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Kode</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($masterTransaction as $transactionMaster)
                                <tr>
                                    <td class="text-center">
                                        <p>{{ $loop->iteration }}</p>
                                    </td>
                                    <td>
                                        <p>{{ $transactionMaster->code }}</p>
                                    </td>
                                    <td>
                                        <p>{{ $transactionMaster->description }}</p>
                                    </td>
                                    <td>
                                        <p>
                                            <a href="{{ route('edit-master-journal.index', ['code' => $transactionMaster->code]) }}" class="btn btn-link text-primary font-weight-bold text-small">
                                                <i class="fas fa-user-edit"></i> Edit
                                            </a>
                                            <a href="{{ route('view-master-journal.index', ['code' => $transactionMaster->code]) }}" class="btn btn-link text-primary font-weight-bold text-small">
                                                <i class="fa fa-eye" aria-hidden="true"></i> Lihat
                                            </a>
                                            <button class="btn btn-link text-primary font-weight-bold text-small" data-bs-toggle="modal" data-bs-target="#deleteMasterJournalModal" data-id="{{ $transactionMaster->id }}" data-code="{{ $transactionMaster->code }}">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </p>
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

<!-- Modal Hapus -->
<div class="modal fade" id="deleteMasterJournalModal" tabindex="-1" role="dialog" aria-labelledby="deleteMasterJournalModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteMasterJournalModalLabel">Hapus Master Journal</h5>
            </div>
            <form id="deleteMasterJournalForm" method="POST">
                @method('DELETE')
                @csrf
                <div class="modal-body">
                    <p id="deleteMasterJournalMessage">Apakah Anda yakin ingin menghapus data ini?</p>
                    <input type="hidden" id="delete_master_journal" name="id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger" id="confirmDeleteMasterJournal">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#masterJournalTable').DataTable();

        // Open Delete Modal and set form action
        $('#deleteMasterJournalModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var code = button.data('code');

            console.log("Apakah code masuk sini ?" + code);

            var modal = $(this);
            var message = 'Apakah Anda yakin ingin menghapus data ' + code + ' ?';
            modal.find('.modal-body #deleteMasterJournalMessage').text(message);
            modal.find('.modal-body #delete_master_journal').val(id);
            $('#deleteMasterJournalForm').attr('action', '/master-journal/' + id);
        });

        // Handle form submission
        $('#deleteMasterJournalForm').on('submit', function() {
            $(this).find('button[type="submit"]').prop('disabled', true);
        });
    });
</script>

@endsection