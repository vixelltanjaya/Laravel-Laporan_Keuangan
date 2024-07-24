<?php

namespace App\Http\Controllers;

use App\Models\BisPariwisata;
use App\Models\CoaModel;
use Illuminate\Http\Request;

class editDataPariwisataController extends Controller
{
    public function index($id)
    {
        $pariwisata = BisPariwisata::findOrFail($id);
        $chartOfAccounts = BisPariwisata::joinCoa();
        return view('edit-data-pariwisata', compact('pariwisata','chartOfAccounts'));
    }
}
