<?php

namespace App\Http\Controllers;

use App\Models\AccountBalance;
use App\Models\ClosingBalance;
use App\Models\CoaModel;
use App\Models\DetailJournalEntry;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClosedBalanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Log::debug('Month Year: ' . json_encode($request->all()));

        $sumBalance = CoaModel::sumBalanceCoa($request);
        $selectedMonthYear = $request->input('month_year');

        // Initialize variabel
        $selectedMonthYearFormatted = null;
        $isPastOneMonth = false;


        if ($selectedMonthYear) {
            try {
                if (Carbon::hasFormat($selectedMonthYear, 'Y-m')) {
                    $selectedMonthYearFormatted = Carbon::createFromFormat('Y-m', $selectedMonthYear)->format('Ymd');
                    $isPastOneMonth = Carbon::createFromFormat('Ymd', $selectedMonthYearFormatted)->lt(Carbon::now()->subMonth());
                } else {
                    throw new Exception("Invalid date format: " . $selectedMonthYear);
                }
            } catch (Exception $e) {
                Log::error('Date format error: ' . $e->getMessage());
                $selectedMonthYearFormatted = Carbon::now()->format('Ymd');
            }
        }

        // hitung total sum
        $totalSum = $sumBalance->map(function ($balance) {
            if (strtolower($balance->account_sign) === 'debit') {
                return $balance->beginning_balance_next_month + $balance->total_debit - $balance->total_credit;
            } else {
                return $balance->beginning_balance_next_month + $balance->total_credit - $balance->total_debit;
            }
        })->sum();

        $isClose = $this->getIsCloseStatus($selectedMonthYearFormatted);

        Log::debug('selectedMonthYearFormatted ' . json_encode($selectedMonthYearFormatted));
        Log::debug('isClose ' . json_encode($isClose));

        return view('user-accounting.closed-balance', compact('sumBalance', 'isClose', 'isPastOneMonth', 'totalSum'));
    }


    protected $financialStatementController;

    public function __construct(GenerateFinancialStatementController $financialStatementController)
    {
        $this->financialStatementController = $financialStatementController;
    }

    public function store(Request $request)
    {
        Log::debug('func store ' . json_encode($request->all()));

        $request->validate([
            'balances.*.account_id' => 'required|integer',
            'balances.*.beginning_balance_next_month' => 'required|numeric',
            'balances.*.debit' => 'required|numeric',
            'balances.*.credit' => 'required|numeric',
            'balances.*.balance_difference' => 'required|numeric',
            'balances.*.month_year' => 'required|date_format:Y-m',
        ]);

        DB::beginTransaction();

        try {
            $balancesData = $request->input('balances');

            if (is_null($balancesData)) {
                throw new Exception('Balances data is null');
            }

            $netIncomeResults = $this->financialStatementController->countNetIncome($request);

            Log::debug('Balances data: ' . json_encode($balancesData));
;
            $this->updateOrCreateBalance(3300, $netIncomeResults['netIncomeCurrentMonth']);
            $this->updateOrCreateBalance(3200, $netIncomeResults['netIncomeYTD']);
            $this->updateOrCreateBalance(3100, $netIncomeResults['netIncome']);

            $balanceTimeArray = [];

            foreach ($balancesData as $data) {
                $monthYear = $data['month_year'];
                $balance_time = Carbon::parse($monthYear)->lastOfMonth()->format('Ymd');
                $close_date = now()->format('Y/m/d');

                $balance = new AccountBalance();
                $balance->account_id = $data['account_id'];
                $balance->beginning_balance = $data['beginning_balance_next_month'];
                $balance->debit_mutation = $data['debit'];
                $balance->credit_mutation = $data['credit'];
                $balance->ending_balance = $data['balance_difference'];
                $balance->balance_time = $balance_time;
                $balance->save();

                $balanceTimeArray[] = $balance_time;
            }
            Log::debug('Balance stored: ' . json_encode($balance->toArray()));

            // Ensure unique balance times for closing balance entries
            $uniqueBalanceTimes = array_unique($balanceTimeArray);

            foreach ($uniqueBalanceTimes as $balance_time) {
                $closeBalance = new ClosingBalance();
                $closeBalance->balance_time = $balance_time;
                $closeBalance->is_close = 1;
                $closeBalance->close_date = $close_date;
                $closeBalance->save();
            }
            Log::debug('Close balance stored: ' . json_encode($closeBalance->toArray()));

            DB::commit();

            return redirect()->route('closed-balance.index')->with('berhasil', 'Pembukuan berhasil ditutup');
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Error storing balances: ' . $e->getMessage());
            return redirect()->route('closed-balance.index')->with('gagal', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    protected function updateOrCreateBalance($accountId, $beginningBalance)
    {
        $balance = AccountBalance::where('account_id', $accountId)
            ->where('balance_time', Carbon::now()->format('Ymd')) 
            ->first();

        Log::debug('beginning balance: ' . json_encode($beginningBalance));
        Log::debug('balance update or create balance: ' . json_encode($balance));

        if ($balance) {
            $balance->beginning_balance = $beginningBalance;
            $balance->save();
        } else {
            $balance = new AccountBalance();
            $balance->account_id = $accountId;
            $balance->beginning_balance = $beginningBalance;
            $balance->debit_mutation = 0;
            $balance->credit_mutation = 0;
            $balance->ending_balance = $beginningBalance;
            $balance->balance_time = Carbon::now()->endOfMonth()->format('Ymd');
            $balance->save();
        }

        Log::debug('Account balance updated/created: ' . json_encode($balance->toArray()));
    }

    private function getIsCloseStatus($formattedMonthYear)
    {
        $isClose = ClosingBalance::where('balance_time', $formattedMonthYear)
            ->where('is_close', 1)
            ->exists();

        Log::debug('Close status for ' . $formattedMonthYear . ': ' . ($isClose ? 'Closed' : 'Open'));

        return $isClose;
    }


}
