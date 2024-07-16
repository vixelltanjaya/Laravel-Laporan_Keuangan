<?php

namespace App\Http\Controllers;

use App\Models\CoaModel;
use App\Models\DetailMasterTransaction;
use App\Models\MasterTransaction;
use App\Services\GenerateCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PgSql\Lob;

class cashOutFormController extends Controller
{

    public function index()
    {

        $coa = CoaModel::all();
        $masterTransaction = MasterTransaction::joinEvidenceCode()
        ->where('prefix_code', '!=', 'BKM')
        ->get();

        $detailMasterTransaction = DetailMasterTransaction::joinJournalTrx();

        return view('user-accounting.cash-out-form', [
            'chartOfAccounts' => $coa,
            'detailMasterTransaction' => $detailMasterTransaction,
            'masterTransaction' => $masterTransaction,
        ]);
    }
}
