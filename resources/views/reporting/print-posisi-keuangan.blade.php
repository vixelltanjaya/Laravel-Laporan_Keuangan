<div class="btn-group">
    <button id="printButton" class="btn btn-dark">
        <i class="ri-printer-line"></i> Print to PDF
    </button>
</div>
<form action="{{ route('export.balance-sheet') }}" method="GET" class="d-inline">
    @csrf
    <button type="submit" class="btn btn-info">Export To Excel Laporan Posisi Keuangan</button>
</form>