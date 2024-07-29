<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::statement('
            CREATE OR REPLACE FUNCTION f_get_coa()
            RETURNS TABLE (
                account_id varchar, 
                account_name varchar, 
                account_sign varchar, 
                account_type varchar, 
                account_group varchar
            ) AS $$
            BEGIN
                RETURN QUERY
                SELECT 
                    coa."account_id", 
                    coa."account_name", 
                    coa."account_sign", 
                    coa."account_type", 
                    coa."account_group"
                FROM 
                    "chart_of_account" coa;
            END;
            $$ LANGUAGE plpgsql;
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP FUNCTION IF EXISTS f_get_coa();');
    }
};
