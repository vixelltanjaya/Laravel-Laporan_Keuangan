<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CoaModel extends Model
{
    use SoftDeletes;

    protected $fillable = ['account_id', 'account_name', 'account_sign', 'account_type', 'account_group'];
    protected $table = 'chart_of_account';


    public static function sumBalanceCoa($monthYear = null)
    {
        // If month_year is provided, parse it into year and month
        if ($monthYear) {
            $yearMonth = explode('-', $monthYear);
            $year = $yearMonth[0];
            $month = $yearMonth[1];

            // Create the raw SQL expression as a string
            $dateFilter = "DATE_TRUNC('month', \"B\".\"created_at\") = DATE_TRUNC('month', CAST('$year-$month-01' AS DATE))";
        } else {
            // If no month_year is provided, use a filter that always evaluates to true
            $dateFilter = '1 = 1'; // Always true
        }

        Log::debug('datefilter ' .json_encode($dateFilter));

        // Build the query using the raw SQL expression
        $query = DB::table('chart_of_account as A')
            ->leftJoin('detail_journal_entry as B', 'A.account_id', '=', 'B.account_id')
            ->leftJoin('account_balance as C', 'A.account_id', '=', 'C.account_id')
            ->leftJoin('journal_entry as D', 'B.entry_id', '=', 'D.id')
            ->where('D.is_reversed', '<>', 1)
            ->whereRaw($dateFilter) // Use whereRaw with a string
            ->select(
                'A.account_id',
                'A.account_name',
                DB::raw('COALESCE("C"."beginning_balance", 0) AS "beginning_balance"'),
                DB::raw('COALESCE(SUM(CAST("B"."debit" AS NUMERIC)), 0) AS "total_debit"'),
                DB::raw('COALESCE(SUM(CAST("B"."credit" AS NUMERIC)), 0) AS "total_credit"'),
                DB::raw('CASE
                    WHEN "A"."account_sign" = \'Debit\' OR "A"."account_sign" = \'debit\' THEN 
                        (COALESCE("C"."beginning_balance", 0) + COALESCE(SUM(CAST("B"."debit" AS NUMERIC)), 0) - COALESCE(SUM(CAST("B"."credit" AS NUMERIC)), 0)) 
                    WHEN "A"."account_sign" = \'Kredit\' OR "A"."account_sign" = \'kredit\' THEN 
                        (COALESCE("C"."beginning_balance", 0) - COALESCE(SUM(CAST("B"."debit" AS NUMERIC)), 0) + COALESCE(SUM(CAST("B"."credit" AS NUMERIC)), 0)) 
                    ELSE 
                        0 
                END AS "balance_difference"')
            )
            ->groupBy('A.account_id', 'A.account_name', 'C.beginning_balance', 'A.account_sign')
            ->orderBy('A.account_id', 'ASC');

            Log::debug('dapatkan query ' .json_encode($query->toSql()));

        // Execute the query and return the results
        return $query->get();
    }

    public static function joinAccountBalance()
    {
        return DB::table('chart_of_account as A')
            ->leftJoin('account_balance as B', 'A.account_id', '=', 'B.account_id')
            ->select(
                'A.account_id',
                'A.account_name',
                'A.account_sign',
                'A.account_type',
                'A.account_group',
                DB::raw('COALESCE("B"."debit_mutation", 0) AS debit_mutation'),
                DB::raw('COALESCE("B"."credit_mutation", 0) AS credit_mutation'),
                DB::raw('COALESCE("B"."ending_balance", 0) AS ending_balance')
            )
            ->orderBy('A.account_id', 'ASC')
            ->get();
    }
}
