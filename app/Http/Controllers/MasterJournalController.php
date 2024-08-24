<?php

namespace App\Http\Controllers;

use App\Models\DetailMasterTransaction;
use App\Models\MasterTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MasterJournalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $masterTransaction = MasterTransaction::orderBy('code', 'asc')->get();

        return view('user-accounting.master-journal', compact('masterTransaction'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::debug('Request data: ' . json_encode($request->all()));

        // Validate data
        $request->validate([
            'description' => 'required|string|max:255',
            'evidence_id' => 'required|integer|exists:evidence_code,id',
            'noAccount.*' => 'required|exists:chart_of_account,account_id',
            'accountSign.*' => 'required|in:debit,credit',
        ]);

        // Begin a transaction
        DB::beginTransaction();

        try {
            // Create master journal
            $masterJournal = new MasterTransaction();
            $masterJournal->description = $request->description;
            $masterJournal->evidence_id = $request->evidence_id;
            $masterJournal->save();

            // Reload the model to get the auto-incremented code
            $masterJournal->refresh(); // Refresh the model to get the latest values
            Log::debug('Master Journal Code after save: ' . $masterJournal->code);

            // Check if code is null
            if (is_null($masterJournal->code)) {
                throw new \Exception('Failed to retrieve auto-increment code for master journal.');
            }

            // Save detail transactions
            $detailTransactions = [];
            foreach ($request->noAccount as $index => $noAccount) {
                $detailTransactions[] = [
                    'master_code' => $masterJournal->code, // Use the auto-incremented code
                    'gl_account' => $noAccount,
                    'account_position' => $request->accountSign[$index],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            Log::debug('Detail Transactions: ', ['transactions' => $detailTransactions]);

            // Insert detail transactions into the database
            DB::table('detail_master_transaction')->insert($detailTransactions);

            // Commit the transaction
            DB::commit();

            Log::debug('Transaction committed successfully.');
            return redirect()->route('master-journal.index')->with('berhasil', 'Master Journal berhasil dibuat');
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollback();
            Log::error('Transaction failed: ' . $e->getMessage());
            return redirect()->route('master-journal.index')->with('gagal', 'Master Journal gagal dibuat: ' . $e->getMessage());
        }
    }




    public function update(Request $request, string $id)
    {
        Log::debug('masuk func update');

        // Validate data
        $request->validate([
            'description' => 'required|string|max:255',
            'evidence_id' => 'required|integer|exists:evidence_code,id',
            'noAccount.*' => 'required|exists:chart_of_account,account_id',
            'accountSign.*' => 'required|in:debit,credit',
        ]);
        DB::beginTransaction();

        try {
            $masterJournal = MasterTransaction::findOrFail($id);

            // Update master journal details
            $masterJournal->description = $request->description;
            $masterJournal->evidence_id = $request->evidence_id;
            $masterJournal->save();

            Log::debug('Master journal updated: ' . json_encode($masterJournal));

            // Delete existing detail transactions associated with this journal
            DB::table('detail_master_transaction')->where('master_code', $masterJournal->code)->delete();

            // Prepare new detail transactions
            $detailTransactions = [];
            foreach ($request->noAccount as $index => $noAccount) {
                $detailTransactions[] = [
                    'master_code' => $masterJournal->code,
                    'gl_account' => $noAccount,
                    'account_position' => $request->accountSign[$index],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            Log::debug('Detail transactions: ' . json_encode($detailTransactions));
            DB::table('detail_master_transaction')->insert($detailTransactions);

            // Commit the transaction
            DB::commit();

            return redirect()->route('master-journal.index')->with('berhasil', 'Master Journal berhasil diperbarui');
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollback();
            Log::error('Update failed: ' . $e->getMessage());
            return redirect()->route('master-journal.index')->with('gagal', 'Master Journal gagal diperbarui: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $masterJournal = MasterTransaction::findOrFail($id);
            $masterJournal->delete();

            Log::debug('Tes transaksi yang masuk ' . json_encode($masterJournal));

            return redirect()->route('master-journal.index')->with('berhasil', 'Master Journal berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('master-journal.index')->with('gagal', 'Terjadi kesalahan saat menghapus Master Journal');
        }
    }
}
