<?php

namespace App\Http\Controllers;

use App\Models\BisPariwisata;
use App\Models\BookingBus;
use App\Models\CoaModel;
use App\Models\Customer;
use App\Models\DetailJournalEntry;
use App\Models\JournalEntry;
use Carbon\Carbon;
use Exception;
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

        if (!$plat_nomor) {
            return redirect()->route('pariwisata.index')->with('error', 'Plat nomor tidak diberikan.');
        }

        $bookPlat = BookingBus::joinBusAndCustomer($plat_nomor);
        $bookings = BookingBus::all();
        $customers = Customer::all();
        $bus = BisPariwisata::where('plat_nomor', $plat_nomor)->first();

        Log::debug('bookPlat' . $bookPlat);
        return view('pesan-bus', compact(['bus', 'bookings', 'customers', 'bookPlat']));
    }

    public function listBook(Request $request)
    {
        $platNomor = $request->input('plat_nomor');

        if (!$platNomor) {
            return response()->json(['error' => 'Plat nomor tidak diberikan'], 400);
        }

        $bookings = BookingBus::joinBusAndCustomer($platNomor);
        $events = [];

        foreach ($bookings as $booking) {
            $startDate = $booking->start_book ? Carbon::parse($booking->start_book)->format('Y-m-d') : 'N/A';
            $endDate = $booking->end_book ? Carbon::parse($booking->end_book)->format('Y-m-d') : 'N/A';
            $tomorrow = date('Y-m-d', strtotime($endDate . "+1 days"));

            $events[] = [
                'title' => $booking->name,
                'start' => $startDate,
                'end' => $tomorrow,
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
            $debitEntry->evidence_image = $path ?? '';
            $debitEntry->save();
            Log::debug('debit Entry ' . json_encode($debitEntry));

            // Detail entry untuk credit
            $creditEntry = new DetailJournalEntry();
            $creditEntry->entry_id = $journalEntry->id;
            $creditEntry->account_id = $creditAccountId;
            $creditEntry->debit = 0;
            $creditEntry->credit = $amount;
            $creditEntry->evidence_image = $path ?? '';
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

    public function update(Request $request)
    {
        Log::debug('Request data: ' . json_encode($request->all()));

        $request->validate([
            'fleet_departure' => 'nullable|date_format:Y-m-d\TH:i',
            'fleet_arrivals' => 'nullable|date_format:Y-m-d\TH:i',
        ]);

        try {
            $booking = BookingBus::find($request->bus_pariwisata_id);
            $booking->fleet_departure = $request->fleet_departure;
            $booking->fleet_arrivals = $request->fleet_arrivals;
            $booking->save();

            $platNomor = $request->input('plat_nomor');
            return redirect()->route('pesan-bus.index', ['plat_nomor' => $platNomor])
                ->with('berhasil', 'Jam keberangkatan dan kedatangan berhasil diupdate.');
        } catch (Exception $e) {
            Log::error('Error updating booking: ' . $e->getMessage());
            return redirect()->route('pesan-bus.index',['plat_nomor' => $platNomor])->with('gagal', 'Terjadi kesalahan saat mengupdate jam keberangkatan dan kedatangan.');
        }
    }

    public function destroy($id)
    {
        Log::debug('route' . $id);
        try {
            $booking = BookingBus::findOrFail($id);
            $booking->delete();

            return redirect()->route('pariwisata.index')->with('berhasil', 'Booking berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting booking: ' . $e->getMessage());
            return redirect()->route('pariwisata.index')->with('gagal', 'Terjadi kesalahan saat menghapus booking.');
        }
    }
}
