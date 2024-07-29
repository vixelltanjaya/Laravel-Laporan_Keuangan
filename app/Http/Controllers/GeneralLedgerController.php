<?php

namespace App\Http\Controllers;

use App\Models\AccountBalance;
use App\Models\CoaModel;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GeneralLedgerController extends Controller
{
    /**
     * Display the general ledger page.
     */
    public function index()
    {
        // kasih kosong agar tidak error saat masuk ke page
        $monthYear = null;
        $chartOfAccounts = collect();
        $filteredAccounts = collect();

        return view('user-accounting.general-ledger', compact(
            'monthYear',
            'chartOfAccounts',
            'filteredAccounts'
        ));
    }

    /**
     * Handle the filtered request for general ledger.
     */
    public function getRequest(Request $request)
    {
        Log::debug('get request: ' . json_encode($request->all()));
        $monthYear = $request->input('month_year');

        $chartOfAccounts = CoaModel::getRequestTrx($request)->groupBy('account_id');

        // Process entries to calculate the amount based on account_sign
        $processedAccounts = $chartOfAccounts->map(function ($entries) {
            return $entries->map(function ($entry) {
                $accountSign = Str::lower($entry->account_sign);
                $entry->amount = $accountSign === 'debit'
                    ? $entry->debit - $entry->credit
                    : $entry->credit - $entry->debit;
                return $entry;
            });
        });

        Log::debug('Processed accounts: ', $processedAccounts->toArray());

        return view('user-accounting.general-ledger', compact('processedAccounts', 'monthYear'));
    }
}
