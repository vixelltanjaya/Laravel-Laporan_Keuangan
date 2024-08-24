<?php

namespace App\Http\Controllers;

use App\Exports\BalanceSheetExport;
use App\Exports\NetIncomeExport;
use App\Exports\PerubahanModalExport;
use App\Models\AccountBalance;
use App\Models\CoaModel;
use App\Models\DetailJournalEntry;
use App\Models\JournalEntry;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
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

        $startDate = $request->input('transaction_month_start');
        $endDate = $request->input('transaction_month_end');

        session()->put('export_request', $request->all());

        $formattedStartDate = $startDate ? date('d F Y', strtotime($startDate . '-01')) : '';
        $formattedEndDate = $endDate ? date('d F Y', strtotime($endDate . '-01')) : '';
        // Pass the $request object directly to the function
        $incomeStatement = CoaModel::getSumRequestTrx($request);
        Log::debug('income incomeStatement: ' . json_encode($incomeStatement));

        $totalPendapatan = $incomeStatement->filter(function ($item) {
            return strtolower($item->account_type) === 'pendapatan';
        })->sum('total_amount');

        $totalBeban = $incomeStatement->filter(function ($item) {
            return strtolower($item->account_type) === 'beban';
        })->sum('total_amount');

        $labaBersih = $totalPendapatan - $totalBeban;

        return view('user-accounting.generate-financial-statement', compact([
            'reportType',
            'incomeStatement',
            'totalPendapatan',
            'totalBeban',
            'labaBersih',
            'formattedStartDate',
            'formattedEndDate'
        ]));
    }
    public function balance(Request $request)
    {
        Log::debug('req id ' . json_encode($request->all()));

        $reportType = 'balance';
        session()->put('export_request', $request->all());
        $balanceSheet = CoaModel::getSumRequestTrx($request);
        $netIncomeResults = $this->countNetIncome($request);

        $endDate = $request->input('transaction_month_end');
        $formattedEndDate = $endDate ? date('d F Y', strtotime($endDate . '-01')) : '';
        $formattedMonth = $endDate ? date('F Y') : '';

        $totalPendapatanDanEkuitas = $balanceSheet->filter(function ($item) {
            return strtolower($item->account_type) === 'ekuitas' || strtolower($item->account_type) === 'pendapatan';
        })->sum('total_amount');

        $totalBeban = $balanceSheet->filter(function ($item) {
            return strtolower($item->account_type) === 'beban';
        })->sum('total_amount');

        $totalKewajiban = $balanceSheet->filter(function ($item) {
            return strtolower($item->account_type) === 'kewajiban';
        })->sum('total_amount');

        $netAmount = $totalPendapatanDanEkuitas - $totalBeban;
        $totalKewajibanDanEkuitas = $totalPendapatanDanEkuitas - $totalBeban + $totalKewajiban;

        return view('user-accounting.generate-financial-statement', [
            'reportType' => $reportType,
            'balanceSheet' => $balanceSheet,
            'labaTahunBerjalan' => $netIncomeResults,
            'netAmount' => $netAmount,
            'totalKewajibanDanEkuitas' => $totalKewajibanDanEkuitas,
            'formattedEndDate' => $formattedEndDate,
            'formattedMonth' => $formattedMonth
        ]);
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
        if (is_null($requestData)) {
            return redirect()->back()->with('gagal', 'Terjadi kesalahan. Silakan refresh halaman dan coba lagi.');
        }

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
        return Excel::download(new NetIncomeExport($incomeStatement, $months), 'net_income.xlsx');
    }
    public function exportPerubahanModal(Request $request)
    {
        $requestData = session()->get('export_request');
        if (is_null($requestData)) {
            return redirect()->back()->with('gagal', 'Terjadi kesalahan. Silakan refresh halaman dan coba lagi.');
        }

        $request = new Request($requestData);

        Log::debug('export balance sheet request berdasar filter index' . json_encode($request));
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
        Log::debug('export perubahan modal request: ' . json_encode($request->all()));

        // Fetch income statement data
        $incomeStatement = CoaModel::getSumRequestTrx($request);
        $saldoLaba = $this->countNetIncome($request);

        Log::debug('export perubahan modal incomeStatement: ' . json_encode($incomeStatement));

        return Excel::download(new PerubahanModalExport($incomeStatement, $saldoLaba, $months), 'perubahan_modal.xlsx');
    }
    public function generatePdfIncomeStatement(Request $request)
    {
        $requestData = session()->get('export_request');
        if (is_null($requestData)) {
            return redirect()->back()->with('gagal', 'Terjadi kesalahan. Silakan refresh halaman dan coba lagi.');
        }

        try {

            $request = new Request($requestData);

            $startDate = $request->input('transaction_month_start');
            $endDate = $request->input('transaction_month_end');

            if (!$startDate || !$endDate) {
                return back()->with('error', 'Start date or end date is missing.');
            }

            $formattedStartDate = $startDate ? date('d F Y', strtotime($startDate . '-01')) : '';
            $formattedEndDate = $endDate ? date('d F Y', strtotime($endDate . '-01')) : '';

            $incomeStatement = CoaModel::getSumRequestTrx($request);
            Log::debug('income incomeStatement: ' . json_encode($incomeStatement));

            if (is_null($incomeStatement)) {
                throw new Exception('Failed to retrieve income statement data.');
            }

            $totalPendapatan = $incomeStatement->filter(function ($item) {
                return strtolower($item->account_type) === 'pendapatan';
            })->sum('total_amount');

            $totalBeban = $incomeStatement->filter(function ($item) {
                return strtolower($item->account_type) === 'beban';
            })->sum('total_amount');

            $labaBersih = $totalPendapatan - $totalBeban;

            $data = [
                'incomeStatement' => $incomeStatement,
                'formattedStartDate' => $formattedStartDate,
                'formattedEndDate' => $formattedEndDate,
                'totalPendapatan' => $totalPendapatan,
                'totalBeban' => $totalBeban,
                'labaBersih' => $labaBersih,
            ];

            Log::debug('data ' . json_encode($data));

            // Load the view and pass the data
            $pdf = Pdf::loadView('reporting.laporan-laba-rugi', $data);
            return $pdf->download('laporan_lr_pdf.pdf');
        } catch (Exception $e) {
            Log::error('Error ' . $e->getMessage());
            return back()->with('gagal', 'Silakan refresh halaman');
        }
    }
    public function generatePdfBalanceSheet(Request $request)
    {
        $requestData = session()->get('export_request');
        if (is_null($requestData)) {
            return redirect()->back()->with('gagal', 'Terjadi kesalahan. Silakan refresh halaman dan coba lagi.');
        }

        $request = new Request($requestData);

        Log::debug('cek request' . json_encode($request->all()));

        $endDate = $request->input('transaction_month_end');

        $formattedEndDate = $endDate ? date('d F Y', strtotime($endDate . '-01')) : '';
        $formattedMonth = $endDate ? date('F Y') : '';

        $balanceSheet = CoaModel::getSumRequestTrx($request);
        $netIncomeResults = $this->countNetIncome($request);

        $totalPendapatanDanEkuitas = $balanceSheet->filter(function ($item) {
            return strtolower($item->account_type) === 'ekuitas' || strtolower($item->account_type) === 'pendapatan';
        })->sum('total_amount');

        $totalBeban = $balanceSheet->filter(function ($item) {
            return strtolower($item->account_type) === 'beban';
        })->sum('total_amount');

        $totalKewajiban = $balanceSheet->filter(function ($item) {
            return strtolower($item->account_type) === 'kewajiban';
        })->sum('total_amount');

        $netAmount = $totalPendapatanDanEkuitas - $totalBeban;
        $totalKewajibanDanEkuitas = $totalPendapatanDanEkuitas - $totalBeban + $totalKewajiban;

        $data = [
            'balanceSheet' => $balanceSheet,
            'formattedEndDate' => $formattedEndDate,
            'formattedMonth' => $formattedMonth,
            'labaTahunBerjalan' => $netIncomeResults,
            'totalBeban' => $totalBeban,
            'totalPendapatanDanEkuitas' => $totalPendapatanDanEkuitas,
            'totalKewajiban' => $totalKewajiban,
            'netAmount' => $netAmount,
            'totalKewajibanDanEkuitas' => $totalKewajibanDanEkuitas,
        ];

        Log::debug('data ' . json_encode($data));

        // Load the test view and pass the data
        $pdf = Pdf::loadView('reporting.laporan-posisi-keuangan', $data);
        return $pdf->download('laporan_posisi_keuangan_pdf.pdf');
    }
    
    private function formatAmount($amount)
    {
        return number_format($amount, 0, ',', '.');
    }
}
