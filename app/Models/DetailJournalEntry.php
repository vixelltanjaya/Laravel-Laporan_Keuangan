<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DetailJournalEntry extends Model
{
    use HasFactory;

    protected $table = 'detail_journal_entry';

    protected $fillable = ['entry_id', 'account_id', 'debit', 'credit', 'evidence_image'];

    public static function joinJournalEntry(){
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
}
