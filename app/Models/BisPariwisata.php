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
        'no_rangka'];
    protected $table = 'bis_pariwisata';

    public function suratJalan()
    {
        return $this->hasOne(SuratJalan::class);
    }

    public static function joinSuratJalan(){
        // Create query builder
        $query = DB::table('bis_pariwisata as A')
            ->join('surat_jalan as B', 'B.bis_pariwisata_id', '=', 'A.id')
            ->select(
                'A.id',
                'A.plat_nomor',
                'A.karoseri',
                'A.tahun_kendaraan',
                'A.no_rangka',
                'B.evidence_image',
                'B.version'
            );

        return $query->get();
    }
}
