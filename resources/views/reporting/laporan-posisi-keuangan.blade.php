<div id="headerTable" style="text-align: center;">
    <h2 class="mb-0">PT Maharani Putra Sejahtera</h2>
    <p class="mb-0" id="reportTitle">Laporan Posisi Keuangan</p>
    <p class="mb-4" id="reportPeriod">
        Periode {{ $formattedEndDate }}
    </p>
</div>

<table id="tableBalanceSheet" class="table table-bordered">
    <tbody>
        <tr>
            <td><strong>Aset</strong></td>
            <td><strong></strong></td>
            <td><strong>{{ $formattedMonth }}</strong></td>
        </tr>
        <!-- Asset -->
        <tr>
            <td><strong>Aset Lancar</strong></td>
            <td></td>
            <td></td>
        </tr>
        @foreach($balanceSheet as $balance)
        @if(strtolower($balance->account_group) === 'aset lancar')
        <tr>
            <td>{{ $balance->account_name }}</td>
            <td></td>
            <td>{{ number_format($balance->total_amount, 2, ',', '.') }}</td>
        </tr>
        @endif
        @endforeach
        <tr>
            <td><strong>Total Aset Lancar</strong></td>
            <td></td>
            <td><strong>{{ number_format($balanceSheet->filter(function($item) {
            return strtolower($item->account_group) === 'lancar' || strtolower($item->account_group) === 'aset lancar';
        })->sum('total_amount'), 2, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <td><strong>Aset Tetap</strong></td>
            <td></td>
            <td></td>
        </tr>
        @foreach($balanceSheet as $balance)
        @if(strtolower($balance->account_group) === 'tetap' || strtolower($balance->account_group) === 'aset tetap')
        <tr>
            <td>{{ $balance->account_name }}</td>
            <td></td>
            <td>{{ number_format($balance->total_amount, 2, ',', '.') }}</td>
        </tr>
        @endif
        @endforeach
        <tr>
            <td><strong>Total Aset Tetap</strong></td>
            <td></td>
            <td><strong>{{ number_format($balanceSheet->filter(function($item) {
            return strtolower($item->account_group) === 'tetap' || strtolower($item->account_group) === 'aset tetap';
        })->sum('total_amount'), 2, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <td><strong>Total Aset</strong></td>
            <td></td>
            <td><strong>{{ number_format($balanceSheet->filter(function($item) {
            return strtolower($item->account_type) === 'aset' || strtolower($item->account_type) === 'aset';
        })->sum('total_amount'), 2, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <td><strong>&nbsp;</strong></td>
            <td><strong>&nbsp;</strong></td>
            <td><strong>&nbsp;</strong></td>
        </tr>
        <!-- Liability + Equity -->
        <tr>
            <td><strong>Kewajiban dan Ekuitas</strong></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td><strong>Kewajiban Lancar</strong></td>
            <td></td>
            <td></td>
        </tr>
        @foreach($balanceSheet as $balance)
        @if(strtolower($balance->account_group) === 'kewajiban lancar' || strtolower($balance->account_group) === 'Kewajiban Lancar')
        <tr>
            <td>{{ $balance->account_name }}</td>
            <td></td>
            <td>{{ number_format($balance->total_amount, 2, ',', '.') }}</td>
        </tr>
        @endif
        @endforeach
        <tr>
            <td><strong>Kewajiban Tidak Lancar</strong></td>
            <td></td>
            <td></td>
        </tr>
        @foreach($balanceSheet as $balance)
        @if(strtolower($balance->account_group) === 'jangka panjang' || strtolower($balance->account_group) === 'jangka panjang')
        <tr>
            <td>{{ $balance->account_name }}</td>
            <td></td>
            <td>{{ number_format($balance->total_amount, 2, ',', '.') }}</td>
        </tr>
        @endif
        @endforeach
        <tr>
            <td><strong>Total Kewajiban</strong></td>
            <td></td>
            <td><strong>{{ number_format($balanceSheet->filter(function($item) {
            return strtolower($item->account_type) === 'kewajiban' || strtolower($item->account_type) === 'kewajiban';
        })->sum('total_amount'), 2, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td><strong>Ekuitas Owner</strong></td>
            <td></td>
            <td></td>
        </tr>
        @foreach($balanceSheet as $balance)
        @if(strtolower($balance->account_group) === 'ekuitas')
        <tr>
            <td>{{ $balance->account_name }}</td>
            <td>{{ number_format($balance->total_amount, 2, ',', '.') }}</td>
            <td></td>
        </tr>
        @endif
        @endforeach
        <tr>
            <td>Laba Bulan Berjalan</td>
            <td></td>
            <td>{{ number_format($labaTahunBerjalan['netIncomeCurrentMonth'], 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Laba Tahun Berjalan</td>
            <td></td>
            <td>{{ number_format($labaTahunBerjalan['netIncomeYTD'], 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Saldo Laba</td>
            <td></td>
            <td>{{ number_format($labaTahunBerjalan['netIncome'], 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td><strong>Total Ekuitas</strong></td>
            <td></td>
            <td><strong>{{number_format($netAmount,2, ',', '.')}}</strong></td>
        </tr>
        <tr>
            <td><strong>Total Kewajiban dan Ekuitas</strong></td>
            <td></td>
            <td><strong>{{number_format($totalKewajibanDanEkuitas,2, ',', '.')}}</strong></td>
        </tr>
    </tbody>
</table>