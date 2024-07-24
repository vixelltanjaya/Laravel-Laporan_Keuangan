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
                            <h5 class="mb-0">Uang Keluar</h5>
                        </div>
                        <div class="nav-item d-flex align-self-end">
                            <a href="{{ route('cash-out-form.index') }}" class="btn bg-gradient-primary mb-0 me-2">Form Uang Keluar</a>
                        </div>
                    </div>
                </div>
                @include('components.alert-danger-success')
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="order-column stripe" id="cashOutTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
                                    <th>Kode Transaksi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ( $cashout as $cout )
                                <tr>
                                    <td>
                                        <p>{{$loop->iteration}}</p>
                                    </td>
                                    <td>
                                        <p>{{$cout->entry_date}}</p>
                                    </td>
                                    <td>
                                        <p>{{$cout->description}}</p>
                                    </td>
                                    <td>
                                        <p>{{$cout->evidence_code}}</p>
                                    </td>
                                    <td>
                                        <p>
                                            <a href="{{route('view-cash-out.index',['id' => $cout->id])}}" class="btn btn-link text-info">
                                                <i class="fas fa-eye"></i> Lihat
                                            </a>
                                            <button class="btn btn-link btn-sm text-dark" onclick="printInvoice()">
                                                <i class="fas fa-print"></i> Print
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

<script>
    $(document).ready(function() {
        $('#cashOutTable').DataTable();
    });

    function printInvoice() {
        window.print();
    }
</script>

@endsection