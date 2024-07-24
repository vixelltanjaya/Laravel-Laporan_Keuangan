<?php

namespace App\Http\Controllers;

use App\Models\BisPariwisata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PariwisataExternalController extends Controller
{
    public function index(){
        $bisPariwisata = BisPariwisata::joinSuratJalan();

        Log::debug('apa evidence image nya ' . json_encode($bisPariwisata));

        return view('customer.pariwisata-external', [
            'bisPariwisata' => $bisPariwisata
        ]);
    }
}
