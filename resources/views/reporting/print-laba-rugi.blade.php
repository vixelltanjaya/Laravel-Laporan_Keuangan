<div class="d-flex justify-align-left">
    <div class="btn-group mx-2">
        <form action="{{ route('export.income-statement') }}" method="GET" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-info">Export to Excel</button>
        </form>
    </div>
    <div class="btn-group mx-2">
        <form action="{{ route('generate-financial-statement.generatePdfIncomeStatement') }}" method="GET" class="d-inline">
            <button type="submit" class="btn btn-dark">
                <i class="ri-printer-line"></i> Print to PDF
            </button>
        </form>
    </div>
</div>