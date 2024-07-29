<?php

namespace App\Http\Controllers;

use App\Models\BisPariwisata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PgSql\Lob;

class HomeController extends Controller
{
    public function home()
    {
        return redirect()->route('dashboard');
    }
}
