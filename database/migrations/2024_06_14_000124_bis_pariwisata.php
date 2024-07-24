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
        Schema::create('bis_pariwisata', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('plat_nomor', 15);
            $table->integer('tahun_kendaraan');
            $table->string('karoseri', 50);
            $table->string('no_rangka', 50);
            $table->double('selling_price', 14,3);
            $table->string('account_id', 50)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bis_pariwisata');
    }
};
