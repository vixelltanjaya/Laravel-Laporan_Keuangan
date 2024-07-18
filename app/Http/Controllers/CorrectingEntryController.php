<?php

namespace App\Http\Controllers;

use App\Models\CoaModel;
use App\Models\DetailJournalEntry;
use App\Models\EvidenceCode;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CorrectingEntryController extends Controller
{
    public function index($id)
    {
        $accounts = CoaModel::all();
        $journalEntry = JournalEntry::where('id', $id)->first();
        $journalData = JournalEntry::joinDetailAndUsers($id);
        $prefixCode = EvidenceCode::all();

        Log::debug('evidence code' . $id);

        return view('user-accounting.correcting-entry',[
            'journalEntries' => $journalData->journalEntry,
            'details' => $journalData->details,
            'accounts' => $accounts,
            'journalEntry' => $journalEntry,
            'prefixCode' => $prefixCode
        ]);
    }

    public function store(Request $request, $id)
    {

        Log::debug('request ' .json_encode($request->all()));
        Log::debug('id ' . $id);
        // Validate the incoming request data
        $validatedData = $request->validate([
            'created_by' => 'required|string',
            'no_ref' => 'required|string',
            'transaction_date' => 'required|date',
            'notes' => 'required|string',
            'evidence_image' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048', // 'sometimes' if it's not always required
            // 'account_id' => 'required|array',
            // 'account_id.*' => 'integer',
            // 'amount' => 'required|array',
            // 'amount.*' => 'numeric'
        ]);

        // Handle file upload
        if ($request->hasFile('evidence_image')) {
            $imagePath = $request->file('evidence_image')->store('evidence_images', 'public');
            $validatedData['evidence_image'] = $imagePath;
        }

        // Find the JournalEntry by $id and update it
        $journalEntry = JournalEntry::findOrFail($id);
        $journalEntry->update([
            'description' => $validatedData['notes'],
            'user_id' => auth()->user()->id,
            'evidence_code' => $validatedData['no_ref'] . '/' . $this->generateDocumentNo(),
            'is_reversed' => false,
            'reversed_by' => null,
        ]);

        // Optional: Handle detail entries if applicable
        // For example, save associated journal details (if applicable)
        if (isset($validatedData['account_id']) && isset($validatedData['amount'])) {
            foreach ($validatedData['account_id'] as $index => $accountId) {
                DetailJournalEntry::updateOrCreate(
                    ['entry_id' => $journalEntry->id, 'account_id' => $accountId],
                    ['debit' => $validatedData['amount'][$index], 'credit' => 0] // Adjust as needed
                );
            }
        }

        return redirect()->route('journal.index')->with('success', 'Jurnal koreksi berhasil disimpan.');
    }
}
