<?php

namespace App\Http\Controllers;

use App\Models\CoaModel;
use App\Models\DetailMasterTransaction;
use App\Models\Division;
use App\Models\EvidenceCode;
use App\Models\MasterTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EditMasterJournalController extends Controller
{
    public function index($code)
    {
        Log::info('Masuk ke function Index');

        $detailJournal = MasterTransaction::joinDetailMasterTransaction($code);
  
        $masterJournal = MasterTransaction::where('code', $code)->firstOrFail();
        $chartOfAccounts = CoaModel::all();
        $division = Division::all();
        $EvidenceCode = EvidenceCode::all();

        Log::info('Divisi ID dari MasterJournal: ' . $masterJournal->business_type_id);
        Log::info('evidence ID dari MasterJournal: ' . $masterJournal->evidence_id);

        // Log::info('Chart of Accounts: ' . json_encode($chartOfAccounts));
        // Log::info('CODE: ' . json_encode($code));


        return view('user-accounting.edit-master-journal', [
            'masterJournal' => $masterJournal,
            'chartOfAccounts' => $chartOfAccounts,
            'division' => $division,
            'EvidenceCode' => $EvidenceCode,
            'detailJournal' => $detailJournal
        ]);
    }

}
