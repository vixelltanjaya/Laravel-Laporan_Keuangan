<?php

namespace App\Models;

use App\Http\Controllers\PariwisataController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BookingBus extends Model
{
    use HasFactory;

    protected $table = 'booking_bus';

    protected $fillable = [
        'customer_id',
        'bus_pariwisata_id',
        'journal_entry_id',
        'start_book',
        'end_book',
        'description',
        'fleet_departure',
        'fleet_arrivals'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function busPariwisata()
    {
        return $this->belongsTo(BisPariwisata::class);
    }

    // public function journalEntry()
    // {
    //     return $this->belongsTo(JournalEntry::class);
    // }

    public static function joinBusAndCustomer($platNomor)
    {
        return DB::table('booking_bus as B')
            ->select(
                'C.name',
                'C.alamat',
                'C.no_telp',
                'A.plat_nomor',
                'A.selling_price',
                'B.is_booked',
                'B.journal_entry_id',
                'B.bus_pariwisata_id',
                'B.description',
                'A.plat_nomor',
                'B.start_book',
                'B.end_book',
                'B.id',
                'B.fleet_departure',
                'B.fleet_arrivals',
                'B.customer_id'
            )
            ->join('bis_pariwisata as A', 'A.id', '=', 'B.bus_pariwisata_id')
            ->join('customer as C', 'C.id', '=', 'B.customer_id')
            ->where('A.plat_nomor', $platNomor)
            ->orderBy('B.start_book','desc')
            ->get();
    }

    public static function joinBusAndCustomerDashboard()
    {
        return DB::table('booking_bus as B')
            ->select(
                'C.name',
                'C.alamat',
                'C.no_telp',
                'A.plat_nomor',
                'A.selling_price',
                'B.is_booked',
                'B.journal_entry_id',
                'B.bus_pariwisata_id',
                'B.description',
                'A.plat_nomor',
                'B.start_book',
                'B.end_book'
            )
            ->join('bis_pariwisata as A', 'A.id', '=', 'B.bus_pariwisata_id')
            ->join('customer as C', 'C.id', '=', 'B.customer_id')
            ->get();
    }
}
