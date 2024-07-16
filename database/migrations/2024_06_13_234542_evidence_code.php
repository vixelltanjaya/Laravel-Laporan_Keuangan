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
        Schema::create('evidence_code', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('prefix_code', 3)->index();
            $table->string('code_title', 75);
            $table->timestamps();

            $table->unique(['prefix_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evidence_code');
    }
};
