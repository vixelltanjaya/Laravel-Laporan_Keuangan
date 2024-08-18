<?php

namespace App\Http\Controllers;

use App\Exports\GeneralLedgerExport;
use App\Models\AccountBalance;
use App\Models\CoaModel;
use App\Models\JournalEntry;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

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
        $formattedDate = 'Pilih bulan dan tahun';

        return view('user-accounting.general-ledger', compact(
            'monthYear',
            'chartOfAccounts',
            'filteredAccounts',
            'processedAccounts',
            'formattedDate'
        ));
    }

    /**
     * Handle the filtered request for general ledger.
     */
    public function getRequest(Request $request)
    {
        Log::debug('get request: ' . json_encode($request->all()));
        $monthYear = $request->input('month_year');
        session()->put('general_ledger_request', $request->all());

        $chartOfAccounts = CoaModel::getRequestTrx($request)->groupBy('account_id');
        $beginningBalance = CoaModel::sumBalanceCoa($request);
        $formattedDate = $monthYear ? date('F Y', strtotime($monthYear . '-01')) : '';

        $processedAccounts = $chartOfAccounts->map(function ($entries, $accountId) use ($beginningBalance, $monthYear) {
            $entries = $entries->map(function ($entry) {
                $accountSign = Str::lower($entry->account_sign);
                $entry->amount = $accountSign === 'debit'
                    ? $entry->debit - $entry->credit
                    : $entry->credit - $entry->debit;
                $entry->formattedDateTrx = Carbon::parse($entry->created_at)->format('Y/m/d');
                return $entry;
            });

            // beginning balance
            $beginningBalanceEntry = $beginningBalance->firstWhere('account_id', $accountId);
            $beginningBalanceAmount = $beginningBalanceEntry ? $beginningBalanceEntry->beginning_balance_next_month : 0;
            $accountName = $beginningBalanceEntry ? $beginningBalanceEntry->account_name : 'Unknown Account';

            $beginningBalanceRow = (object)[
                'account_id' => $accountId,
                'account_name' => $accountName,
                'created_at' => Carbon::createFromFormat('Y-m', $monthYear)->startOfMonth()->format('Y/m/d'),
                'formattedDateTrx' => Carbon::createFromFormat('Y-m', $monthYear)->startOfMonth()->format('Y/m/d'),
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

        return view('user-accounting.general-ledger', compact('processedAccounts', 'monthYear', 'formattedDate'));
    }

    public function generateGlToExcel(Request $request)
    {
        $requestData = session()->get('general_ledger_request');
        if (is_null($requestData)) {
            return redirect()->back()->with('gagal', 'Terjadi kesalahan. Silakan refresh halaman dan coba lagi.');
        }

        $request = new Request($requestData);
        $monthYear = $request->input('month_year');

        $chartOfAccounts = CoaModel::getRequestTrx($request)->groupBy('account_id');
        $beginningBalance = CoaModel::sumBalanceCoa($request);
        $formattedDate = $monthYear ? date('F Y', strtotime($monthYear . '-01')) : '';

        $processedAccounts = $chartOfAccounts->map(function ($entries, $accountId) use ($beginningBalance, $monthYear) {
            $entries = $entries->map(function ($entry) {
                $accountSign = Str::lower($entry->account_sign);
                $entry->amount = $accountSign === 'debit'
                    ? $entry->debit - $entry->credit
                    : $entry->credit - $entry->debit;
                $entry->formattedDateTrx = Carbon::parse($entry->created_at)->format('Y/m/d');
                return $entry;
            });

            $beginningBalanceEntry = $beginningBalance->firstWhere('account_id', $accountId);
            $beginningBalanceAmount = $beginningBalanceEntry ? $beginningBalanceEntry->beginning_balance_next_month : 0;
            $accountName = $beginningBalanceEntry ? $beginningBalanceEntry->account_name : 'Unknown Account';

            $beginningBalanceRow = (object)[
                'account_id' => $accountId,
                'account_name' => $accountName,
                'created_at' => Carbon::createFromFormat('Y-m', $monthYear)->startOfMonth()->format('Y/m/d'),
                'formattedDateTrx' => Carbon::createFromFormat('Y-m', $monthYear)->startOfMonth()->format('Y/m/d'),
                'description' => 'Saldo Awal Bulan',
                'evidence_code' => '',
                'debit' => 0,
                'credit' => 0,
                'amount' => $beginningBalanceAmount,
                'readonly' => true,
            ];

            return $entries->prepend($beginningBalanceRow);
        });

        // Ensure that processedAccounts is a Collection
        $processedAccounts = collect($processedAccounts);

        return Excel::download(new GeneralLedgerExport($processedAccounts, $monthYear, $formattedDate), 'general_ledger.xlsx');
    }
}
