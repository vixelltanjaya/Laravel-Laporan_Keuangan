<?php

namespace App\Models;

use Carbon\Carbon;
use Exception;
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

        $getLastDate = date('Y-m-d', strtotime("last day of $transactionMonthEnd "));

        Log::debug($getLastDate);

        try {
            $balanceTime = Carbon::createFromFormat('Y-m-d', $getLastDate)
                ->startOfMonth()
                ->subDay()
                ->format('Ymd');
            Log::debug('balance_time calculated: ' . $balanceTime);
        } catch (Exception $e) {
            Log::error('Error parsing date: ' . $e->getMessage());
            $balanceTime = null;
        }

        Log::debug('datefilter ' . json_encode($formattedEndDate));

        $query = DB::table('chart_of_account as A')
            ->leftJoin('detail_journal_entry as B', function ($join) use ($formattedStartDate, $formattedEndDate) {
                $join->on('A.account_id', '=', 'B.account_id')
                    ->whereBetween('B.updated_at', [$formattedStartDate, $formattedEndDate]);
            })
            ->leftJoin('account_balance as C', function ($join) use ($balanceTime) {
                $join->on('A.account_id', '=', 'C.account_id')
                    ->where('C.balance_time', $balanceTime);
            })
            ->leftJoin('journal_entry as D', 'B.entry_id', '=', 'D.id')
            ->select(
                'A.account_id',
                'A.account_name',
                'A.account_sign',
                'A.account_type',
                'D.division_id',
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
            ->groupBy('A.account_id', 'A.account_name', 'C.beginning_balance', 'A.account_sign', 'C.ending_balance', 'C.balance_time', 'A.account_type', 'D.division_id')
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

        if (!$transactionMonthStart) {
            $transactionMonthStart = date('Y-01-01');
        } else {
            $transactionMonthStart = date('Y-m-d', strtotime($transactionMonthStart . '-01'));
        }

        $formattedStartDate = date('Y-m-d 00:00:00', strtotime($transactionMonthStart . '-01'));
        // $formattedEndDate = date('Y-m-d 23:59:59', strtotime("last day of $transactionMonthEnd"));

        Log::debug('formattedStartDate: ' . json_encode($formattedStartDate));
        Log::debug('formattedEndDate: ' . json_encode($transactionMonthEnd));

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
            WHEN LOWER ("A".account_type) = \'aset\' THEN SUM("B".debit) - SUM("B".credit)
            WHEN LOWER ("A".account_type) = \'kewajiban\' THEN SUM("B".credit) - SUM("B".debit)
            WHEN LOWER ("A".account_type) = \'ekuitas\' THEN SUM("B".credit) - SUM("B".debit)
            WHEN LOWER ("A".account_type) = \'pendapatan\' THEN SUM("B".credit) - SUM("B".debit)
            WHEN LOWER ("A".account_type) = \'beban\' THEN SUM("B".debit) - SUM("B".credit)
            ELSE 0
        END AS total_amount
        ')
            )
            ->whereIn(DB::raw('SUBSTRING("A".account_id, 1, 1)'), $accountIdStartsWith)
            ->whereBetween('B.updated_at', [$formattedStartDate, $transactionMonthEnd]);

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
            ->leftJoin('detail_journal_entry as C', function ($join) use ($dateStart, $dateEnd) {
                $join->on('C.account_id', '=', 'A.account_id')
                    ->whereBetween('C.updated_at', [$dateStart, $dateEnd]);
            })
            ->leftJoin('journal_entry as B', 'B.id', '=', 'C.entry_id')
            ->leftJoin('account_balance as D', 'D.account_id', '=', 'A.account_id')
            ->select(
                'A.account_id',
                'A.account_name',
                'A.account_sign',
                'C.debit',
                'C.credit',
                'B.description',
                'B.evidence_code',
                'B.created_at',
                'C.updated_at',
                'D.balance_time'
            )
            ->orderBy('A.account_id', 'asc')
            ->orderBy('B.created_at', 'asc');

        Log::debug('SQL Query: ' . $results->toSql());
        Log::debug('Query Bindings: ', $results->getBindings());

        return $results->get();
    }
    public static function getNetIncome($request)
    {
        $accountIdStartsWith = ['1', '2', '3', '4', '5'];
        $transactionMonthStart = $request->input('transaction_month_start');
        $transactionMonthEnd = $request->input('transaction_month_end');

        Log::debug('transactionMonthStart: ' . json_encode($transactionMonthStart));
        Log::debug('transactionMonthEnd: ' . json_encode($transactionMonthEnd));

        if (!$transactionMonthStart) {
            $transactionMonthStart = date('Y-01-01');
        } else {
            $transactionMonthStart = date('Y-m-d', strtotime($transactionMonthStart . '-01'));
        }

        $formattedStartDate = date('Y-m-d 00:00:00', strtotime($transactionMonthStart . '-01'));
        $formattedEndDate = date('Y-m-d 23:59:59', strtotime("last day of $transactionMonthEnd"));

        Log::debug('formattedStartDate: ' . json_encode($formattedStartDate));
        Log::debug('formattedEndDate: ' . json_encode($formattedEndDate));

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
                'C.entry_date',
                DB::raw('SUM("B".debit) AS total_debit'),
                DB::raw('SUM("B".credit) AS total_credit'),
                DB::raw('
        CASE
            WHEN LOWER ("A".account_type) = \'aset\' THEN SUM("B".debit) - SUM("B".credit)
            WHEN LOWER ("A".account_type) = \'kewajiban\' THEN SUM("B".credit) - SUM("B".debit)
            WHEN LOWER ("A".account_type) = \'ekuitas\' THEN SUM("B".credit) - SUM("B".debit)
            WHEN LOWER ("A".account_type) = \'pendapatan\' THEN SUM("B".credit) - SUM("B".debit)
            WHEN LOWER ("A".account_type) = \'beban\' THEN SUM("B".debit) - SUM("B".credit)
            ELSE 0
        END AS total_amount
        ')
            )
            ->whereIn(DB::raw('SUBSTRING("A".account_id, 1, 1)'), $accountIdStartsWith)
            ->whereBetween('B.updated_at', [$formattedStartDate, $formattedEndDate]);

        // Add condition for `division_id` if provided
        if ($divisionId !== 'all' && $divisionId != 0) {
            $query->where('C.division_id', '=', $divisionId);
        }

        // Group by `account_id` to ensure unique account_id values
        $results = $query
            ->groupBy(
                'A.account_id',
                'A.account_name',
                'A.account_type',
                'A.account_group',
                'E.balance_time',
                'C.entry_date'
            )
            ->orderBy('A.account_id', 'ASC')
            ->get();

        // Log the query
        Log::debug('Generated SQL Query: ' . $query->toSql());
        Log::debug('Query Bindings: ', $query->getBindings());

        return $results;
    }
}
