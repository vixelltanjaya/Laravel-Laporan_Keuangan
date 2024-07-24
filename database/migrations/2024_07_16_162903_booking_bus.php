<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('booking_bus', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('customer_id')->unsigned()->index();
            $table->boolean('is_booked');
            $table->string('description',255);
            $table->bigInteger('bus_pariwisata_id')->unsigned()->index();
            $table->bigInteger('journal_entry_id')->unsigned()->index();
            $table->date('start_book');
            $table->date('end_book');
            $table->timestamps();

            // FK
            $table->foreign('customer_id')->references('id')->on('customer')->onDelete('cascade');
            $table->foreign('bus_pariwisata_id')->references('id')->on('bis_pariwisata')->onDelete('cascade');
            $table->foreign('journal_entry_id')->references('id')->on('journal_entry')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booking_bus');
    }
};
