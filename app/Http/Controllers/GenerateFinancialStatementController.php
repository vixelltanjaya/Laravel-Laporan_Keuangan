<?php

namespace App\Http\Controllers;

use App\Exports\BalanceSheetExport;
use App\Exports\NetIncomeExport;
use App\Models\AccountBalance;
use App\Models\CoaModel;
use App\Models\DetailJournalEntry;
use App\Models\JournalEntry;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;


class GenerateFinancialStatementController extends Controller
{

    public function income(Request $request)
    {
        Log::debug('income request: ' . json_encode($request->all()));
        $reportType = 'income';

        session()->put('export_request', $request->all());

        // Pass the $request object directly to the function
        $incomeStatement = CoaModel::getSumRequestTrx($request);
        Log::debug('income incomeStatement: ' . json_encode($incomeStatement));

        return view('user-accounting.generate-financial-statement', compact([
            'reportType',
            'incomeStatement',
        ]));
    }
    public function balance(Request $request)
    {
        Log::debug('req id ' . json_encode($request->all()));

        $reportType = 'balance';
        session()->put('export_request', $request->all());
        $balanceSheet = CoaModel::getSumRequestTrx($request);
        $netIncomeResults = $this->countNetIncome($request);


        return view('user-accounting.generate-financial-statement', [
            'reportType' => $reportType,
            'balanceSheet' => $balanceSheet,
            'labaTahunBerjalan' => $netIncomeResults,
        ]);
    }

    public function perubahanModal(Request $request)
    {
        $reportType = 'perubahanModal';
        session()->put('export_request', $request->all());
        $netIncomeResults = $this->countNetIncome($request);
        $perubahanModal = CoaModel::getSumRequestTrx($request);

        // dd($netIncomeResults);

        return view('user-accounting.generate-financial-statement', compact('reportType', 'netIncomeResults', 'perubahanModal'));
    }

    public function countNetIncome($request)
    {
        Log::debug('func count countNetIncome' . json_encode($request->all()));

        $startDate = $request->input('transaction_month_start');
        $endDate = $request->input('transaction_month_end');

        // Format Year and Month
        $endDateMonth = Carbon::parse($endDate)->month;
        $startYear = Carbon::parse($startDate)->year;

        // untuk filter date satu
        if (!$endDate) {
            $endDateToStart = Carbon::now()->startOfMonth()->format('Y-m-01');
        } else {
            $endDateToStart = Carbon::parse($endDate)->startOfMonth()->format('Y-m-01');
        }

        // Log::debug('endDateToStart' . json_encode($endDateToStart));
        // Log::debug('endDateMonth' . json_encode($endDateMonth));

        $endYear = Carbon::parse($endDate)->year;
        $lastMonthPreviousYear = 12;
        $lastDatePreviousYear = Carbon::create($endYear - 1, $lastMonthPreviousYear, 31)->endOfDay();

        // current month
        $startDateParsed = Carbon::parse($endDateToStart);
        $endDateParsed = Carbon::parse($endDate)->endOfDay();
        // Log::debug('start date parsed' . json_encode($startDateParsed));
        // Log::debug('end date parsed' . json_encode($endDateParsed));

        // Get all transactions
        $currentMonthIncome = DetailJournalEntry::all();
        $currentYearIncome = CoaModel::getNetIncome($request);

        // Filter transactions for the entire period
        $incomeStatementPeriod = $currentYearIncome->filter(function ($item) use ($lastDatePreviousYear) {
            $transactionDate = Carbon::parse($item->entry_date);
            return $transactionDate->lessThanOrEqualTo($lastDatePreviousYear);
        });

        // Log::debug('incomeStatementPeriod' . json_encode($incomeStatementPeriod));

        // Filter transactions for the current month
        $incomeStatementMonth = $currentMonthIncome->filter(function ($item) use ($startDateParsed, $endDateParsed) {
            $transactionDate = Carbon::parse($item->created_at);
            return $transactionDate->between($startDateParsed, $endDateParsed);
        });

        // Log::debug('income statement month' . json_encode($incomeStatementMonth));

        // Filter transactions for the current year ver 1.1
        $incomeStatementYear = $currentYearIncome->filter(function ($item) use ($startYear, $endDateMonth) {
            $transactionDate = Carbon::parse($item->entry_date);
            return ($transactionDate->year == $startYear) && ($transactionDate->month <= ($endDateMonth - 1));
        });

        // Log::debug('income statement year' . json_encode($incomeStatementYear));

        // Calculate revenue and expense for the entire period
        $revenue = $incomeStatementPeriod->filter(function ($item) {
            return str_starts_with($item->account_id, '4');
        })->sum(function ($item) {
            return $item->total_credit - $item->total_debit;
        });

        $expense = $incomeStatementPeriod->filter(function ($item) {
            return str_starts_with($item->account_id, '5');
        })->sum(function ($item) {
            return $item->total_debit - $item->total_credit;
        });

        // Calculate revenue and expense for the current month
        $revenueCurrentMonth = $incomeStatementMonth->filter(function ($item) {
            return str_starts_with($item->account_id, '4');
        })->sum(function ($item) {
            return $item->credit - $item->debit;
        });

        $expenseCurrentMonth = $incomeStatementMonth->filter(function ($item) {
            return str_starts_with($item->account_id, '5');
        })->sum(function ($item) {
            return $item->debit - $item->credit;
        });

        // Calculate revenue and expense for the current year
        $revenueCurrentYear = $incomeStatementYear->filter(function ($item) {
            return str_starts_with($item->account_id, '4');
        })->sum(function ($item) {
            return  $item->total_credit - $item->total_debit;
        });

        $expenseCurrentYear = $incomeStatementYear->filter(function ($item) {
            return str_starts_with($item->account_id, '5');
        })->sum(function ($item) {
            return $item->total_debit - $item->total_credit;
        });

        // Calculate net income
        $netIncome = $revenue - $expense; // Akumulatif
        $netIncomeCurrentMonth = $revenueCurrentMonth - $expenseCurrentMonth; // Bulanan
        $netIncomeYTD = $revenueCurrentYear - $expenseCurrentYear; // Tahunan

        Log::debug('Revenue: ' . $revenue);
        Log::debug('Expense: ' . $expense);
        Log::debug('Revenue for Current Month: ' . $revenueCurrentMonth);
        Log::debug('Expense for Current Month: ' . $expenseCurrentMonth);
        Log::debug('Revenue for Current Year: ' . $revenueCurrentYear);
        Log::debug('Expense for Current Year: ' . $expenseCurrentYear);

        return [
            'netIncome' => $netIncome,
            'netIncomeCurrentMonth' => $netIncomeCurrentMonth,
            'netIncomeYTD' => $netIncomeYTD
        ];
    }

