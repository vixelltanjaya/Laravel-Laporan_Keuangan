<?php

namespace App\Http\Controllers;

use App\Models\AccountBalance;
use App\Models\CoaModel;
use App\Models\JournalEntry;
use Illuminate\Http\Request;

class GeneralLedgerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $chartOfAccounts = CoaModel::all();
        $journalEntries = JournalEntry::all();
        $accountBalances = AccountBalance::all();

        return view('user-accounting.general-ledger',[
            'chartOfAccounts' => $chartOfAccounts,
            'journalEntries' => $journalEntries,
            'accountBalances' => $accountBalances,
        ]);
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
        //
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
