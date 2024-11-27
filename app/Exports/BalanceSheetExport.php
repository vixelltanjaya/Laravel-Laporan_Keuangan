<?php

namespace App\Exports;

use Illuminate\Support\Collection as SupportCollection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class BalanceSheetExport implements FromCollection, WithHeadings, WithTitle
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

        // Header for Aset Lancar
        $data[] = ['Aset Lancar', '', '', ''];

        // Add Aset Lancar data
        foreach ($this->incomeStatement as $item) {
            if (strtolower($item->account_group) === 'aset lancar') {
                $data[] = ['', $item->account_name, number_format($item->total_amount, 2)];
            }
        }

        // Total Aset Lancar
        $totalAsetLancar = $this->incomeStatement->filter(function ($item) {
            return strtolower($item->account_group) === 'aset lancar';
        })->sum('total_amount');

        $data[] = ['Total Aset Lancar', '', '', number_format($totalAsetLancar, 2)];

        // Header for Aset Tetap
        $data[] = ['Aset Tetap', '', '', ''];

        // Add Aset Tetap data
        foreach ($this->incomeStatement as $item) {
            if (strtolower($item->account_group) === 'aset tetap' || strtolower($item->account_group) === 'tetap') {
                $data[] = ['', $item->account_name, number_format($item->total_amount, 2)];
            }
        }

        // Total Aset Tetap
        $totalAsetTetap = $this->incomeStatement->filter(function ($item) {
            return strtolower($item->account_group) === 'aset tetap' || strtolower($item->account_group) === 'tetap';
        })->sum('total_amount');

        $data[] = ['Total Aset Tetap', '', '', number_format($totalAsetTetap, 2)];

        // Total Aset (Aset Lancar + Aset Tetap)
        $totalAssets = $totalAsetLancar + $totalAsetTetap;
        $data[] = ['Total Aset', '', '', number_format($totalAssets, 2)];

        // Header Kewajiban
        $data[] = ['Kewajiban dan Ekuitas', '', '', ''];
        // Kewajiban
        $data[] = ['Kewajiban Lancar', '', '', ''];
        foreach ($this->incomeStatement as $item) {
            if (strtolower($item->account_group) === 'kewajiban lancar') {
                $data[] = ['', $item->account_name, number_format($item->total_amount, 2)];
            }
        }
        // header 
        $data[] = ['Kewajiban Jangka Panjang', '', ''];
        foreach ($this->incomeStatement as $item) {
            if (strtolower($item->account_group) === 'jangka panjang' || strtolower($item->account_group) === 'kewajiban tetap') {
                $data[] = ['', $item->account_name, number_format($item->total_amount, 2)];
            }
        }

        // Total kewajiban
        $totalKewajiban = $this->incomeStatement->filter(function ($item) {
            return strtolower($item->account_type) === 'kewajiban';
        })->sum('total_amount');
        $data[] = ['Total Kewajiban', '', '', number_format($totalKewajiban, 2)];

        // header ekuitas
        $data[] = ['Ekuitas Owner', '', ''];
        // ekuitas
        foreach ($this->incomeStatement as $item) {
            if (strtolower($item->account_type) === 'ekuitas') {
                $data[] = ['', $item->account_name, number_format($item->total_amount, 2)];
            }
        }
        // saldo laba
        $netIncome = isset($this->saldoLaba['netIncome']) ? $this->saldoLaba['netIncome'] : 0;
        $netIncomeCurrentMonth = isset($this->saldoLaba['netIncomeCurrentMonth']) ? $this->saldoLaba['netIncomeCurrentMonth'] : 0;
        $netIncomeYTD = isset($this->saldoLaba['netIncomeYTD']) ? $this->saldoLaba['netIncomeYTD'] : 0;

        
        $data[] = ['Laba Bulan Berjalan', '', number_format($netIncomeCurrentMonth, 2)];
        $data[] = ['Laba Tahun Berjalan', '', number_format($netIncomeYTD, 2)];
        $data[] = ['Laba Bersih', '', number_format($netIncome, 2)];
        
        $totalEkuitas = $this->incomeStatement->filter(function ($item) {
            return strtolower($item->account_type) === 'ekuitas' && strtolower($item->account_group) !== 'laba';
        })->sum('total_amount
        ');
        // total kewajiban + saldo laba
        $totalKewajibanAndLaba = $totalKewajiban + $totalEkuitas + $netIncomeCurrentMonth + $netIncomeYTD + $netIncome;
        $data[] = ['Total Kewajiban dan Ekuitas', '', '', number_format($totalKewajibanAndLaba, 2)];

        return collect($data);
    }

    public function headings(): array
    {
        return [
            ['Laporan Posisi Keuangan'],
            ['PT Maharani Putra Sejahtera'],
            ['Periode ' . $this->period['end_date']],
            [''],
            ['', '', '',''],
        ];
    }

    public function title(): string
    {
        return 'Laporan Posisi Keuangan';
    }

    public function startCell(): string
    {
        return 'A5';
    }
}
