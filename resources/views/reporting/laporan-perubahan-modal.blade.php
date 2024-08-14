<div class="d-flex justify-align-left">
    <div class="btn-group mx-2">
        <form action="{{ route('export.income-statement') }}" method="GET" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-info">Export to Excel</button>
        </form>
    </div>
    <div class="btn-group mx-2">
        <button id="printButton" class="btn btn-dark">
            <i class="ri-printer-line"></i> Print to PDF
        </button>
    </div>
</div>


<h6 class="mb-0 text-center">PT Maharani Putra Sejahtera</h6>
<p class="mb-0 text-center" id="reportTitle">Laporan Perubahan Modal</p>
<p class="mb-4 text-center" id="reportPeriod">
    Periode
    {{ request('transaction_month_start') ? date('d F Y', strtotime(request('transaction_month_start') . '-01')) : '' }}
    -
    {{ request('transaction_month_end') ? date('d F Y', strtotime(request('transaction_month_end') . '-01')) : '' }}
</p>

<table class="table table-bordered">
<tbody>
    <tr>
        <td><strong>Modal Pemilik</strong></td>
        <td></td>
        <td>{{ number_format($perubahanModal->firstWhere('account_name', 'Modal Pemilik')->total_amount ?? 0, 0, ',', '.') }}</td>
    </tr>
    <tr>
        <td>Laba</td>
        <td>{{ number_format($netIncomeResults['netIncomeYTD'] + ($netIncomeResults['netIncomeCurrentMonth']), 0, ',', '.') }}</td>
        <td></td>
    </tr>
    <tr>
        <td>Prive</td>
        <td>{{ number_format($perubahanModal->firstWhere('account_type', 'Prive')->total_amount ?? 0, 0, ',', '.') }}</td>
        <td></td>
    </tr>
    <tr>
        <td><strong>Perubahan Modal</strong></td>
        <td></td>
        <td><strong>
            {{ number_format($netIncomeResults['netIncomeYTD'] + ($netIncomeResults['netIncomeCurrentMonth']) - ($perubahanModal->firstWhere('account_type', 'Prive')->total_amount ?? 0), 0, ',', '.') }}
        </strong></td>
    </tr>
    <!-- Modal Akhir Section -->
    <tr>
        <td><strong>Modal Akhir</strong></td>
        <td></td>
        <td><strong>
            {{ number_format(($perubahanModal->firstWhere('account_type', 'Ekuitas')->total_amount ?? 0) + ($netIncomeResults['netIncomeYTD'] + ($netIncomeResults['netIncomeCurrentMonth']) - ($perubahanModal->firstWhere('account_type', 'Prive')->total_amount ?? 0)), 0, ',', '.') }}
        </strong></td>
    </tr>
</tbody>
</table>
