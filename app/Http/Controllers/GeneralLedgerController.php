<?php

namespace App\Http\Controllers;

use App\Models\AccountBalance;
use App\Models\CoaModel;
use App\Models\JournalEntry;
use Carbon\Carbon;
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
        $processedAccounts = collect();

        return view('user-accounting.general-ledger', compact(
            'monthYear',
            'chartOfAccounts',
            'filteredAccounts',
            'processedAccounts'
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
        $beginningBalance = CoaModel::sumBalanceCoa($request);

        // Process entries to calculate the amount based on account_sign
        $processedAccounts = $chartOfAccounts->map(function ($entries, $accountId) use ($beginningBalance, $monthYear) {
            // Calculate amount based on account_sign
            $entries = $entries->map(function ($entry) {
                $accountSign = Str::lower($entry->account_sign);
                $entry->amount = $accountSign === 'debit'
                    ? $entry->debit - $entry->credit
                    : $entry->credit - $entry->debit;
                return $entry;
            });

            // Add the beginning balance as the first entry
            $beginningBalanceEntry = $beginningBalance->firstWhere('account_id', $accountId);
            $beginningBalanceAmount = $beginningBalanceEntry ? $beginningBalanceEntry->beginning_balance_next_month : 0;
            $accountName = $beginningBalanceEntry ? $beginningBalanceEntry->account_name : 'Unknown Account';

            $beginningBalanceRow = (object)[
                'account_id' => $accountId,
                'account_name' => $accountName,
                'created_at' => Carbon::createFromFormat('Y-m', $monthYear)->startOfMonth()->format('Y/m/d'),
                'description' => 'Saldo Awal Bulan',
                'evidence_code' => '',
                'debit' => 0,
                'credit' => 0,
                'amount' => $beginningBalanceAmount,
                'readonly' => true, 
            ];

            return $entries->prepend($beginningBalanceRow);
        });

        Log::debug('Processed accounts: ', $processedAccounts->toArray());

        return view('user-accounting.general-ledger', compact('processedAccounts', 'monthYear'));
    }
}
