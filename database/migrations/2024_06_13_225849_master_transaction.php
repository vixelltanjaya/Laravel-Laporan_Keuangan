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
        Schema::create('master_transaction', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->bigInteger('code')->index();
            $table->string('description',255);
            $table->integer('evidence_id')->index();
            $table->timestamps();

            $table->unique(['code']);

            $table->foreign('evidence_id')->references('id')->on('evidence_code')->onDelete('cascade');
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
