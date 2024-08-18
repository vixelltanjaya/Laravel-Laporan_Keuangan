<?php

namespace App\Exports;

use Illuminate\Support\Collection as SupportCollection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class PerubahanModalExport implements FromCollection, WithHeadings, WithTitle
{

    protected $incomeStatement;
    protected $saldoLaba;
    protected $period;

    public function __construct(SupportCollection $incomeStatement, $saldoLaba, $period)
    {
        $this->incomeStatement = $incomeStatement;
        $this->saldoLaba = $saldoLaba;
        $this->period = $period;
    }

    public function collection()
    {
        $data = [];

        // Initialize $modalPemilikRaw to ensure it is always defined
        $modalPemilikRaw = 0;

        // Modal Pemilik
        foreach ($this->incomeStatement as $item) {
            if (strtolower($item->account_name) === 'modal pemilik') {
                $data[] = ['', $item->account_name, number_format($item->total_amount, 2)];
                // Assuming $item->total_amount is numeric
                $modalPemilikRaw = $item->total_amount;
            }
        }
        $data[] = ['Modal Pemilik', '', '', number_format($modalPemilikRaw, 2)];

        // Saldo Laba
        $netIncome = isset($this->saldoLaba['netIncome']) ? $this->saldoLaba['netIncome'] : 0;
        $netIncomeCurrentMonth = isset($this->saldoLaba['netIncomeCurrentMonth']) ? $this->saldoLaba['netIncomeCurrentMonth'] : 0;
        $netIncomeYTD = isset($this->saldoLaba['netIncomeYTD']) ? $this->saldoLaba['netIncomeYTD'] : 0;
        // $data[] = ['Laba Bulan Berjalan', '', number_format($netIncomeCurrentMonth, 2)];
        // $data[] = ['Laba Tahun Berjalan', '', number_format($netIncomeYTD, 2)];
        // $data[] = ['Laba Bersih', '', number_format($netIncome, 2)];
        $totalLaba = $netIncome + $netIncomeCurrentMonth + $netIncomeYTD;
        $data[] = ['Laba', '',number_format($totalLaba, 2)];

        // prive
        $prive = $this->incomeStatement->filter(function ($item) {
            return strtolower($item->account_name) === 'prive';
        })->sum('total_amount');
        $data[] = ['Prive', '', number_format($prive, 2)];

        // Laba - Prive
        $perubahanModal = $totalLaba - $prive;
        $data[] = ['Perubahan Modal', '', '', number_format($perubahanModal, 2)];

        // Calculate Total Perubahan Modal
        $totalPerubahanModal = $perubahanModal - $modalPemilikRaw;
        $data[] = ['Total Kewajiban dan Laba', '', '', number_format($totalPerubahanModal, 2)];

        return collect($data);
    }

    public function headings(): array
    {
        return [
            ['Laporan Perubahan Modal'],
            ['PT Maharani Putra Sejahtera'],
            ['Periode ' . $this->period['end_date']],
        ];
    }

    public function title(): string
    {
        return 'Laporan Buku Besar';
    }

    public function startCell(): string
    {
        return 'A5';
    }
}
