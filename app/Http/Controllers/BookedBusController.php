<?php

namespace App\Http\Controllers;

use App\Models\BisPariwisata;
use App\Models\BookingBus;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookedBusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $plat_nomor = $request->input('plat_nomor');

        $bookings = BookingBus::all();
        $bus = BisPariwisata::where('plat_nomor', $plat_nomor)->first();
        return view('pesan-bus', compact('bus','bookings'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'customer_id' => 'required|exists:customer,id',
            'is_booked' => 'required|boolean',
            'bus_pariwisata_id' => 'required|exists:bis_pariwisata,id',
            'description' => 'required|string|max:155',
            'evidence_code' => 'required|string|max:14',
        ]);

        DB::transaction(function () use ($validatedData) {
            $journalEntry = JournalEntry::create([
                'description' => $validatedData['description'],
                'user_id' => auth()->id(), 
                'evidence_code' => $validatedData['evidence_code'],
                'is_reversed' => 0,
            ]);

            BookingBus::create([
                'customer_id' => $validatedData['customer_id'],
                'is_booked' => $validatedData['is_booked'],
                'bus_pariwisata_id' => $validatedData['bus_pariwisata_id'],
                'journal_entry_id' => $journalEntry->id,
            ]);
        });

        return redirect()->route('booking-bus.index')->with('berhasil', 'Booking created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
