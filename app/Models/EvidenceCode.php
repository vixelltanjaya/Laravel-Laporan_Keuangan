<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvidenceCode extends Model
{
    use HasFactory;

    protected $fillable=['prefix_code','code_title'];
    protected $table='evidence_code';
}
