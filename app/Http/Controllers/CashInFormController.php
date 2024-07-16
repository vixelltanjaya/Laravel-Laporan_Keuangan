<?php

namespace App\Http\Controllers;

use App\Models\CoaModel;
use App\Models\DetailMasterTransaction;
use App\Models\MasterTransaction;

class CashInFormController extends Controller
{
    public function index()
    {

        $coa = CoaModel::orderBy('account_id','asc')->get();

        $masterTransaction = MasterTransaction::joinEvidenceCode()
        ->where('prefix_code', '!=', 'BKK')
        ->get();
        $detailMasterTransaction = DetailMasterTransaction::joinJournalTrx();

        return view('user-accounting.cash-in-form', [
            'chartOfAccounts' => $coa,
            'detailMasterTransaction' => $detailMasterTransaction,
            'masterTransaction' => $masterTransaction,
        ]);
    }

}
