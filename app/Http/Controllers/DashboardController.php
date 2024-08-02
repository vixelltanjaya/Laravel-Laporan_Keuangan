<?php

namespace App\Http\Controllers;

use App\Charts\monthlySalesChart;
use App\Models\BisPariwisata;
use App\Models\BookingBus;
use App\Models\CoaModel;
use App\Models\DetailJournalEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $salesData = BisPariwisata::getSalesOnBothLine();
        $countBus = BisPariwisata::count();

        $accountGroups = [
            'group1' => [1000, 1001],
            'group2' => [2000, 2100, 2101],
            'group3' => [1100]
        ];

        $dashboard = DetailJournalEntry::getTotalAmountsByGroups($accountGroups);


        return view('dashboard', compact([
            'countBus',
            'dashboard'
        ]));
    }

    public function listPenjualanHarianVsPariwisata()
    {
        $year = date('Y');
        $month = date('m');
        $labels = [];
        $dataHarian = [];
        $dataPariwisata = [];

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

            $labels[] = Carbon::create()->month($i)->format('F');
            $dataHarian[] = $totalPenjualanHarian;
            $dataPariwisata[] = $totalPenjualanPariwisata;
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Pendapatan Harian',
                    'data' => $dataHarian,
                    'fill' => false,
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'tension' => 0.1
                ],
                [
                    'label' => 'Pendapatan Pariwisata',
                    'data' => $dataPariwisata,
                    'fill' => false,
                    'borderColor' => 'rgba(153, 102, 255, 1)',
                    'tension' => 0.1
                ]
            ]
        ]);
    }

    public function listBookDashboard()
    {
        $bookings = BookingBus::joinBusAndCustomerDashboard();
        $events = [];

        foreach ($bookings as $booking) {
            $startDate = $booking->start_book ? Carbon::parse($booking->start_book)->format('Y-m-d') : 'N/A';
            $endDate = $booking->end_book ? Carbon::parse($booking->end_book)->format('Y-m-d') : 'N/A';
            $tomorrow = date('Y-m-d', strtotime($endDate . "+1 days"));


            $plat_nomor = $booking->plat_nomor;
            $color = $this->generateColor($plat_nomor);

            $events[] = [
                'start' => $startDate,
                'end' => $tomorrow,
                'customer' => $booking->name,
                'bus' => $plat_nomor,
                'description' => $booking->description ?? 'Tidak ada deskripsi',
                'color' => $color
            ];
        }

        return response()->json($events);
    }


    private function generateColor($string)
    {
        $hash = crc32($string);
        $color = sprintf('#%06X', $hash & 0xFFFFFF);
        return $color;
    }
}
