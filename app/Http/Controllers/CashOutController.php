<?php

namespace App\Http\Controllers;

use App\Models\CoaModel;
use App\Models\DetailJournalEntry;
use App\Models\JournalEntry;
use App\Models\MasterTransaction;
use Barryvdh\DomPDF\PDF;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class CashOutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cashout = JournalEntry::whereRaw("substring(evidence_code from 1 for 3) != 'BKM'")
            ->orderBy('created_at', 'desc')
            ->get();
        Log::debug('isi cash out' . json_encode($cashout));
        // set format d/m/y
        foreach ($cashout as $transaction) {
            $transaction->formatted_created_at = Carbon::parse($transaction->created_at)->format('d/m/Y');
        }

        return view('user-accounting.cash-out', compact('cashout'));
    }


    public function store(Request $request)
    {
        Log::debug('Req id ' . json_encode($request->all()));

        $request->validate([
            'made_by' => 'required|string',
            'ref' => 'required',
            'master_transaction_id' => 'required|integer',
            'transaction_date' => 'required|date',
            'notes' => 'required|string',
            'division' => 'required',
            'evidence_image' => 'sometimes|file|mimes:jpeg,png,jpg|max:2048',
        ]);

        $formattedDate = Carbon::parse($request->entry_date)->format('Y-m-d');

        // Cek tanggal transaksi = hari ini
        if (!Carbon::parse($request->transaction_date)->isToday()) {
            return back()->withErrors(['transaction_date' => 'The transaction date must be today.'])->withInput();
        }

        // Handle file upload
        $imagePath = null;
        if ($request->hasFile('evidence_image')) {
            $imagePath = $request->file('evidence_image')->store('evidence_images', 'public');
            Log::debug('File uploaded. Path: ' . $imagePath);
        }

        // Normalize ref to array if it's a single value
        $refs = is_array($request->input('ref')) ? $request->input('ref') : [$request->input('ref')];

        DB::beginTransaction();

        try {
            Log::debug('Before processing each ref');

            DB::enableQueryLog();
            $currentYear = date('Y');
            $currentMonth = date('m');
            $shortYear = substr($currentYear, -2);
            // Process each 'ref' value
            foreach ($refs as $ref) {
                // Cek entri terakhir berdasarkan bulan dan ref
                $pattern = $ref . '/' . $shortYear . str_pad($currentMonth, 2, '0', STR_PAD_LEFT) . '/%';
                $lastEntry = JournalEntry::whereYear('entry_date', $currentYear)
                    ->whereMonth('entry_date', $currentMonth)
                    ->where('evidence_code', 'like', $pattern)
                    ->orderBy('id', 'desc')
                    ->first();

                Log::debug('Last Entry: ' . json_encode($lastEntry));

                if ($lastEntry) {
                    Log::debug('Last Entry evidence_code: ' . $lastEntry->evidence_code);
                    // Ambil 4 karakter terakhir dari evidence_code dan konversi ke integer
                    $increment = (int)substr($lastEntry->evidence_code, -4) + 1;
                } else {
                    $increment = 1;
                }

                Log::debug('Increment: ' . $increment);

                $formattedNewId = str_pad($increment, 4, '0', STR_PAD_LEFT);
                $documentNo = $shortYear . str_pad($currentMonth, 2, '0', STR_PAD_LEFT) . '/' . $formattedNewId;
                $combinedEvidenceCode = $ref . '/' . $documentNo;

                Log::debug('formattedNewId: ' . $formattedNewId);
                Log::debug('documentNo: ' . $documentNo);
                Log::debug('combinedEvidenceCode: ' . $combinedEvidenceCode);

                // Create JournalEntry
                $journalEntry = JournalEntry::create([
                    'description' => $request->notes,
                    'entry_date' => $formattedDate,
                    'user_id' => auth()->user()->id,
                    'evidence_code' => $combinedEvidenceCode,
                    'is_reversed' => false,
                    'reversed_by' => null,
                    'division_id' => $request->division ?? '',
                ]);

                Log::debug('Journal Entry created: ' . json_encode($journalEntry));

                // Filter gl_account for valid debit or credit values
                $filteredCoaIds = array_filter($request->input('gl_account'), function ($coaId, $index) use ($request) {
                    $debit = $request->input('debit')[$index] ?? 0;
                    $credit = $request->input('credit')[$index] ?? 0;
                    return $debit > 0 || $credit > 0;
                }, ARRAY_FILTER_USE_BOTH);

                // Create DetailJournalEntry for filtered coa_ids
                foreach ($filteredCoaIds as $index => $coaId) {
                    DetailJournalEntry::create([
                        'entry_id' => $journalEntry->id,
                        'account_id' => $coaId,
                        'debit' => $request->input('debit')[$index] ?? 0,
                        'credit' => $request->input('credit')[$index] ?? 0,
                        'evidence_image' => $imagePath ?? ''
                    ]);
                    Log::debug('Detail Journal Entry created for ref: ' . $ref);
                }
            }

            DB::commit();
            return redirect()->route('cash-out.index')->with('berhasil', 'Transaksi berhasil disimpan.');
        } catch (ValidationException $e) {
            DB::rollback();
            Log::error('Validation error: ' . $e->getMessage());
            return redirect()->route('cash-out-form.index')->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Error storing cash out transaction: ' . $e->getMessage());
            return redirect()->route('cash-out-form.index')->with('gagal', 'Transaksi gagal. ' . $e->getMessage());
        }
    }

}
