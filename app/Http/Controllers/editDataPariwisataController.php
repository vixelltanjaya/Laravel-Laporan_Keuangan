<?php

namespace App\Http\Controllers;

use App\Models\BisPariwisata;
use App\Models\CoaModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class editDataPariwisataController extends Controller
{
    public function index($id)
    {
        $pariwisata = BisPariwisata::findOrFail($id);
        $chartOfAccounts = CoaModel::where('account_id', 'like', '2%')->orderBy('account_id','asc')->get();

        Log::debug('id' .json_encode($id));
        return view('edit-data-pariwisata', compact('pariwisata','chartOfAccounts'));
    }
}
