<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratJalan extends Model
{
    use HasFactory;

    protected $table = 'surat_jalan';

    protected $fillable = [
        'bis_pariwisata_id',
        'evidence_image',
        'version',
    ];

    public function bisPariwisata()
    {
        return $this->belongsTo(BisPariwisata::class);
    }
}
