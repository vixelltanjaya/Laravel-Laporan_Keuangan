<?php

namespace App\Http\Controllers;

use App\Models\CoaModel;
use App\Models\DetailJournalEntry;
use App\Models\JournalEntry;
use App\Models\MasterTransaction;
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


        $year = date('y');
        $month = date('m');

        // Cek entri terakhir berdasarkan bulan
        $lastEntry = JournalEntry::whereYear('created_at', '=', Carbon::now()->year)
            ->whereMonth('created_at', '=', Carbon::now()->month)
            ->orderBy('id', 'desc')
            ->first();

        $increment = $lastEntry ? (int)substr($lastEntry->evidence_code, 9, 4) + 1 : 1;
        // $increment = $lastEntry ? (int)substr($lastEntry->evidence_code, -4) + 1 : 1;
        Log::debug('monthly incremeent id ' . json_encode($lastEntry));

        // Format new doc 4 digits
        $formattedNewId = str_pad($increment, 4, '0', STR_PAD_LEFT);

        Log::debug('formatted NewId ' . json_encode($formattedNewId));

        // Gabungkan tahun, bulan, dan newFormattedDoc untuk membuat documentNo
        $documentNo = $year . str_pad($month, 2, '0', STR_PAD_LEFT) . '/' . $formattedNewId;
        $combinedEvidenceCode = $request->ref . '/' . $documentNo;

        Log::debug('document NO ' . json_encode($documentNo));

        DB::beginTransaction();

        try {
            Log::debug('Before validation');

            // Validate request
            $validatedData = $request->validate([
                'made_by' => 'required|string',
                'ref' => 'required|string',
                'master_transaction_id' => 'required|integer',
                'transaction_date' => 'required|date',
                'notes' => 'required|string',
                'evidence_image' => 'sometimes|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            Log::debug('After validation: ' . json_encode($validatedData));

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

            $combinedEvidenceCode = $request->ref . '/' . $documentNo;

            // Create JournalEntry
            $journalEntry = JournalEntry::create([
                'description' => $request->notes,
                'user_id' => auth()->user()->id,
                'evidence_code' => $combinedEvidenceCode,
                'is_reversed' => false,
                'reversed_by' => null,
            ]);

            Log::debug('Journal Entry created: ' . json_encode($journalEntry));
            Log::debug('request inputan: ' . json_encode($request->input('gl_account')));

            // Filter gl_account untuk memastikan hanya yang memiliki nilai debit atau kredit yang diproses
            $filteredCoaIds = array_filter($request->input('gl_account'), function ($coaId, $index) use ($request) {
                $debit = $request->input('debit')[$index] ?? 0;
                $credit = $request->input('credit')[$index] ?? 0;
                return $debit > 0 || $credit > 0;
            }, ARRAY_FILTER_USE_BOTH);

            // Log filtered coa_ids
            Log::debug('Filtered gl_account: ' . json_encode($filteredCoaIds));

            // Create DetailJournalEntry for filtered coa_ids
            foreach ($filteredCoaIds as $index => $coaId) {
                $detail = DetailJournalEntry::create([
                    'entry_id' => $journalEntry->id,
                    'account_id' => $coaId,
                    'debit' => $request->input('debit')[$index] ?? 0,
                    'credit' => $request->input('credit')[$index] ?? 0,
                    'evidence_image' => $imagePath ?? ''
                ]);
                Log::debug('Detail Journal Entry created: ' . json_encode($detail));
                Log::debug('request evidence image: ' . json_encode($request->evidence_image));
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
