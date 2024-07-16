<?php

namespace App\Http\Controllers;

use App\Models\CoaModel;
use App\Models\Division;
use App\Models\EvidenceCode;

class AddMasterJournalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $EvidenceCode = EvidenceCode::all();
        $chartOfAccount = CoaModel::orderBy('account_id','asc')->get( );
        $division = Division::all();
        return view('user-accounting.add-master-journal', [
            'chartOfAccounts' => $chartOfAccount,
            'EvidenceCode' => $EvidenceCode,
            'division' => $division,
        ]);
    }
}
