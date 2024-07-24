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
        if ($monthYear) {
            $yearMonth = explode('-', $monthYear);
            $year = $yearMonth[0];
            $month = $yearMonth[1];
            $dateFilter = "DATE_TRUNC('month', \"B\".\"created_at\") = DATE_TRUNC('month', CAST('$year-$month-01' AS DATE))";
        } else {
            $dateFilter = '1 = 1'; // Always true
        }

        Log::debug('datefilter ' . json_encode($dateFilter));

        // Build the query using the raw SQL expression
        $query = DB::table('chart_of_account as A')
            ->leftJoin('detail_journal_entry as B', 'A.account_id', '=', 'B.account_id')
            ->leftJoin('account_balance as C', 'A.account_id', '=', 'C.account_id')
            ->leftJoin('journal_entry as D', 'B.entry_id', '=', 'D.id')
            ->whereRaw($dateFilter)
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

        Log::debug('dapatkan query ' . json_encode($query->toSql()));

        return $query->get();
    }

    // func nya masih perlu diedit
    public static function getIncomeStatement($request)
    {
        $accountIdStartsWith = ['4', '5'];
        $transactionMonthStart = $request->input('transaction_month_start');
        $transactionMonthEnd = $request->input('transaction_month_end');

        $formattedStartDate = date('Y-m-d 00:00:00', strtotime($transactionMonthStart . '-01'));
        $formattedEndDate = date('Y-m-d 23:59:59', strtotime("last day of $transactionMonthEnd"));

        $divisionId = $request->input('division_id');

        $query = DB::table('chart_of_account AS A')
            ->leftJoin('detail_journal_entry AS B', 'A.account_id', '=', 'B.account_id')
            ->leftJoin('journal_entry AS C', 'C.id', '=', 'B.entry_id')
            ->leftJoin('division AS D', 'D.id', '=', 'C.division_id')
            ->select(
                'A.account_id',
                'A.account_name',
                'A.account_type',
                DB::raw('SUM("B".debit) AS total_debit'),
                DB::raw('SUM("B".credit) AS total_credit'),
                'D.description'
            )
            ->whereIn(DB::raw('SUBSTRING("A".account_id, 1, 1)'), $accountIdStartsWith)
            ->whereBetween('B.updated_at', [$formattedStartDate, $formattedEndDate])
            ->groupBy('A.account_id', 'A.account_name', 'A.account_type', 'D.description');

        // Tambahkan kondisi untuk `division_id` jika ada
        if ($divisionId !== 'all') {
            $query->where('C.division_id', '=', $divisionId);
        }

        // Log the query
        Log::debug('Generated SQL Query: ' . $query->toSql());
        Log::debug('Query Bindings: ', $query->getBindings());

        // Urutkan hasil berdasarkan `account_id`
        $results = $query->orderBy('A.account_id', 'ASC')->get();

        return $results;
    }
}
