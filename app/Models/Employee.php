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
        $latestUpdates = DB::table('payroll')
            ->select('employees_id', DB::raw('MAX(updated_at) as latest_updated_at'))
            ->groupBy('employees_id');

        return DB::table('employees as e')
            ->leftJoinSub($latestUpdates, 'latest_payroll', function ($join) {
                $join->on('e.id', '=', 'latest_payroll.employees_id');
            })
            ->leftJoin('payroll as p', function ($join) {
                $join->on('e.id', '=', 'p.employees_id')
                    ->on('p.updated_at', '=', 'latest_payroll.latest_updated_at');
            })
            ->select('e.id', 'e.username', 'p.gaji', 'p.id as payroll_id', 'p.employees_id', 'p.updated_at');


    }
}
