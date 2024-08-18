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
            <th>Akun</th>
            <th>Jumlah (Rp)</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        <!-- Pendapatan Section -->
        <tr>
            <td colspan="3"><strong>Pendapatan</strong></td>
        </tr>
        @foreach($incomeStatement as $item)
        @if(strtolower($item->account_type) === 'pendapatan')
        <tr>
            <td>{{ $item->account_name }}</td>
            <td>{{ number_format($item->total_amount, 2) }}</td>
            <td>{{ $item->description ?? '' }}</td>
        </tr>
        @endif
        @endforeach
        <tr>
            <td><strong>Total Pendapatan</strong></td>
            <td><strong>{{ number_format($totalPendapatan, 2) }}</strong></td>
            <td></td>
        </tr>

        <!-- Beban Section -->
        <tr>
            <td colspan="3"><strong>Beban</strong></td>
        </tr>
        @foreach($incomeStatement as $item)
        @if(strtolower($item->account_type) === 'beban')
        <tr>
            <td>{{ $item->account_name }}</td>
            <td>{{ number_format($item->total_amount, 2) }}</td>
            <td>{{ $item->description ?? '' }}</td>
        </tr>
        @endif
        @endforeach
        <tr>
            <td><strong>Total Beban</strong></td>
            <td><strong>{{ number_format($totalBeban, 2) }}</strong></td>
            <td></td>
        </tr>

        <!-- Laba Bersih Section -->
        <tr>
            <td><strong>Laba Bersih</strong></td>
            <td><strong>{{ number_format($labaBersih, 2) }}</strong></td>
            <td></td>
        </tr>
    </tbody>
</table>
