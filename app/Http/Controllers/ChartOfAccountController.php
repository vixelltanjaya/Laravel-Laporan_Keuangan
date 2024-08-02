<?php

namespace App\Http\Controllers;

use App\Models\CoaModel;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Exports\ChartOfAccountExport;
use App\Imports\ChartOfAccountImport;
use Exception;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ChartOfAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $chartOfAccounts = CoaModel::orderBy('account_id', 'asc')->get();
        return view('user-accounting.chart-Of-Account', compact('chartOfAccounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_id' => 'required|string|max:20',
            'account_name' => 'required|string|max:255',
            'account_sign' => 'required|string|max:14',
            'account_type' => 'required|string|max:14',
            'account_group' => 'nullable|string|max:14',
        ]);

        $data = $request->all();
        $data['account_group'] = $request->input('account_group') ?? '';

        CoaModel::create($data);

        return redirect()->route('chart-of-account.index')->with('berhasil', 'Akun berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {

        Log::debug('Request ID: ' . json_encode($request->id));
        Log::debug('Route ID: ' . json_encode($id));

        $request->validate([
            'account_id' => 'required|string|max:50',
            'account_name' => 'required|string|max:255',
            'account_sign' => 'required|string|max:50',
            'account_type' => 'required|string|max:255',
            'account_group' => 'required|string|max:255',
        ]);

        try {
            $coa = CoaModel::findOrFail($id);

            $coa->account_id = $request->account_id;
            $coa->account_name = $request->account_name;
            $coa->account_sign = $request->account_sign;
            $coa->account_type = $request->account_type;
            $coa->account_group = $request->account_group;

            $coa->save();

            Log::debug('Array ID ' . json_encode($id));
            Log::debug('Array COA ' . json_encode($coa));
            Log::debug('Request Data: ' . json_encode($request->all()));

            return redirect()->route('chart-of-account.index')->with('berhasil', 'Account berhasil diupdate.');
        } catch (Exception $e) {
            Log::error('Error updating chart of account: ' . $e->getMessage());
            return redirect()->route('chart-of-account.index')->with('gagal', 'Terjadi kesalahan saat mengupdate account: ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $chartOfAccount = CoaModel::findOrFail($id);
            $chartOfAccount->delete();

            Log::info('Apakah account ' . json_encode($chartOfAccount));

            return redirect()->route('chart-of-account.index')->with('berhasil', 'Akun berhasil dihapus');
        } catch (Exception) {
            return redirect()->route('chart-of-account.index')->with('gagal', 'Terjadi kesalahan saat menghapus akun');
        }
    }


    public function exportMasterAccountToExcel()
    {
        return Excel::download(new ChartOfAccountExport, 'data_master_akun.xlsx');
    }

    public function importAccount(Request $request)
    {
        Log::info('Apakah proses import masuk sini?');

        $request->validate([
            'import_account' => 'required|file|mimes:xls,xlsx,csv',
        ]);

        if ($request->hasFile('import_account')) {
            Log::info('File ditemukan: ' . $request->file('import_account')->getClientOriginalName());

            try {
                Excel::import(new ChartOfAccountImport, $request->file('import_account'));

                Log::info('Proses import selesai');
            } catch (\Exception $e) {
                Log::error('Error during import: ' . $e->getMessage());
                return redirect()->back()->with('gagal', 'Terjadi kesalahan saat mengimpor file');
            }
        } else {
            Log::error('File tidak ditemukan dalam request');
            return redirect()->back()->with('gagal', 'File tidak ditemukan dalam request.');
        }

        return redirect()->back()->with('berhasil', 'File berhasil diunggah dan diproses.');
    }
}
