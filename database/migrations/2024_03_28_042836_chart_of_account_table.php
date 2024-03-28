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
        Schema::create('coa', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('account_id',20)->unique();
            $table->string('account_code',255);
            $table->string('account_group',14);
            $table->string('account_name',14);
            $table->string('account_subgroup',14);
            $table->string('account_status',14);
            $table->date('created_at');
            $table->date('updated_at');
            $table->date('inactive_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::dropIfExists('coa');
    }
};
