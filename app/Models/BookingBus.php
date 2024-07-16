<?php

namespace App\Models;

use App\Http\Controllers\PariwisataController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingBus extends Model
{
    use HasFactory;

    protected $table = 'booking_bus';

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function busPariwisata()
    {
        return $this->belongsTo(BisPariwisata::class);
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }
}
