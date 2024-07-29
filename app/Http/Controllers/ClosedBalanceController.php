<?php

namespace App\Http\Controllers;

use App\Models\AccountBalance;
use App\Models\ClosingBalance;
use App\Models\CoaModel;
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


        Log::debug('Month Year: ' .json_encode($request->all()));

        $sumBalance = CoaModel::sumBalanceCoa($request);

        // Asumsi mengambil nilai pertama dari $sumBalance
        $checkMonth = $request->input('month_year');

        // $account_id = $firstBalance->account_id ?? null;
        // $beginning_balance = $firstBalance->beginning_balance ?? null;
        // $debit = $firstBalance->total_debit ?? null;
        // $credit = $firstBalance->total_credit ?? null;
        // $balance_difference = $firstBalance->balance_difference ?? null;

        Log::debug('sumBalance ' . $sumBalance);
        // Log::debug('balance ' . json_encode($firstBalance));

        return view('user-accounting.closed-balance', compact(['sumBalance' ]));
    }

    public function store(Request $request)
    {
        Log::debug('func store ' . json_encode($request->all()));
    
        // Validate the request data for each set of balances
        $request->validate([
            'balances.*.account_id' => 'required|integer',
            'balances.*.beginning_balance' => 'required|integer',
            'balances.*.debit' => 'required|integer',
            'balances.*.credit' => 'required|integer',
            'balances.*.balance_difference' => 'required|integer',
            'balances.*.month_year' => 'required|date_format:Y-m',
        ]);
    
        DB::beginTransaction();
    
        try {
            $balancesData = $request->input('balances');
    
            if (is_null($balancesData)) {
                throw new Exception('Balances data is null');
            }
    
            Log::debug('Balances data: ' . json_encode($balancesData));
    
            $balanceTimeArray = [];
    
            foreach ($balancesData as $data) {
                Log::debug('pemrosesan balance data: ' . json_encode($data));
    
                $monthYear = $data['month_year'];
                $balance_time = Carbon::parse($monthYear)->lastOfMonth()->format('Y-m-d');
                $close_date = now()->format('Y/m/d');
    
                $balance = new AccountBalance();
                $balance->account_id = $data['account_id'];
                $balance->beginning_balance = $data['beginning_balance'];
                $balance->debit_mutation = $data['debit'];
                $balance->credit_mutation = $data['credit'];
                $balance->ending_balance = $data['balance_difference'];
                $balance->balance_time = $balance_time;
                // $balance->save();
    
                $balanceTimeArray[] = $balance_time;
    
                Log::debug('Balance stored: ' . json_encode($balance));
            }
    
            // Ensure unique balance times for closing balance entries
            $uniqueBalanceTimes = array_unique($balanceTimeArray);
    
            foreach ($uniqueBalanceTimes as $balance_time) {
                $closeBalance = new ClosingBalance();
                $closeBalance->balance_time = $balance_time;
                $closeBalance->is_close = 1;
                $closeBalance->close_date = $close_date;
                // $closeBalance->save();
    
                Log::debug('Close balance stored: ' . json_encode($closeBalance));
            }
    
            DB::rollBack();
    
            return redirect()->route('closed-balance.index')->with('berhasil', 'Pembukuan berhasil ditutup');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error storing balances: ' . $e->getMessage());
            return redirect()->route('closed-balance.index')->with('gagal', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
}
