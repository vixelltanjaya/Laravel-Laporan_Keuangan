<?php

namespace App\Http\Controllers;

use App\Models\CoaModel;
use Illuminate\Http\Request;

class addDataPariwisataController extends Controller
{
    public function index()
    {
        $chartOfAccounts = CoaModel::where('account_id', 'like', '2%')->get();
        return view('add-data-pariwisata', compact('chartOfAccounts'));
    }
}
