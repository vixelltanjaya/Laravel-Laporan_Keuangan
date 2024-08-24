<?php

namespace App\Http\Controllers;

use App\Models\BisPariwisata;
use App\Models\BookingBus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PariwisataExternalController extends Controller
{
    public function index()
    {
        $bisPariwisata = BisPariwisata::joinSuratJalan();

        Log::debug('apa evidence image nya ' . json_encode($bisPariwisata));

        return view('customer.pariwisata-external', [
            'bisPariwisata' => $bisPariwisata
        ]);
    }

    public function listBookExternal()
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
