<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class GenerateCodeService {
    public function generateRefCode($prefix_code) {
        $year = date('y'); // Get last two digits of the current year
        $month = date('m'); // Get the current month

        // Get the latest transaction with the same prefix code, year, and month
        $latestTransaction = DB::table('master_transaction')
            ->where('ref', 'like', $prefix_code . '/' . $year . '/' . $month . '/%')
            ->orderBy('ref', 'desc')
            ->first();

        if ($latestTransaction) {
            $latestRef = $latestTransaction->ref;
            $latestNumber = intval(substr($latestRef, -4));
            $newNumber = str_pad($latestNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix_code . '/' . $year . '/' . $month . '/' . $newNumber;
    }
}
