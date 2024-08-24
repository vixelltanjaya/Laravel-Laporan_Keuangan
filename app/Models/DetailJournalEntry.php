<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DetailJournalEntry extends Model
{
    use HasFactory;

    protected $table = 'detail_journal_entry';

    protected $fillable = ['entry_id', 'account_id', 'debit', 'credit', 'evidence_image', 'employee_id'];

    public static function joinJournalEntry()
    {
        return DB::table('detail_journal_entry as B')
            ->join('journal_entry as A', 'A.id', '=', 'B.entry_id')
            ->select(
                'A.evidence_code',
                'A.is_reversed',
                'B.entry_id',
                'B.account_id',
                'B.evidence_image',
                'A.description',
                'B.debit',
                'B.credit'
            )
            ->first();
    }

    public static function getTotalAmountsByGroups(array $groups)
    {
        $results = [];

        foreach ($groups as $key => $accountIds) {
            $results[$key] = DB::table('detail_journal_entry as B')
                ->leftJoin('chart_of_account as A', 'A.account_id', '=', 'B.account_id')
                ->select(DB::raw('CASE 
                WHEN "A".account_sign = \'kredit\' THEN SUM("B".credit) - SUM("B".debit)
                ELSE SUM("B".debit) - SUM("B".credit)
            END AS total_amount'))
                ->whereIn('B.account_id', $accountIds)
                ->groupBy('B.account_id', 'A.account_sign')
                ->pluck('total_amount') 
                ->sum(); 
        }

        return $results;
    }
}
