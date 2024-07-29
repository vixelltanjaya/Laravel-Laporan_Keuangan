<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class payroll extends Model
{
    use HasFactory;
    protected $fillable = ['gaji','honor'];
    protected $table = 'payroll';

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
