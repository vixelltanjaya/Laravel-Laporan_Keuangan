<?php

namespace App\Http\Controllers;

use App\Models\BisPariwisata;
use App\Models\BookingBus;
use App\Models\CoaModel;
use App\Models\Customer;
use App\Models\DetailJournalEntry;
use App\Models\JournalEntry;
use Carbon\Carbon;
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
        $customers = Customer::all();
        $bus = BisPariwisata::where('plat_nomor', $plat_nomor)->first();
        return view('pesan-bus', compact('bus', 'bookings', 'customers'));
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



    public function store(Request $request)
    {
        Log::debug('masuk func store');
        Log::debug('cek req id ' . json_encode($request->all()));
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'description' => 'required|string|max:255',
            'customer_id' => 'required|exists:customer,id',
            'bus_pariwisata_id' => 'required|exists:bis_pariwisata,id',
            'evidence_image' => 'nullable|file|max:2048',
            'amount' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $amount = floatval(str_replace('.', '', $request->amount));
            // simpan ke booking_bus
            $booking = new BookingBus();
            $booking->start_book = $request->start_date;
            $booking->end_book = $request->end_date;
            $booking->is_booked = true;
            $booking->description = $request->description;
            $booking->customer_id = $request->customer_id;
            $booking->bus_pariwisata_id = $request->bus_pariwisata_id;
            $booking->journal_entry_id = 0;

            if ($request->hasFile('evidence_image')) {
                $path = $request->file('evidence_image')->store('evidence_images');
                $booking->evidence_image = $path;
            }
            $booking->save();
            Log::debug('bookings ' . json_encode($booking));

            // Increment dokumen
            $currentYear = date('Y');
            $currentMonth = date('m');
            $shortYear = substr($currentYear, -2);
            $pattern = 'BKM' . '/' . $shortYear . str_pad($currentMonth, 2, '0', STR_PAD_LEFT) . '/%';
            $lastEntry = JournalEntry::whereYear('entry_date', $currentYear)
                ->whereMonth('entry_date', $currentMonth)
                ->where('evidence_code', 'like', $pattern)
                ->orderBy('id', 'desc')
                ->first();

            Log::debug('Last Entry: ' . json_encode($lastEntry));

            if ($lastEntry) {
                Log::debug('Last Entry evidence_code: ' . $lastEntry->evidence_code);
                $increment = (int)substr($lastEntry->evidence_code, -4) + 1;
            } else {
                $increment = 1;
            }

            Log::debug('Increment: ' . $increment);

            $formattedNewId = str_pad($increment, 4, '0', STR_PAD_LEFT);
            $documentNo = $shortYear . str_pad($currentMonth, 2, '0', STR_PAD_LEFT) . '/' . $formattedNewId;
            $combinedEvidenceCode = 'BKM' . '/' . $documentNo;

            // simpan ke journal_entry
            $journalEntry = new JournalEntry();
            $journalEntry->description = $request->description;
            $journalEntry->entry_date = now();
            $journalEntry->user_id = auth()->user()->id;
            $journalEntry->evidence_code = $combinedEvidenceCode;
            $journalEntry->is_reversed = 0;
            $journalEntry->division_id = 1;
            $journalEntry->evidence_code_origin = '';
            $journalEntry->save();

            Log::debug('journal Entry ' . json_encode($journalEntry));

            // Update booking with journal_entry_id
            $booking->journal_entry_id = $journalEntry->id;
            $booking->save();
            Log::debug('bookings2 ' . json_encode($booking));

            // simpan ke detail journal entry
            $bisPariwisata = BisPariwisata::find($request->bus_pariwisata_id);
            $debitAccountId = 1001; // Account ID untuk debit
            $creditAccountId = $bisPariwisata->account_id; // Account ID dari bis pariwisata

            // Detail entry untuk debit
            $debitEntry = new DetailJournalEntry();
            $debitEntry->entry_id = $journalEntry->id;
            $debitEntry->account_id = $debitAccountId;
            $debitEntry->debit = $amount;
            $debitEntry->credit = 0;
            $debitEntry->evidence_image = $booking->evidence_image;
            $debitEntry->save();
            Log::debug('debit Entry ' . json_encode($debitEntry));

            // Detail entry untuk credit
            $creditEntry = new DetailJournalEntry();
            $creditEntry->entry_id = $journalEntry->id;
            $creditEntry->account_id = $creditAccountId;
            $creditEntry->debit = 0;
            $creditEntry->credit = $amount;
            $creditEntry->evidence_image = $booking->evidence_image;
            $creditEntry->save();
            Log::debug('credit Entry ' . json_encode($creditEntry));


            DB::commit();

            return response()->json(['success' => true, 'message' => 'Event saved successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save booking and journal entry: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to save event.']);
        }
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
