<?php

namespace App\Http\Controllers;

use App\Exports\NetIncomeExport;
use App\Models\CoaModel;
use App\Models\DetailJournalEntry;
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
        $netIncomeResults = $this->countNetIncome($request);

        dd($netIncomeResults);

        return view('user-accounting.generate-financial-statement', compact('reportType', 'netIncomeResults'));
    }

    public function countNetIncome($request)
    {
        Log::debug('func count' . json_encode($request->all()));

        $startDate = $request->input('transaction_month_start');
        $endDate = $request->input('transaction_month_end');

        // untuk filter date satu
        if (!$endDate) {
            $endDateToStart = Carbon::now()->startOfMonth()->format('Y-m-01');
        } else {
            $endDateToStart = Carbon::parse($endDate)->startOfMonth()->format('Y-m-01');
        }

        Log::debug('endDateToStart' . json_encode($endDateToStart));
        Log::debug('end date' . json_encode($endDate));

        // Format Year and Month
        $startYear = Carbon::parse($startDate)->year;
        $startMonth = Carbon::parse($startDate)->month;

        $endYear = Carbon::parse($endDate)->year;
        $endMonth = Carbon::parse($endDate)->month;

        // current month
        $startDateParsed = Carbon::parse($endDateToStart);
        $endDateParsed = Carbon::parse($endDate)->endOfDay();
        Log::debug('start date parsed' . json_encode($startDateParsed));
        Log::debug('end date parsed' . json_encode($endDateParsed));

        // Get all transactions
        $incomeStatement = CoaModel::getSumRequestTrx($request);
        $currentMonthIncome = DetailJournalEntry::all();

        // Filter transactions for the entire period
        $incomeStatementPeriod = $incomeStatement->filter(function ($item) use ($startYear, $endYear) {
            $transactionDate = Carbon::parse($item->balance_time);
            return ($transactionDate->year <= $endYear);
        });

        // Filter transactions for the current month
        $incomeStatementMonth = $currentMonthIncome->filter(function ($item) use ($startDateParsed, $endDateParsed) {
            $transactionDate = Carbon::parse($item->updated_at);
            return $transactionDate->between($startDateParsed, $endDateParsed);
        });

        Log::debug('income statement month' . json_encode($incomeStatementMonth));

        // Filter transactions for the current year
        $incomeStatementYear = $incomeStatement->filter(function ($item) use ($startYear) {
            $transactionDate = Carbon::parse($item->balance_time);
            return ($transactionDate->year == $startYear);
        });

        Log::debug('income statement year' . json_encode($incomeStatementYear));

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
            return $item->total_credit - $item->total_debit;
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
    
    $transactionPeriod = CarbonPeriod::create($startDate, $endDate);
    
    $months = collect($transactionPeriod->map(function ($date) {
        return $date->format('F-Y');
    }))->unique()->toArray();

    Log::debug('exportIncomeStatement request: ' . json_encode($request->all()));

    $incomeStatement = CoaModel::getSumRequestTrx($request);
    Log::debug('exportIncomeStatement incomeStatement: ' . json_encode($incomeStatement));

    session()->forget('export_request');

    return Excel::download(new NetIncomeExport($incomeStatement, $months), 'net_income.xlsx');
}


    // public function incomeStatement($startDate, $endDate)
    // {
    //     $formattedStartDate = date('Y-m-d 23:59:59', strtotime($startDate));
    //     $formattedEndDate = date('Y-m-d 23:59:59', strtotime($endDate));

    //     $akunRevenue = CoaMaster::where('kelompok_akun_id', 11)->get();
    //     $akunExpense = CoaMaster::where('kelompok_akun_id', 13)->get();

    //     $totalRevenuePerAkun = [];
    //     $totalExpensePerAkun = [];
    //     $totalRevenue = 0;
    //     $totalExpense = 0;

    //     foreach ($akunRevenue as $akun) {
    //         $revenue = TransactionDetails::whereHas('coa', function ($query) use ($akun) {
    //             $query->where('kelompok_akun_id', 11)
    //                 ->where('id', $akun->id);
    //         });

    //         $revenue->whereHas('transaction', function ($query) use ($formattedStartDate, $formattedEndDate) {
    //             $query->whereBetween('transaction_date', [$formattedStartDate, $formattedEndDate]);
    //         });

    //         $totalRevenuePerAkun[$akun->id] = $revenue->sum('credit');
    //         $totalRevenue += $totalRevenuePerAkun[$akun->id];
    //     }

    //     foreach ($akunExpense as $akun) {
    //         $expense = TransactionDetails::whereHas('coa', function ($query) use ($akun) {
    //             $query->where('kelompok_akun_id', 13)
    //                 ->where('id', $akun->id);
    //         });

    //         $expense->whereHas('transaction', function ($query) use ($formattedStartDate, $formattedEndDate) {
    //             $query->whereBetween('transaction_date', [$formattedStartDate, $formattedEndDate]);
    //         });

    //         $totalExpensePerAkun[$akun->id] = $expense->sum('debit');
    //         $totalExpense += $totalExpensePerAkun[$akun->id];
    //     }

    //     $allAkun = CoaMaster::all();

    //     foreach ($allAkun as $akun) {
    //         if ($akun->akun_induk_terkait) {
    //             if (isset($totalRevenuePerAkun[$akun->id])) {
    //                 if (!isset($totalRevenuePerAkun[$akun->akun_induk_terkait])) {
    //                     $totalRevenuePerAkun[$akun->akun_induk_terkait] = 0;
    //                 }
    //                 $totalRevenuePerAkun[$akun->akun_induk_terkait] += $totalRevenuePerAkun[$akun->id];
    //             }

    //             if (isset($totalExpensePerAkun[$akun->id])) {
    //                 if (!isset($totalExpensePerAkun[$akun->akun_induk_terkait])) {
    //                     $totalExpensePerAkun[$akun->akun_induk_terkait] = 0;
    //                 }
    //                 $totalExpensePerAkun[$akun->akun_induk_terkait] += $totalExpensePerAkun[$akun->id];
    //             }
    //         }
    //     }

    //     $netIncome = $totalRevenue - $totalExpense;

    //     return [
    //         'totalRevenuePerAkun' => $totalRevenuePerAkun,
    //         'totalExpensePerAkun' => $totalExpensePerAkun,
    //         'totalRevenue' => $totalRevenue,
    //         'totalExpense' => $totalExpense,
    //         'netIncome' => $netIncome,
    //     ];
    // }

    // public function balanceSheet(Request $request)
    // {
    //     $endDate = $request->input('end_date');
    //     $startDateCurrentMonth = date('Y-m-01', strtotime($endDate));
    //     $endDateCurrentMonth = date('Y-m-t', strtotime($endDate));

    //     $startOfYear = date('Y-01-01', strtotime($endDate));
    //     $endOfPreviousMonth = date('Y-m-t', strtotime("$endDate -1 month"));

    //     // Calculate laba bulan berjalan (current month income)
    //     $netIncomeCurrentMonthData = $this->incomeStatement($startDateCurrentMonth, $endDateCurrentMonth);
    //     $netIncomeCurrentMonth = $netIncomeCurrentMonthData['netIncome'];

    //     // Calculate laba tahun berjalan (year-to-date income)
    //     $netIncomeYTDData = $this->incomeStatement($startOfYear, $endOfPreviousMonth);
    //     $netIncomeYTD = $netIncomeYTDData['netIncome'];

    //     $akunAktiva = CoaMaster::whereIn('kelompok_akun_id', [1, 2, 3, 4, 5, 6, 7])->get();
    //     $akunPasiva = CoaMaster::whereIn('kelompok_akun_id', [8, 9, 10])->get();

    //     $totalAktivaPerAkun = [];
    //     $totalPasivaPerAkun = [];
    //     $totalAktiva = 0;
    //     $totalPasiva = 0;

    //     foreach ($akunAktiva as $akun) {
    //         $aktiva = TransactionDetails::whereHas('coa', function ($query) use ($akun) {
    //             $query->whereIn('kelompok_akun_id', [1, 2, 3, 4, 5, 6, 7])
    //                 ->where('id', $akun->id);
    //         });

    //         if ($endDate) {
    //             $aktiva->whereHas('transaction', function ($query) use ($endDate) {
    //                 $query->whereDate('transaction_date', '<=', $endDate);
    //             });
    //         }

    //         $totalAktivaPerAkun[$akun->id] = $akun->saldo_berjalan + $aktiva->sum('debit') - $aktiva->sum('credit');
    //         $totalAktiva += $totalAktivaPerAkun[$akun->id];
    //     }

    //     foreach ($akunPasiva as $akun) {
    //         $pasiva = TransactionDetails::whereHas('coa', function ($query) use ($akun) {
    //             $query->whereIn('kelompok_akun_id', [8, 9, 10])
    //                 ->where('id', $akun->id);
    //         });

    //         if ($endDate) {
    //             $pasiva->whereHas('transaction', function ($query) use ($endDate) {
    //                 $query->whereDate('transaction_date', '<=', $endDate);
    //             });
    //         }

    //         $totalPasivaPerAkun[$akun->id] = $akun->saldo_berjalan + $pasiva->sum('credit') - $pasiva->sum('debit');
    //         $totalPasiva += $totalPasivaPerAkun[$akun->id];
    //     }

    //     // Summarize balances from child accounts to their parent accounts
    //     $allAkun = CoaMaster::all();

    //     foreach ($allAkun as $akun) {
    //         if ($akun->akun_induk_terkait) {
    //             if (isset($totalAktivaPerAkun[$akun->id])) {
    //                 if (!isset($totalAktivaPerAkun[$akun->akun_induk_terkait])) {
    //                     $totalAktivaPerAkun[$akun->akun_induk_terkait] = 0;
    //                 }
    //                 $totalAktivaPerAkun[$akun->akun_induk_terkait] += $totalAktivaPerAkun[$akun->id];
    //             }

    //             if (isset($totalPasivaPerAkun[$akun->id])) {
    //                 if (!isset($totalPasivaPerAkun[$akun->akun_induk_terkait])) {
    //                     $totalPasivaPerAkun[$akun->akun_induk_terkait] = 0;
    //                 }
    //                 $totalPasivaPerAkun[$akun->akun_induk_terkait] += $totalPasivaPerAkun[$akun->id];
    //             }
    //         }
    //     }

    //     // Calculate retained earnings (laba ditahan)

    //     return view('report.balance-sheet.index', [
    //         'totalAktivaPerAkun' => $totalAktivaPerAkun,
    //         'totalPasivaPerAkun' => $totalPasivaPerAkun,
    //         'totalAktiva' => $totalAktiva,
    //         'totalPasiva' => $totalPasiva,
    //         'netIncomeCurrentMonth' => $netIncomeCurrentMonth,
    //         'netIncomeYTD' => $netIncomeYTD,
    //         'endDate' => $endDate,
    //         'akunAktiva' => $akunAktiva,
    //         'akunPasiva' => $akunPasiva,
    //     ]);
    // }

}
