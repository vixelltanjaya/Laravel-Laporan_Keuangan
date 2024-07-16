@extends('layouts.user_type.auth')

@section('content')

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-7 mt-4">
            <div class="card">
                <div class="card-header pb-0 px-3">
                    <h6 class="mb-0">Reporting</h6>
                </div>
                <div class="card-body pt-4 p-3">
                    <ul class="list-group">
                        <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
                            <div class="d-flex flex-column">
                                <h6 class="mb-3 text-sm">Laba/Rugi</h6>
                            </div>
                            <div class="ms-auto text-end">
                                <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;" data-bs-toggle="modal" data-bs-target="#incomeStatementModal"><i class="far fa-file text-dark me-2" aria-hidden="true"></i>Tampilkan</a>
                            </div>
                        </li>
                        <li class="list-group-item border-0 d-flex p-4 mb-2 mt-3 bg-gray-100 border-radius-lg">
                            <div class="d-flex flex-column">
                                <h6 class="mb-3 text-sm">Neraca</h6>
                            </div>
                            <div class="ms-auto text-end">
                                <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;" data-bs-toggle="modal" data-bs-target="#balanceSheetModal"><i class="far fa-file text-dark me-2" aria-hidden="true"></i>Tampilkan</a>
                            </div>
                        </li>
                        <li class="list-group-item border-0 d-flex p-4 mb-2 mt-3 bg-gray-100 border-radius-lg">
                            <div class="d-flex flex-column">
                                <h6 class="mb-3 text-sm">Arus Kas</h6>
                            </div>
                            <div class="ms-auto text-end">
                                <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;" data-bs-toggle="modal" data-bs-target="#cashFlowModal"><i class="far fa-file text-dark me-2" aria-hidden="true"></i>Tampilkan</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

<!-- Laba/Rugi Modal -->
<div id="incomeStatementModal" class="modal fade" tabindex="-1" aria-labelledby="incomeStatementModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="incomeStatementModalLabel">Add Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="incomeStatementForm" action="{{ url('reporting') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="transaction_date">Tanggal Transaksi Awal <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="transaction_date_start">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="transaction_date">Tanggal Transaksi Akhir <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="transaction_date_end">
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="transaction_type">Tipe Bisnis <span class="text-danger">*</span></label>
                            <select name="transaction_type" id="transaction_type" class="form-control" required>
                                <option value="" disabled selected>Pilih Tipe Bisnis</option>
                                <option value="all">All</option>
                                @foreach ($division as $divisi)
                                <option value="{{ $divisi->id }}">{{ $divisi->description }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Cari</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Neraca Modal -->
<div id="balanceSheetModal" class="modal fade" tabindex="-1" aria-labelledby="balanceSheetModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="balanceSheetModalLabel">Add Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="balanceSheetForm" action="{{ url('reporting') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="transaction_date">Tanggal Transaksi Awal <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="transaction_date_start">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="transaction_date">Tanggal Transaksi Akhir <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="transaction_date_end">
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="transaction_type">Tipe Bisnis <span class="text-danger">*</span></label>
                            <select name="transaction_type" id="transaction_type" class="form-control" required>
                                <option value="" disabled selected>Pilih Tipe Bisnis</option>
                                <option value="all">All</option>
                                @foreach ($division as $divisi)
                                <option value="{{ $divisi->id }}">{{ $divisi->description }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Cari</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- arus kas Modal -->
<div id="cashFlowModal" class="modal fade" tabindex="-1" aria-labelledby="cashFlowModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cashFlowModalLabel">Add Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="cashFlowForm" action="{{ url('reporting') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="transaction_date">Tanggal Transaksi Awal <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="transaction_date_start">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="transaction_date">Tanggal Transaksi Akhir <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="transaction_date_end">
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="transaction_type">Tipe Bisnis <span class="text-danger">*</span></label>
                            <select name="transaction_type" id="transaction_type" class="form-control" required>
                                <option value="" disabled selected>Pilih Tipe Bisnis</option>
                                <option value="all">All</option>
                                @foreach ($division as $divisi)
                                <option value="{{ $divisi->id }}">{{ $divisi->description }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Cari</button>
                </div>
            </form>
        </div>
    </div>
</div>



@push('js')
<script>
    $(document).ready(function() {
        $('#financialReports').click(function() {
            $('#incomeStatementForm')[0].reset();
            $('#incomeStatementModal').modal('show');
            $('#balanceSheetForm')[0].reset();
            $('#balanceSheetModal').modal('show');
            $('#cashFlowForm')[0].reset();
            $('#cashFlowModal').modal('show');
        });
    });
</script>
@endpush