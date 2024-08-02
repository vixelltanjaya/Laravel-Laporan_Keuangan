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
                            <h5 class="mb-0">Evidence Code</h5>
                        </div>
                        <div class="nav-item d-flex align-self-end">
                            <a href="{{ url('add-evidence-code') }}" class="btn bg-gradient-primary mb-0 me-2">+&nbsp;Add</a>
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

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="hover" id="evidenceCodeTable">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Kode</th>
                                    <th>Judul Kode</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ( $evidenceCode as $evidence )
                                <tr>
                                    <td class="text-center">
                                        <p> {{$loop->iteration}} </p>
                                    </td>
                                    <td>
                                        <p> {{$evidence->prefix_code}} </p>
                                    </td>
                                    <td>
                                        <p> {{$evidence->code_title}} </p>
                                    </td>
                                    <td class="ps-4">
                                        <button class="btn btn-link text-secondary font-weight-bold text-small" data-id="{{ $evidence->id }}" data-prefix_code="{{ $evidence->prefix_code }}" data-code_title="{{ $evidence->code_title }}" data-bs-toggle="modal" data-bs-target="#editEvidenceCodeModal">
                                            <i class="ri-edit-line">Edit</i>
                                        </button>
                                        <button class="btn btn-link text-danger" data-id="{{ $evidence->id }}" data-prefix_code="{{ $evidence->prefix_code }}" data-bs-toggle="modal" data-bs-target="#deleteEvidenceCodeModal">
                                            <i class="ri-delete-bin-line">Hapus</i>
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


<!-- Modal Edit  -->
@foreach ( $evidenceCode as $index => $evidence )
<div class="modal" id="editEvidenceCodeModal" tabindex="-1" role="dialog" aria-labelledby="editEvidenceCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editEvidenceCodeModalLabel">Edit Evidence</h5>
            </div>
            <div class="modal-body">
                <form id="editEvidenceCodeForm" method="POST" action="{{ route('evidence-code.update', ['evidence_code' => ':id']) }}">
                    @method('PATCH')
                    @csrf
                    <input type="hidden" id="edit_evidence_id" name="id">
                    <div class="form-group">
                        <label for="edit_prefix_code">Prefiks Kode</label>
                        <input type="text" class="form-control" id="edit_prefix_code" name="prefix_code" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_code_title">Judul Kode</label>
                        <input type="text" class="form-control" id="edit_code_title" name="code_title" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Modal Hapus -->
<div class="modal fade" id="deleteEvidenceCodeModal" tabindex="-1" role="dialog" aria-labelledby="deleteEvidenceCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteEvidenceCodeModalLabel">Hapus Evidence</h5>
            </div>
            <form id="deleteEvidenceCodeForm" method="POST">
                @method('DELETE')
                @csrf
                <div class="modal-body">
                    <p id="deleteEvidenceCodeMessage"></p>
                    <input type="hidden" id="delete_evidence_code_id" name="id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger" id="confirmDeleteEvidenceCode">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#evidenceCodeTable').DataTable();

        // Open Edit evidence Modal and populate data
        $('#editEvidenceCodeModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var prefix_code = button.data('prefix_code');
            var code_title = button.data('code_title');

            console.log("Apa nama prefix_code? " + prefix_code);

            var modal = $(this);
            modal.find('.modal-body #edit_evidence_id').val(id);
            modal.find('.modal-body #edit_prefix_code').val(prefix_code);
            modal.find('.modal-body #edit_code_title').val(code_title);

            var form = modal.find('form');
            var action = form.attr('action').replace(':id', id);
            form.attr('action', action);
            console.log("Form action URL: " + form.attr('action'));
            console.log("ID Akun di modal: " + modal.find('.modal-body #edit_evidence_id').val());

        });

        // Open Delete evidence Modal and set form action
        $('#deleteEvidenceCodeModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var prefix_code = button.data('prefix_code');

            console.log("Apakah id masuk sini ?" + prefix_code);

            var modal = $(this);
            var message = 'Apakah Anda yakin ingin menghapus data ' + prefix_code + ' ?';
            modal.find('.modal-body #deleteEvidenceCodeMessage').text(message);
            modal.find('.modal-body #delete_evidence_code_id').val(id);
            $('#deleteEvidenceCodeForm').attr('action', '/evidence-code/' + id);
        });

        // Handle form submission
        $('#deleteEvidenceCodeForm').on('submit', function() {
            $(this).find('button[type="submit"]').prop('disabled', true);
        });
    });
</script>

@endsection