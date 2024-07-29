<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = ['username', 'email', 'department', 'status'];
    protected $table = 'employees';

    public static function joinPayroll()
    {
        return DB::table('employees as A')
            ->leftJoin('payroll as B', 'B.employees_id', '=', 'A.id')
            ->select('A.username', 'A.department', 'A.status', 'B.updated_at', 'B.gaji', 'B.honor')
            ->get();
    }
}
