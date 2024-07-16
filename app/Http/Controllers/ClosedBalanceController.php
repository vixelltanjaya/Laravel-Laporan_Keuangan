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
        Log::debug('Request parameters: ' . json_encode($request->all())); 

        $monthYear = $request->input('month_year');
        Log::debug('Month Year: ' . $monthYear); 

        $sumBalance = CoaModel::sumBalanceCoa($monthYear);
        return view('user-accounting.closed-balance', compact('sumBalance'));
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
        // Validate the request data
        $request->validate([
            'account_id' => 'required|integer',
            'beginning_balance' => 'required|integer',
            'debit' => 'required|integer',
            'credit' => 'required|integer',
            'ending_balance' => 'required|integer',
            'balance_time' => 'required|integer',
            'month_year' => 'required|date_format:Y-m',
        ]);

        DB::beginTransaction();

        try {
            $data = $request->only(['account_id', 'beginning_balance', 'debit', 'credit', 'ending_balance', 'balance_time', 'month_year']);

            $balance = new AccountBalance();
            $balance->account_id = $data['account_id'];
            $balance->beginning_balance = $data['beginning_balance'];
            $balance->debit = $data['debit'];
            $balance->credit = $data['credit'];
            $balance->ending_balance = $data['ending_balance'];
            $balance->balance_time = $data['balance_time'];
            $balance->month_year = $data['month_year'];
            $balance->save();

            // Commit the transaction
            DB::commit();

            // Redirect or return a response
            return redirect()->route('closed-balance.index')->with('berhasil', 'Saldo berhasil ditutup!');
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
