<?php

namespace App\Exports;

use App\Models\CoaModel;
use Illuminate\Support\Collection as SupportCollection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class NetIncomeExport implements FromCollection, WithHeadings, WithTitle
{
    protected $incomeStatement;
    protected $period;

    public function __construct(SupportCollection $incomeStatement, $period)
    {
        $this->incomeStatement = $incomeStatement;
        $this->period = $period; 
    }


    public function collection()
    {

        $data = [];

        // header perhitungan
        $data[] = ['Perhitungan', '', ''];

        // pendapatan
        foreach ($this->incomeStatement as $item) {
            if (strtolower($item->account_type) === 'pendapatan') {
                $data[] = [$item->account_name, number_format($item->total_amount, 2), ''];
            }
        }
        $data[] = ['Total Pendapatan', '', number_format($this->incomeStatement->filter(function ($item) {
            return strtolower($item->account_type) === 'pendapatan';
        })->sum('total_amount'), 2)];

        // Header beban
        $data[] = ['Beban', '', ''];

        // Beban
        foreach ($this->incomeStatement as $item) {
            if (strtolower($item->account_type) === 'beban') {
                $data[] = [$item->account_name, number_format($item->total_amount, 2), ''];
            }
        }
        $data[] = ['Total Beban', '', number_format($this->incomeStatement->filter(function ($item) {
            return strtolower($item->account_type) === 'beban';
        })->sum('total_amount'), 2)];

        // Net Income
        $totalIncome = $this->incomeStatement->filter(function ($item) {
            return strtolower($item->account_type) === 'pendapatan';
        })->sum('total_amount');

        $totalExpense = $this->incomeStatement->filter(function ($item) {
            return strtolower($item->account_type) === 'beban';
        })->sum('total_amount');

        $netIncome = $totalIncome - $totalExpense;
        $data[] = ['Laba Bersih', '', number_format($netIncome, 2)];

        return collect($data);
    }

    public function headings(): array
    {
        return [
            ['Laporan Laba Rugi'],
            ['PT Maharani Putra Sejahtera'],
            ['Periode ' . implode(' - ', $this->period)],
            ['', '', ''], // Extra row for spacing
        ];
    }

    public function title(): string
    {
        return 'Laporan Laba Rugi';
    }

    public function startCell(): string
    {
        return 'A5';
    }
}
