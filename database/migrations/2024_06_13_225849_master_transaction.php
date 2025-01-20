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
        Schema::create('evidence_code', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('prefix_code', 3)->index();
            $table->string('code_title', 75);
            $table->timestamps();

            $table->unique(['prefix_code']);
        });
        Schema::create('master_transaction', function (Blueprint $table) {
            $table->id(); 
            $table->integer('code')->unique()->nullable();
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
        Schema::dropIfExists('evidence_code');
    }
};
