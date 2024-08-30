<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detail_journal_entry', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->bigInteger('entry_id')->index();
            $table->string('account_id',50)->index();
            $table->string('employee_id',50)->index();
            $table->double('debit',14,2);
            $table->double('credit',14,2);
            $table->string('evidence_image')->nullable();
            $table->timestamps();

            $table->foreign('entry_id')->references('id')->on('journal_entry')->onDelete('cascade');
            $table->foreign('account_id')->references('account_id')->on('chart_of_account')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_journal_entry');
    }
};
