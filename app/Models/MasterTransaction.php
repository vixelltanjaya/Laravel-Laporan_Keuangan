<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterTransaction extends Model
{
    use HasFactory;
    protected $fillable = ['code','description','evidence_id'];
    protected $table = 'master_transaction';


    public static function joinEvidenceCode(){
        
            return DB::table('master_transaction as A')
                ->join('evidence_code as B', 'B.id', '=', 'A.evidence_id')
                ->select('A.id', 'A.code', 'B.prefix_code', 'A.description');
    } 

    public static function joinDetailMasterTransaction($code){
        return DB::table('master_transaction as A')
            ->select('A.id', 'A.code', 'A.description', 'A.evidence_id', 'B.gl_account', 'B.account_position', 'C.account_name')
            ->join('detail_master_transaction as B', 'A.code', '=', 'B.master_code')
            ->join('chart_of_account as C', 'C.account_id', '=', 'B.gl_account')
            ->where('A.code','=',$code)
            ->get();
    }
}
