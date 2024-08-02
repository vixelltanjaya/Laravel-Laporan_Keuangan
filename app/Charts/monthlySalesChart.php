<?php

namespace App\Charts;

use App\Models\DetailJournalEntry;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class monthlySalesChart
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build(): \ArielMejiaDev\LarapexCharts\LineChart
    {
        $year = date('Y');
        $month = date('m');

        $arrayMonth = [];
        $arrayHarian = [];
        $arrayPariwisata = [];

        for ($i = 1; $i <= $month; $i++) {
            $totalPenjualanHarian = DetailJournalEntry::query()
                ->whereYear('updated_at', $year)
                ->whereMonth('updated_at', $i)
                ->where('account_id', '4000')
                ->sum('credit');
            $totalPenjualanPariwisata = DetailJournalEntry::query()
                ->whereYear('updated_at', $year)
                ->whereMonth('updated_at', $i)
                ->where('account_id', '4100')
                ->sum('credit');

            Log::debug('totalPenjualanharian ' . $totalPenjualanHarian);

            $arrayMonth[] = Carbon::create()->month($i)->format('F');
            $arrayHarian[] = $totalPenjualanHarian;
            $arrayPariwisata[] = $totalPenjualanPariwisata;
        }

        return $this->chart->lineChart()
            ->setTitle('Penjualan Bulanan')
            ->setSubtitle('Penjualan Pariwisata vs Penjualan Harian')
            ->addData('Penjualan Pariwisata',  $arrayPariwisata)
            ->addData('Penjualan Harian', $arrayHarian)
            ->setXAxis($arrayMonth);
    }
}