    public function exportIncomeStatement(Request $request)
    {
        $requestData = session()->get('export_request');
        $request = new Request($requestData);

        $startDate = $request->input('transaction_month_start');
        $endDate = $request->input('transaction_month_end');

        $formattedStartDate = Carbon::parse($startDate)->format('d-F-Y');
        $formattedEndDate = Carbon::parse($endDate)->format('d-F-Y');

        // Instead of creating a period, directly use the dates
        $months = [
            'start_date' => $formattedStartDate,
            'end_date' => $formattedEndDate,
        ];

        Log::debug('exportIncomeStatement request: ' . json_encode($request->all()));

        $incomeStatement = CoaModel::getSumRequestTrx($request);
        Log::debug('exportIncomeStatement incomeStatement: ' . json_encode($incomeStatement));

        session()->forget('export_request');

        return Excel::download(new NetIncomeExport($incomeStatement, $months), 'net_income.xlsx');
    }

    public function exportBalanceSheet(Request $request)
    {
        Log::debug('export balance sheet' .json_encode($request));
        $requestData = session()->get('export_request');
        $request = new Request($requestData);
        
        Log::debug('export balance sheet request berdasar filter index' .json_encode($request));
        $startDate = $request->input('transaction_month_start');
        $endDate = $request->input('transaction_month_end');

        $startDate = $startDate ?: '2024-01-01';

        $formattedStartDate = Carbon::parse($startDate)->format('d-F-Y');
        $formattedEndDate = Carbon::parse($endDate)->format('d-F-Y');

        $months = [
            'start_date' => $formattedStartDate,
            'end_date' => $formattedEndDate,
        ];

        // Log request details
        Log::debug('exportBalanceSheet request: ' . json_encode($request->all()));

        // Fetch income statement data
        $incomeStatement = CoaModel::getSumRequestTrx($request);
        $saldoLaba = $this->countNetIncome($request);

        Log::debug('exportBalanceSheet incomeStatement: ' . json_encode($incomeStatement));

        // Clear the export request from the session
        session()->forget('export_request');

        return Excel::download(new BalanceSheetExport($incomeStatement, $saldoLaba ,$months), 'balance_sheet.xlsx');
    }

    public function generatePdfIncomeStatement(Request $request)
    {
        $requestData = session()->get('export_request');
        if (!$requestData) {
            return back()->with('error', 'Session data is missing or invalid.');
        }
        $request = new Request($requestData);

        $startDate = $request->input('transaction_month_start');
        $endDate = $request->input('transaction_month_end');

        if (!$startDate || !$endDate) {
            return back()->with('error', 'Start date or end date is missing.');
        }

        $transactionPeriod = CarbonPeriod::create($startDate, $endDate);
        $months = collect($transactionPeriod->map(function ($date) {
            return $date->format('d-F-Y');
        }))->unique()->toArray();

        $incomeStatement = CoaModel::getSumRequestTrx($request);

        if (is_null($incomeStatement)) {
            return back()->with('error', 'Failed to retrieve income statement data.');
        }

        $data = [
            'incomeStatement' => $incomeStatement,
            'months' => $months
        ];

        Log::debug('months ' . json_encode($months));

        // Load the test view and pass the data
        $pdf = Pdf::loadView('reporting.laporan_lr_pdf', $data);

        Log::debug('pdf ' . json_encode($pdf));

        session()->forget('export_request');

        return $pdf->download('laporan_lr_pdf.pdf');
    }
}
