<tbody>
    <!-- Modal -->
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
