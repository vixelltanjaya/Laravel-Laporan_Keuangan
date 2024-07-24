<?php

namespace App\Http\Controllers;

use App\Models\BisPariwisata;
use App\Models\BookingBus;
use App\Models\CoaModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookBusExternalController extends Controller
{
    public function index(Request $request){
        $plat_nomor = $request->input('plat_nomor');

        Log::debug('request ' .json_encode($request->all()));

        $bookings = BookingBus::all();
        $chartOfAccounts=CoaModel::all();
        $bus = BisPariwisata::where('plat_nomor', $plat_nomor)->first();
        return view('customer.book-bus-external', compact('bus', 'bookings', 'chartOfAccounts'));
    }
    public function listBook(Request $request)
    {
        $platNomor = $request->input('plat_nomor');

        if (!$platNomor) {
            return response()->json(['error' => 'Plat nomor tidak diberikan'], 400);
        }

        // Retrieve bookings based on plat_nomor
        $bookings = BookingBus::joinBusAndCustomer($platNomor);
        $events = [];

        foreach ($bookings as $booking) {
            // Ensure start_book and end_book are formatted correctly
            $startDate = $booking->start_book ? Carbon::parse($booking->start_book)->format('Y-m-d') : 'N/A';
            $endDate = $booking->end_book ? Carbon::parse($booking->end_book)->format('Y-m-d') : 'N/A';

            $events[] = [
                'title' => $booking->name,
                'start' => $startDate,
                'end' => $endDate,
                'description' => $booking->description ?? 'Tidak ada deskripsi',
            ];
        }

        return response()->json($events);
    }
}
