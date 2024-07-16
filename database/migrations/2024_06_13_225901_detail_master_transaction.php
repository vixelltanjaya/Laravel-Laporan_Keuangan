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
        Schema::create('detail_master_transaction', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('master_code')->index();
            $table->string('gl_account', 20)->index();
            $table->string('account_position', 10);
            $table->timestamps();

            $table->foreign('master_code')->references('code')->on('master_transaction')->onDelete('cascade');
            $table->foreign('gl_account')->references('account_id')->on('chart_of_account')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_master_transaction');
    }
};
