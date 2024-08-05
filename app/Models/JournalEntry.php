<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class JournalEntry extends Model
{
    use HasFactory;

    protected $table = 'journal_entry';

    protected $fillable = ['description', 'user_id', 'evidence_code', 'entry_date', 'is_reversed', 'reversed_by', 'reversed_at', 'division_id', 'evidence_code_origin'];

    public static function  joinDetailAndUsers($id)
    {
        $journalEntry = DB::table('journal_entry AS A')
            ->select(
                'A.id',
                'A.description',
                'A.user_id',
                'A.evidence_code',
                'A.is_reversed',
                'A.reversed_by',
                'A.reversed_at',
                'C.name AS user_name',
                'A.created_at',
                'A.evidence_code_origin',
                'D.description as pariwisata_description',
                'A.division_id'
            )
            ->leftJoin('users AS C', 'A.user_id', '=', 'C.id')
            ->leftJoin('division AS D', 'A.division_id', '=', 'D.id')
            ->where('A.id', $id)
            ->first();

        // Get detail journal entri
        $details = DB::table('detail_journal_entry AS B')
            ->select(
                'B.id',
                'B.account_id',
                'B.debit',
                'B.credit',
                'B.evidence_image',
                'B.entry_id',
                'A.account_name',
                'A.account_sign'
            )
            ->leftJoin('chart_of_account AS A', 'B.account_id', '=', 'A.account_id')
            ->where('B.entry_id', $id)
            ->orderBy('B.id', 'ASC')
            ->get();

        return (object)[
            'journalEntry' => $journalEntry,
            'details' => $details
        ];
    }

    public static function joinBookingBus($id)
    {
        return DB::table('journal_entry as B')
            ->select([
                'B.id',
                'A.customer_id',
                'A.start_book',
                'A.end_book',
                'B.description',
                'C.name',
                'C.no_telp',
                'D.credit',
                'D.account_id',
                'E.plat_nomor',
            ])
            ->join('booking_bus as A', 'A.journal_entry_id', '=', 'B.id')
            ->join('customer as C', 'C.id', '=', 'A.customer_id')
            ->join('detail_journal_entry as D', 'B.id', '=', 'D.entry_id')
            ->join('bis_pariwisata as E', 'E.id', '=', 'A.bus_pariwisata_id')
            ->where('B.id', $id)
            ->where('D.account_id', '2101')
            ->first();
    }
}
