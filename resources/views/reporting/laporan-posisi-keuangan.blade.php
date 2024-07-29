<tbody>
    <tr>
        <td><strong>Aset</strong></td>
        <td><strong>2023</strong></td>
        <td><strong>{{ request('transaction_month_end') ? date('F Y', strtotime(request('transaction_month_end') . '-01')) : '' }}</strong></td>
    </tr>
    <!-- Asset -->
    <tr>
        <td><strong>Aset Lancar</strong></td>
        <td></td>
        <td></td>
    </tr>
    @foreach($balanceSheet as $balance)
        @if(strtolower($balance->account_group) === 'lancar' || strtolower($balance->account_group) === 'aset lancar')
            <tr>
                <td>{{ $balance->account_name }}</td>
                <td></td>
                <td>{{ number_format($balance->total_amount, 2) }}</td>
            </tr>
        @endif
    @endforeach
    <tr>
        <td><strong>Total Aset Lancar</strong></td>
        <td></td>
        <td><strong>{{ number_format($balanceSheet->filter(function($item) {
            return strtolower($item->account_group) === 'lancar' || strtolower($item->account_group) === 'aset lancar';
        })->sum('total_amount'), 2) }}</strong></td>
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
                <td>{{ number_format($balance->total_amount, 2) }}</td>
            </tr>
        @endif
    @endforeach
    <tr>
        <td><strong>Total Aset Tetap</strong></td>
        <td></td>
        <td><strong>{{ number_format($balanceSheet->filter(function($item) {
            return strtolower($item->account_group) === 'tetap' || strtolower($item->account_group) === 'aset tetap';
        })->sum('total_amount'), 2) }}</strong></td>
    </tr>
    <tr>
        <td><strong>Total Aset</strong></td>
        <td></td>
        <td><strong>{{ number_format($balanceSheet->filter(function($item) {
            return strtolower($item->account_type) === 'aset' || strtolower($item->account_type) === 'a set';
        })->sum('total_amount'), 2) }}</strong></td>
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
                <td>{{ number_format($balance->total_amount, 2) }}</td>
            </tr>
        @endif
    @endforeach
    <tr>
        <td><strong>Total Kewajiban</strong></td>
        <td></td>
        <td><strong>{{ number_format($balanceSheet->filter(function($item) {
            return strtolower($item->account_type) === 'kewajiban' || strtolower($item->account_type) === 'kewajiban';
        })->sum('total_amount'), 2) }}</strong></td>
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
                <td>{{ number_format($balance->total_amount, 2) }}</td>
                <td></td>
            </tr>
        @endif
    @endforeach
    <tr>
        <td>Laba</td>
        <td></td>
        <td>{{ number_format(
            $balanceSheet->filter(function($item) {
                return strtolower($item->account_type) === 'pendapatan';
            })->sum('total_amount') -
            $balanceSheet->filter(function($item) {
                return strtolower($item->account_type) === 'beban';
            })->sum('total_amount'), 2
        ) }}</td>
    </tr>
    <tr>
        <td><strong>Total Ekuitas</strong></td>
        <td></td>
        <td><strong>{{ number_format(
            $balanceSheet->filter(function($item) {
                return strtolower($item->account_type) === 'ekuitas' || strtolower($item->account_type) === 'pendapatan';
            })->sum('total_amount') -
            $balanceSheet->filter(function($item) {
                return strtolower($item->account_type) === 'beban';
            })->sum('total_amount'), 2
        ) }}</strong></td>
    </tr> 
    <tr>
        <td><strong>Total Kewajiban dan Ekuitas</strong></td>
        <td></td>
        <td><strong>{{ number_format(
            $balanceSheet->filter(function($item) {
                return strtolower($item->account_type) === 'ekuitas' || strtolower($item->account_type) === 'pendapatan';
            })->sum('total_amount') -
            $balanceSheet->filter(function($item) {
                return strtolower($item->account_type) === 'beban';
            })->sum('total_amount') +
            $balanceSheet->filter(function($item) {
                return strtolower($item->account_type) === 'kewajiban';
            })->sum('total_amount'), 2
        ) }}</strong></td>
    </tr>
</tbody>
