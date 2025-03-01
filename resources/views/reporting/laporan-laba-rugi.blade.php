<div id="headerTable" style="text-align: center;">
    <h2 class="mb-0">PT Maharani Putra Sejahtera</h2>
    <p class="mb-0" id="reportTitle">Laporan Laba Rugi</p>
    <p class="mb-4" id="reportPeriod">
        Periode {{ $formattedStartDate }} - {{ $formattedEndDate }}
    </p>
</div>

<table id="tableIncomeStatement" class="table table-bordered">
    <thead>
        <tr>
            <th><strong>Nama Akun</strong></th>
            <th colspan="2"><strong>Nominal (Rp)</strong></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="3"><strong>Pendapatan</strong></td>
        </tr>
        @foreach($incomeStatement as $item)
        @if(strtolower($item->account_type) === 'pendapatan')
        <tr>
            <td>{{ $item->account_name }}</td>
            <td>{{ number_format($item->total_amount, 2) }}</td>
            <td></td>
        </tr>
        @endif
        @endforeach
        <tr>
            <td><strong>Total Pendapatan</strong></td>
            <td></td>
            <td><strong>{{ number_format($totalPendapatan, 2) }}</strong></td>
        </tr>
        <tr>
            <td colspan="3"><strong>Beban</strong></td>
        </tr>
        @foreach($incomeStatement as $item)
        @if(strtolower($item->account_type) === 'beban')
        <tr>
            <td>{{ $item->account_name }}</td>
            <td>{{ number_format($item->total_amount, 2) }}</td>
            <td></td>
        </tr>
        @endif
        @endforeach
        <tr>
            <td><strong>Total Beban</strong></td>
            <td></td>
            <td><strong>{{ number_format($totalBeban, 2) }}</strong></td>
        </tr>
        <tr>
            <td><strong>Laba Bersih</strong></td>
            <td></td>
            <td><strong>{{ number_format($labaBersih, 2) }}</strong></td>
        </tr>
    </tbody>
</table>
