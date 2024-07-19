<?php

namespace App\Http\Controllers;

use App\Models\CoaModel;
use App\Models\DetailJournalEntry;
use App\Models\Division;
use App\Models\EvidenceCode;
use App\Models\JournalEntry;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CorrectingEntryController extends Controller
{
    public function index($id)
    {
        $accounts = CoaModel::all();
        $journalEntry = JournalEntry::where('id', $id)->first();
        $journalData = JournalEntry::joinDetailAndUsers($id);
        $prefixCode = EvidenceCode::all();
        $division = Division::all();

        Log::debug('evidence code' . $id);

        return view('user-accounting.correcting-entry', [
            'journalEntries' => $journalData->journalEntry,
            'details' => $journalData->details,
            'accounts' => $accounts,
            'journalEntry' => $journalEntry,
            'prefixCode' => $prefixCode,
            'division' => $division
        ]);
    }

    public function store(Request $request, $id)
    {
        Log::debug('Req id ' . json_encode($request->all()));

        $validatedData = $request->validate([
            'created_by' => 'required|string',
            'no_ref_asal' => 'required|string',
            'no_ref' => 'required|string',
            'transaction_date' => 'required|date',
            'notes' => 'required|string',
            'division' => 'required',
            'noAccount' => 'required|array',
            'noAccount.*' => 'integer',
            'accountSign' => 'required|array',
            'accountSign.*' => 'in:debit,credit',
            'amount' => 'required|array',
            'amount.*' => 'required|numeric',
            'evidence_image' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        Log::debug('validated data ' . json_encode($validatedData));
        // Format the transaction date
        $formattedDate = Carbon::parse($request->transaction_date)->format('Y-m-d');

        // Check if the transaction date is today
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
        $refs = is_array($request->input('no_ref')) ? $request->input('no_ref') : [$request->input('no_ref')];

        DB::beginTransaction();

        try {
            Log::debug('Before processing each ref');

            DB::enableQueryLog();
            $currentYear = date('Y');
            $currentMonth = date('m');
            $shortYear = substr($currentYear, -2);

            // Process each 'ref' value
            foreach ($refs as $ref) {
                // Check the last entry based on month and ref
                $pattern = $ref . '/' . $shortYear . str_pad($currentMonth, 2, '0', STR_PAD_LEFT) . '/%';
                $lastEntry = JournalEntry::whereYear('entry_date', $currentYear)
                    ->whereMonth('entry_date', $currentMonth)
                    ->where('evidence_code', 'like', $pattern)
                    ->orderBy('id', 'desc')
                    ->first();

                Log::debug('Last Entry: ' . json_encode($lastEntry));

                $increment = $lastEntry ? (int)substr($lastEntry->evidence_code, -4) + 1 : 1;

                Log::debug('Increment: ' . $increment);

                $formattedNewId = str_pad($increment, 4, '0', STR_PAD_LEFT);
                $documentNo = $shortYear . str_pad($currentMonth, 2, '0', STR_PAD_LEFT) . '/' . $formattedNewId;
                $combinedEvidenceCode = $ref . '/' . $documentNo;

                Log::debug('formattedNewId: ' . $formattedNewId);
                Log::debug('documentNo: ' . $documentNo);
                Log::debug('combinedEvidenceCode: ' . $combinedEvidenceCode);

                // Update journalEntry
                $editJournalEntry = JournalEntry::findOrFail($id);
                $editJournalEntry->is_reversed = 2;
                $editJournalEntry->reversed_by = Auth::user()->name;
                $editJournalEntry->reversed_at = now();
                $editJournalEntry->save();
                Log::debug('Journal Entry updated: ' . json_encode($editJournalEntry));

                // Create journalEntry
                $journalEntry = JournalEntry::create([
                    'description' => $request->notes,
                    'entry_date' => $formattedDate,
                    'user_id' => auth()->user()->id,
                    'evidence_code' => $combinedEvidenceCode,
                    'is_reversed' => true,
                    'reversed_by' => auth()->user()->name,
                    'reversed_at' => now(),
                    'division_id' => $request->division ?? '',
                    'evidence_code_origin' => $request->no_ref_asal ?? ''
                ]);

                Log::debug('Journal Entry created: ' . json_encode($journalEntry));

                // Create DetailJournalEntry for each account
                foreach ($request->input('noAccount') as $index => $accountId) {
                    DetailJournalEntry::create([
                        'entry_id' => $journalEntry->id,
                        'account_id' => $accountId,
                        'debit' => $request->input('accountSign')[$index] === 'debit' ? $request->input('amount')[$index] : 0,
                        'credit' => $request->input('accountSign')[$index] === 'credit' ? $request->input('amount')[$index] : 0,
                        'evidence_image' => $imagePath ?? ''
                    ]);
                    Log::debug('Detail Journal Entry created for account ID: ' . $accountId);
                }
            }


            // DB::rollBack();
            DB::commit(); 
            return redirect()->route('cash-in.index')->with('berhasil', 'Transaksi berhasil disimpan (dalam mode pengujian).');
        } catch (ValidationException $e) {
            DB::rollback();
            Log::error('Validation error: ' . $e->getMessage());
            return redirect()->route('correcting-entry.index', ['id' => $id])->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Error storing cash out transaction: ' . $e->getMessage());
            return redirect()->route('correcting-entry.index', ['id' => $id])->with('gagal', 'Transaksi gagal. ' . $e->getMessage());
        }
    }
}
