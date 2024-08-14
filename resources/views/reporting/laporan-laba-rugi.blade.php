<div id="headerTable">
    <h6 class="mb-0 text-center">PT Maharani Putra Sejahtera</h6>
    <p class="mb-0 text-center" id="reportTitle">Laporan Laba Rugi</p>
    <p class="mb-4 text-center" id="reportPeriod">
        Periode {{ $formattedStartDate }} - {{ $formattedEndDate }}
    </p>
</div>

<table id="tableIncomeStatement" class="table table-bordered">
    <tbody>
        <tr>
            <td><strong>Pendapatan</strong></td>
            <td></td>
            <td></td>
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
            <td><strong>{{ number_format($totalPendapatan,2)}}</strong></td>
        </tr>
        <tr>
            <td><strong>Beban</strong></td>
            <td></td>
            <td></td>
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
            <td><strong>{{ number_format($totalBeban,2)}}</strong></td>
        </tr>
        <tr>
            <td><strong>Laba Bersih</strong></td>
            <td></td>
            <td><strong>{{ number_format($labaBersih,2)}}</strong></td>
        </tr>
    </tbody>
</table>