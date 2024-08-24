<?php

namespace App\Http\Controllers;

use App\Models\CoaModel;
use App\Models\DetailJournalEntry;
use App\Models\JournalEntry;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ViewCashInController extends Controller
{
    public function index($id)
    {
        Log::debug('Id Journal Entry:', ['id' => $id]);

        $journalData = JournalEntry::joinDetailAndUsers($id);
        $detailJournal = DetailJournalEntry::where('entry_id', $id)->first();

        $journalEntry = $journalData->journalEntry;
        $details = $journalData->details;
        $id = $journalEntry ? $journalEntry->id : null;

        $hasAccount2101 = false;
        foreach ($details as $detail) {
            if ($detail->account_id == 2101) {
                $hasAccount2101 = true;
                break;
            }
        }

        Log::debug('journalData:' . json_encode($journalData->details));
        Log::debug('detailJournal:' . json_encode($detailJournal));
        Log::debug('Id :' . json_encode($id));

        return view('user-accounting.view-cash-in', [
            'journalEntry' => $journalData->journalEntry,
            'details' => $journalData->details,
            'detailJournal' => $detailJournal,
            'id' => $id,
            'hasAccount2101' => $hasAccount2101,
            // 'no_ref_asal' => $journalEntry->no_ref_asal 
        ]);
    }

    public function generatePdf($id)
    {
        try {
            Log::info('route id: ' . $id);
            $id = intval($id);

            $journalData = JournalEntry::joinDetailAndUsers($id);
            $detailJournal = DetailJournalEntry::where('entry_id', $id)->first();
            $bookingData = JournalEntry::joinBookingBus($id);

            $formattedStartDate = Carbon::parse($bookingData->start_book)->format('d/m/Y');
            $formattedEndDate = Carbon::parse($bookingData->end_book)->format('d/m/Y');
            $formattedBookingDates = $formattedStartDate . ' - ' . $formattedEndDate;

            $journalEntry = $journalData->journalEntry;
            $details = $journalData->details;

            $evidenceCode = substr($journalEntry->evidence_code, 2, 12);
            $invoice = 'INV/' . $evidenceCode;

            Log::info('invoices ' . $invoice);
            Log::info('bookingData ' . json_encode($bookingData));
            Log::info('Journal Entry: ', (array) $journalEntry);
            Log::info('Detail Journal: ', (array) $detailJournal);
            Log::info('details: ', (array) $details);

            $data = [
                'journalEntry' => $journalEntry,
                'details' => $details,
                'detailJournal' => $detailJournal,
                'invoices' => $invoice,
                'bookingData' => $bookingData,
                'formattedBookingDates' => $formattedBookingDates,
            ];

            // Load the view and pass the data
            $pdf = Pdf::loadView('invoice_pdf', $data);

            return $pdf->download('invoice.pdf');
        } catch (Exception $e) {
            Log::error('Error generating PDF: ' . $e->getMessage());
            return redirect()->back()->withErrors('Transaksi bukan transaksi pemesanan Bus Pariwisata.');
        }
    }
}
