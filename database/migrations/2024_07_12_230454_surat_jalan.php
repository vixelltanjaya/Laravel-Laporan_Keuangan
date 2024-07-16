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
        Schema::create('surat_jalan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('bis_pariwisata_id'); // Foreign key column
            $table->string('evidence_image');
            $table->string('version', 9);
            $table->timestamps();

            // Define the foreign key constraint
            $table->foreign('bis_pariwisata_id')->references('bis_pariwisata')->on('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_jalan');
    }
};
