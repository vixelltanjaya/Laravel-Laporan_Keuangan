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
        Schema::create('chart_of_account', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('account_id', 20)->unique()->index();
            $table->string('account_name', 255);
            $table->string('account_sign', 14);
            $table->string('account_type', 14);
            $table->string('account_group', 50);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_of_account');
    }
};
