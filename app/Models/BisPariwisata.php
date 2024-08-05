<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BisPariwisata extends Model
{
    use HasFactory;

    protected $fillable = [
        'plat_nomor',
        'tahun_kendaraan',
        'karoseri',
        'no_rangka',
        'account_id',
        'evidence_image_bus',
        'selling_price'
    ];
    protected $table = 'bis_pariwisata';

    public function suratJalan()
    {
        return $this->hasOne(SuratJalan::class);
    }

    public static function joinSuratJalan()
    {
        // Subquery to get the latest surat_jalan record for each bis_pariwisata_id
        $latestSuratJalan = DB::table('surat_jalan as B')
            ->select('B.id', 'B.bis_pariwisata_id', 'B.evidence_image', 'B.version')
            ->whereRaw('"B"."updated_at" = (SELECT MAX(updated_at) FROM surat_jalan WHERE "bis_pariwisata_id" = "B"."bis_pariwisata_id")');

        // Main query to join bis_pariwisata with the latest surat_jalan records
        $query = DB::table('bis_pariwisata as A')
            ->joinSub($latestSuratJalan, 'B', function ($join) {
                $join->on('A.id', '=', 'B.bis_pariwisata_id');
            })
            ->select(
                'A.id',
                'A.plat_nomor',
                'A.karoseri',
                'A.tahun_kendaraan',
                'A.no_rangka',
                'A.selling_price',
                'B.evidence_image',
                'B.version',
                'A.account_id',
                'A.evidence_image_bus'
            );

        return $query->get();
    }

    public static function joinCoa()
    {

        return DB::table('bis_pariwisata as A')
            ->join('chart_of_account as B', 'A.account_id', '=', 'B.account_id')
            ->select('A.account_id', 'B.account_name', 'B.account_id', 'A.id')
            ->get();
    }

    public static function getSalesOnBothLine()
    {
        return DB::table('chart_of_account as A')
            ->leftJoin('detail_journal_entry as B', 'A.account_id', '=', 'B.account_id')
            ->leftJoin('journal_entry as C', 'C.id', '=', 'B.entry_id')
            ->select(
                'A.account_id',
                'A.account_name',
                DB::raw('SUM("B".credit) as total_credit')
            )
            ->where(DB::raw('SUBSTRING("A".account_name, 1, 14)'), '=', 'Pendapatan Bis')
            ->groupBy('A.account_id', 'A.account_name')
            ->get();
    }
}
