<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('account_balance', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->string('account_id', 20)->index();
            $table->double('beginning_balance',15,3);
            $table->double('debit_mutation',15,3);
            $table->double('credit_mutation',15,3);
            $table->double('ending_balance',15,3);
            $table->string('balance_time',9);
            $table->timestamps();

            $table->foreign('account_id')->references('account_id')->on('chart_of_account')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_balance');
    }
};
