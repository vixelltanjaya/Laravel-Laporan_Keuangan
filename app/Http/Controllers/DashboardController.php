<?php

namespace App\Http\Controllers;

use App\Models\BisPariwisata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $salesData = BisPariwisata::getSalesOnBothLine();

        $labels = [];
        $totalCredit = [];

        foreach ($salesData as $data) {
            $labels[] = $data->account_id;
            $totalCredit[] = $data->total_credit; // Values for the dataset
        }

        Log::debug('label' .json_encode($labels));

        return view('dashboard', compact('labels','totalCredit'));
    }
}

