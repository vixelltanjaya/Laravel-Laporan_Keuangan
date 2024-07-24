<?php

namespace App\Http\Controllers;

use App\Models\AccountBalance;
use App\Models\ClosingBalance;
use App\Models\CoaModel;
use App\Models\DetailJournalEntry;
use Carbon\Carbon;
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

        $monthYear = $request->input('month_year');
        if ($monthYear) {
            $monthYear .= '';
        }


        Log::debug('Month Year: ' . $monthYear);

        $sumBalance = CoaModel::sumBalanceCoa($monthYear);

        // Asumsi mengambil nilai pertama dari $sumBalance
        $firstBalance = $sumBalance->first();

        // Pastikan $firstBalance tidak kosong sebelum mengakses propertinya
        $account_id = $firstBalance->account_id ?? null;
        $beginning_balance = $firstBalance->beginning_balance ?? null;
        $debit = $firstBalance->total_debit ?? null;
        $credit = $firstBalance->total_credit ?? null;
        $balance_difference = $firstBalance->balance_difference ?? null;

        Log::debug('sumBalance ' . $sumBalance);
        Log::debug('balance ' . json_encode($firstBalance));

        return view('user-accounting.closed-balance', compact('sumBalance', 'account_id', 'beginning_balance', 'debit', 'credit', 'balance_difference', 'monthYear'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::debug('func store ' . json_encode($request->all()));
        // Validate the request data
        $request->validate([
            'account_id' => 'required|integer',
            'beginning_balance' => 'required|integer',
            'debit' => 'required|integer',
            'credit' => 'required|integer',
            'balance_difference' => 'required|integer',
            'month_year' => 'required|date_format:Y-m',
        ]);

        DB::beginTransaction();

        try {
            $data = $request->only(['account_id', 'beginning_balance', 'debit', 'credit', 'balance_difference', 'month_year']);

            Log::debug('balance ' . json_encode($data));

            $balance_time = \Carbon\Carbon::parse($data['month_year'])->format('Ymd');
            $close_date = now()->format('Y/m/d');

            $balance = new AccountBalance();
            $balance->account_id = $data['account_id'];
            $balance->beginning_balance = $data['beginning_balance'];
            $balance->debit_mutation = $data['debit'];
            $balance->credit_mutation = $data['credit'];
            $balance->ending_balance = $data['balance_difference'];
            $balance->balance_time = $balance_time;
            // $balance->save();

            $closeBalance = new ClosingBalance();
            $closeBalance->balance_time = $balance_time;
            $closeBalance->is_close = 1;
            $closeBalance->close_date = $close_date;
            // $closeBalance->save();


            Log::debug('balance ' . json_encode($balance));
            Log::debug('close balance ' . json_encode($closeBalance));

            DB::commit();

            // Return a response with data for testing
            return response()->json([
                'balance' => $balance,
                'closeBalance' => $closeBalance,
            ]);
            
            // return redirect()->route('closed-balance.index')->with('berhasil', 'Berhasil tutup buku ');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('closed-balance.index')->with('gagal', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
