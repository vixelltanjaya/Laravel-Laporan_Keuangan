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
                                <a class="btn btn-link text-dark px-3 mb-0" data-bs-toggle="modal" data-bs-target="#incomeStatementModal"><i class="far fa-file text-dark me-2" aria-hidden="true"></i>Tampilkan</a>
                            </div>
                        </li>
                        <li class="list-group-item border-0 d-flex p-4 mb-2 mt-3 bg-gray-100 border-radius-lg">
                            <div class="d-flex flex-column">
                                <h6 class="mb-3 text-sm">Neraca</h6>
                            </div>
                            <div class="ms-auto text-end">
                                <a class="btn btn-link text-dark px-3 mb-0" data-bs-toggle="modal" data-bs-target="#balanceSheetModal"><i class="far fa-file text-dark me-2" aria-hidden="true"></i>Tampilkan</a>
                            </div>
                        </li>
                        <li class="list-group-item border-0 d-flex p-4 mb-2 mt-3 bg-gray-100 border-radius-lg">
                            <div class="d-flex flex-column">
                                <h6 class="mb-3 text-sm">Arus Kas</h6>
                            </div>
                            <div class="ms-auto text-end">
                                <a class="btn btn-link text-dark px-3 mb-0" data-bs-toggle="modal" data-bs-target="#cashFlowModal"><i class="far fa-file text-dark me-2" aria-hidden="true"></i>Tampilkan</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="dynamicModal" class="modal fade" tabindex="-1" aria-labelledby="dynamicModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dynamicModalLabel">Add Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="dynamicForm" method="POST">
                @csrf
                <div class="modal-body" id="modalBody">
                    <!-- Konten modal akan dimuat di sini -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Cari</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('.btn-link').click(function() {
            const target = $(this).data('bs-target');
            let title, formContent, actionUrl;

            switch (target) {
                case '#incomeStatementModal':
                    title = 'Laba/Rugi';
                    actionUrl = "{{ route('generate-financial-statement.income') }}";
                    formContent = `
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="transaction_month_start">Transaksi Awal <span class="text-danger">*</span></label>
                                    <input type="month" class="form-control" name="transaction_month_start" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="transaction_month_end">Transaksi Akhir <span class="text-danger">*</span></label>
                                    <input type="month" class="form-control" name="transaction_month_end" required>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="division_id">Tipe Bisnis <span class="text-danger">*</span></label>
                                <select name="division_id" id="division_id" class="form-control" required>
                                    <option value="" disabled selected>Pilih Tipe Bisnis</option>
                                    <option value="all">All</option>
                                    @foreach ($division as $divisi)
                                    <option value="{{ $divisi->id }}">{{ $divisi->description }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    `;
                    break;
                case '#balanceSheetModal':
                    title = 'Neraca';
                    actionUrl = "{{ route('generate-financial-statement.balance') }}";
                    formContent = `
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="transaction_month_start">Transaksi Awal <span class="text-danger">*</span></label>
                                    <input type="month" class="form-control" name="transaction_month_start" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="transaction_month_end">Transaksi Akhir <span class="text-danger">*</span></label>
                                    <input type="month" class="form-control" name="transaction_month_end" required>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="division_id">Tipe Bisnis <span class="text-danger">*</span></label>
                                <select name="division_id" id="division_id" class="form-control" required>
                                    <option value="" disabled selected>Pilih Tipe Bisnis</option>
                                    <option value="all">All</option>
                                    @foreach ($division as $divisi)
                                    <option value="{{ $divisi->id }}">{{ $divisi->description }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    `;
                    break;
                case '#cashFlowModal':
                    title = 'Arus Kas';
                    actionUrl = "{{ route('generate-financial-statement.cash') }}";
                    formContent = `
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="transaction_month_start">Transaksi Awal <span class="text-danger">*</span></label>
                                    <input type="month" class="form-control" name="transaction_month_start" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="transaction_month_end">Transaksi Akhir <span class="text-danger">*</span></label>
                                    <input type="month" class="form-control" name="transaction_month_end" required>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="division_id">Tipe Bisnis <span class="text-danger">*</span></label>
                                <select name="division_id" id="division_id" class="form-control" required>
                                    <option value="" disabled selected>Pilih Tipe Bisnis</option>
                                    <option value="all">All</option>
                                    @foreach ($division as $divisi)
                                    <option value="{{ $divisi->id }}">{{ $divisi->description }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    `;
                    break;
                default:
                    title = 'Unknown';
                    actionUrl = '';
                    formContent = '';
                    break;
            }

            $('#dynamicModalLabel').text(title);
            $('#modalBody').html(formContent);
            $('#dynamicForm').attr('action', actionUrl);
            $('#dynamicModal').modal('show');
        });
    });
</script>