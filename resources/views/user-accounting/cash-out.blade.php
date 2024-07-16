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
                            <a href="{{ route('cash-out-form.index') }}" class="btn bg-gradient-primary mb-0 me-2">+&nbsp;Add</a>
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
                                    <th>Tanggal</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ( $cashout as $co )
                                <tr>
                                    <td class="text-center">
                                        <p> {{$loop->iteration}} </p>
                                    </td>
                                    <td>
                                        <p> {{$co->formatted_created_at}} </p>
                                    </td>
                                    <td>
                                        <p> {{$co->description}} </p>
                                    </td>
                                    <td>
                                        <p> @if($co->is_reversed == 0)
                                            Baru
                                            @else
                                            Batal
                                            @endif
                                        </p>
                                    </td>
                                    <td class="ps-4">
                                        <p>
                                        <a href="{{route('view-cash-out.index',['id' => $co->id])}}" class="btn btn-link" target="_blank">
                                                <i class="fas fa-eye"></i> Lihat
                                            </a>
                                            <button class="btn btn-link btn-sm" onclick="printInvoice()">
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
        $('#evidenceCodeTable').DataTable();

    });
</script>

@endsection