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
    public function up(): void
    {
        Schema::create('master_transaction', function (Blueprint $table) {
            $table->id(); 
            $table->integer('code')->unique()->default(DB::raw('(SELECT COALESCE(MAX(code), 999) + 1 FROM master_transaction)'));
            $table->string('description');
            $table->unsignedBigInteger('evidence_id');
            $table->timestamps();
        
            $table->foreign('evidence_id')->references('id')->on('evidence_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_transaction');
    }
};
