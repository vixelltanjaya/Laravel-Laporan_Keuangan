<?php

namespace App\Http\Controllers;

use App\Models\EvidenceCode;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EvidenceCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $evidenceCode = EvidenceCode::all();
        return view('user-accounting.evidence-code', compact('evidenceCode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'prefix_code' => 'required|string|max:3',
            'code_title' => 'required|string|max:75',
        ]);

        try {
            // simpan evidence code
            $evidenceCode = new EvidenceCode();
            $evidenceCode->prefix_code = $request->prefix_code;
            $evidenceCode->code_title = $request->code_title;
            $evidenceCode->save();

            return redirect()->route('evidence-code.index')->with('berhasil', 'Evidence Code berhasil ditambahkan.');
        } catch (Exception $e) {
            return redirect()->back()->with('gagal', 'Terjadi kesalahan saat menambahkan Evidence Code');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        Log::debug('Request ID: ' . json_encode($request->id));
        Log::debug('Route ID: ' . json_encode($id));

        $request->validate([
            'prefix_code' => 'max:3|min:3',
            'code_title' => 'required|max:75',
        ]);

        try {
            $evidence = EvidenceCode::find($id);
            $evidence->update($request->all());
            return redirect()->route('evidence-code.index')->with('berhasil', 'Evidence Code Berhasil diupdate');
        } catch (Exception) {
            return redirect()->back()->with('gagal', 'Terjadi kesalahan saat mengubah Evidence Code');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $evidenceCode = EvidenceCode::findOrFail($id);
            $evidenceCode->delete();
    
            return redirect()->route('evidence-code.index')->with('berhasil', 'Evidence Code berhasil dihapus');
        } catch (Exception $e) {
            return redirect()->route('evidence-code.index')->with('gagal', 'Terjadi kesalahan saat menghapus Evidence Code' .$e->getMessage());
        }
    }
}
