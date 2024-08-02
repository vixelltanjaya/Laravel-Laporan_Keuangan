<tbody>
    <tr>
        <td><strong>Perhitungan</strong></td>
        <td></td>
        <td></td>
    </tr>
    <!-- Revenue Section -->
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
        <td><strong>{{ number_format($incomeStatement->filter(function($item) {
            return strtolower($item->account_type) === 'pendapatan';
        })->sum('total_amount'), 2) }}</strong></td>
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
        <td><strong>{{ number_format($incomeStatement->filter(function($item) {
            return strtolower($item->account_type) === 'beban';
        })->sum('total_amount'), 2) }}</strong></td>
    </tr>
    <!-- Net Income Section -->
    <tr>
        <td><strong>Laba Bersih</strong></td>
        <td></td>
        <td><strong>{{ number_format(
            $incomeStatement->filter(function($item) {
                return strtolower($item->account_type) === 'pendapatan';
            })->sum('total_amount') -
            $incomeStatement->filter(function($item) {
                return strtolower($item->account_type) === 'beban';
            })->sum('total_amount'), 2) 
        }}</strong></td>
    </tr>
</tbody>
