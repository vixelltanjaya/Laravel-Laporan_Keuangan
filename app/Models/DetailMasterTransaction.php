<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DetailMasterTransaction extends Model
{
    use HasFactory;

    
    protected $fillable = ['master_code','gl_account','account_position','created_at','updated_at'];
    protected $table = 'detail_master_transaction';

    public static function joinJournalTrx(){
        $detailMasterTransaction = DB::table('detail_master_transaction as A')
            ->join('master_transaction as B', 'B.code', '=', 'A.master_code')
            ->join('evidence_code as C', 'C.id', '=', 'B.evidence_id')
            ->join('chart_of_account as D', 'D.account_id', '=', 'A.gl_account')
            ->orderBy('A.gl_account','asc')
            ->select('A.master_code', 'A.gl_account', 'A.account_position', 'C.prefix_code', 'B.description', 'B.id', 'D.account_name')
            ->get();
        return $detailMasterTransaction;
    }
}


