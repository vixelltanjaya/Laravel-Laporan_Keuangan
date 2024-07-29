<?php

namespace App\Models;

use Carbon\Carbon;
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


    public static function sumBalanceCoa($request)
    {
        $transactionMonthStart = $request->input('month_year');
        $transactionMonthEnd = $request->input('month_year');

        $formattedStartDate = date('Y-m-d 00:00:00', strtotime($transactionMonthStart . '-01'));
        $formattedEndDate = date('Y-m-d 23:59:59', strtotime("last day of $transactionMonthEnd"));

        Log::debug('datefilter ' . json_encode($formattedEndDate));

        $query = DB::table('chart_of_account as A')
            ->leftJoin('detail_journal_entry as B', function ($join) use ($formattedStartDate, $formattedEndDate) {
                $join->on('A.account_id', '=', 'B.account_id')
                    ->whereBetween('B.updated_at', [$formattedStartDate, $formattedEndDate]);
            })
            ->leftJoin('account_balance as C', 'A.account_id', '=', 'C.account_id')
            ->leftJoin('journal_entry as D', 'B.entry_id', '=', 'D.id')
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
                END AS "balance_difference"'),
                DB::raw('COALESCE("C".ending_balance, 0) AS "beginning_balance_next_month"'),
                DB::raw('SUBSTRING("C".balance_time FROM 1 FOR 6) AS balance_time')
            )
            ->groupBy('A.account_id', 'A.account_name', 'C.beginning_balance', 'A.account_sign', 'C.ending_balance',  'C.balance_time')
            ->orderBy('A.account_id', 'ASC');

        Log::debug('SQL Query: ' . $query->toSql());
        Log::debug('Query Bindings: ', $query->getBindings());

        return $query->get();
    }

    public static function getSumRequestTrx($request)
    {
        $accountIdStartsWith = ['1', '2', '3', '4', '5'];
        $transactionMonthStart = $request->input('transaction_month_start');
        $transactionMonthEnd = $request->input('transaction_month_end');

        $formattedStartDate = date('Y-m-d 00:00:00', strtotime($transactionMonthStart . '-01'));
        $formattedEndDate = date('Y-m-d 23:59:59', strtotime("last day of $transactionMonthEnd"));

        $divisionId = $request->input('division_id');

        // Base query
        $query = DB::table('chart_of_account AS A')
            ->leftJoin('detail_journal_entry AS B', 'A.account_id', '=', 'B.account_id')
            ->leftJoin('journal_entry AS C', 'C.id', '=', 'B.entry_id')
            ->leftJoin('account_balance AS E', 'E.account_id', '=', 'A.account_id')
            ->select(
                'A.account_id',
                'A.account_name',
                'A.account_type',
                'A.account_group',
                'E.balance_time',
                DB::raw('SUM("B".debit) AS total_debit'),
                DB::raw('SUM("B".credit) AS total_credit'),
                DB::raw('
            CASE
                WHEN "A".account_type = \'Aset\' OR "A".account_type = \'aset\' THEN SUM("B".debit) - SUM("B".credit)
                WHEN "A".account_type = \'Kewajiban\' OR "A".account_type = \'kewajiban\' THEN SUM("B".credit) - SUM("B".debit)
                WHEN "A".account_type = \'Ekuitas\' OR "A".account_type = \'ekuitas\' THEN SUM("B".credit) - SUM("B".debit)
                WHEN "A".account_type = \'Pendapatan\' OR "A".account_type = \'pendapatan\' THEN SUM("B".credit) - SUM("B".debit)
                WHEN "A".account_type = \'Beban\' OR "A".account_type = \'beban\' THEN SUM("B".debit) - SUM("B".credit)
                ELSE 0
            END AS total_amount
            ')
            )
            ->whereIn(DB::raw('SUBSTRING("A".account_id, 1, 1)'), $accountIdStartsWith)
            ->whereBetween('B.updated_at', [$formattedStartDate, $formattedEndDate]);

        // Add condition for `division_id` if provided
        if ($divisionId !== 'all') {
            $query->where('C.division_id', '=', $divisionId);
        }

        // Group by `account_id` to ensure unique account_id values
        $results = $query
            ->groupBy(
                'A.account_id',
                'A.account_name',
                'A.account_type',
                'A.account_group',
                'E.balance_time'
            )
            ->orderBy('A.account_id', 'ASC')
            ->get();

        // Log the query
        Log::debug('Generated SQL Query: ' . $query->toSql());
        Log::debug('Query Bindings: ', $query->getBindings());

        return $results;
    }

    public static function getRequestTrx($request)
    {
        $monthYear = $request->input('month_year');
        $dateStart = Carbon::createFromFormat('Y-m', $monthYear)->startOfMonth();
        $dateEnd = $dateStart->copy()->endOfMonth();

        $results = DB::table('chart_of_account as A')
            ->leftJoin('detail_journal_entry as B', 'A.account_id', '=', 'B.account_id')
            ->leftJoin('journal_entry as C', function ($join) use ($dateStart, $dateEnd) {
                $join->on('C.id', '=', 'B.entry_id')
                    ->whereBetween('C.created_at', [$dateStart, $dateEnd]);
            })
            ->leftJoin('account_balance as D', 'D.account_id', '=', 'A.account_id')
            ->select(
                'A.account_id',
                'A.account_name',
                'A.account_sign',
                'B.debit',
                'B.credit',
                'C.description',
                'C.evidence_code',
                'C.created_at',
                'D.beginning_balance',
                'D.ending_balance',
                'D.balance_time'
            )
            ->orderBy('A.account_id', 'asc')
            ->orderBy('C.created_at', 'asc')
            ->get();

        return $results;
    }
}
