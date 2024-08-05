@extends('layouts.user_type.auth')

@section('content')

<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Tambah Kode Bukti Transaksi</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('evidence-code.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="prefix_code">Kode</label>
                        <input type="text" class="form-control" id="prefix_code" name="prefix_code" required>
                        <label style="color: lightgray; font-size: 12px; font-family:Arial, Helvetica, sans-serif">Maksimal 3 Huruf untuk kode prefiks</label>
                    </div>
                    <div class="form-group">
                        <label for="code_title">Deskripsi Kode</label>
                        <input type="text" class="form-control" id="code_title" name="code_title"  required>
                    </div>
                    <div class="form-group">
                        <label for="contoh_kode">Contoh Format Kode Full</label>
                        <input type="text" class="form-control" value="JRN/2401/0001" readonly>
                        <label style="color: lightgray; font-size: 12px; font-family:Arial, Helvetica, sans-serif">Urutan Format Kode adalah: Prefiks/YYMM/0001 -> akan bertambah setiap ada dokumen baru </label>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="submit" class="btn btn-secondary">reset</button>
                    <a href="{{ url()->previous() }}" class="btn btn-default">Back</a>
                </form>
            </div>
        </div>
    </div>
</body>

@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('prefix_code').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    });
</script>