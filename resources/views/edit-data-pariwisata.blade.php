@extends('layouts.user_type.auth')

@section('content')

<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
    <div class="container-fluid py-4">
        <div class="row">
            @include('components.alert-danger-success')
            <div class="col-lg-8 mx-auto">
                <div class="card h-100">
                    <div class="card-header pb-0 p-3">
                        <h6 class="mb-0">Edit Kendaraan</h6>
                    </div>
                    <div class="card-body p-3">
                        <form action="{{ route('pariwisata.update', $pariwisata->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="plat_nomor" class="form-label">Plat Nomor</label>
                                    <input type="text" class="form-control" id="plat_nomor" name="plat_nomor" value="{{ $pariwisata->plat_nomor }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="tahun_kendaraan" class="form-label">Tahun Kendaraan</label>
                                    <input type="number" class="form-control" id="tahun_kendaraan" name="tahun_kendaraan" value="{{ $pariwisata->tahun_kendaraan }}" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="karoseri" class="form-label">Karoseri</label>
                                    <input type="text" class="form-control" id="karoseri" name="karoseri" value="{{ $pariwisata->karoseri }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="no_rangka" class="form-label">Nomor Rangka</label>
                                    <input type="text" class="form-control" id="no_rangka" name="no_rangka" value="{{ $pariwisata->no_rangka }}" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="selling_price" class="form-label">Harga DP Sewa</label>
                                    <input type="text" class="form-control" id="selling_price" name="selling_price" value="{{ $pariwisata->selling_price }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="chart_of_account" class="form-label">Nomor Akun</label>
                                    <select class="form-control" id="chart_of_account" name="chart_of_account">
                                        @foreach ($chartOfAccounts as $coa)
                                        <option value="{{ $coa->account_id }}" {{ $pariwisata->account_id == $coa->account_id ? 'selected' : '' }}>
                                            {{ $coa->account_id }} - {{ $coa->account_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="evidence_image" class="form-label">Unggah Surat Layak Jalan <small>(Maks... 2MB)</small></label>
                                    <input type="file" class="form-control-file file-selector-button" name="evidence_image" id="evidence_image">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="evidence_image_bus" class="form-label">Unggah Foto Bis <small>(Maks... 2MB)</small></label>
                                    <input type="file" class="form-control-file file-selector-button" name="evidence_image_bus" id="evidence_image_bus">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                    <a href="{{ route('pariwisata.index') }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@endsection