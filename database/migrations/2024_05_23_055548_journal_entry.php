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
        Schema::create('journal_entry', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->string('description',155);
            $table->date('entry_date');
            $table->bigInteger('user_id')->index();
            $table->string('evidence_code',14);
            $table->tinyInteger('is_reversed');
            $table->string('reversed_by',14)->index()->nullable();
            $table->timestamp('reversed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entry');
    }
};
