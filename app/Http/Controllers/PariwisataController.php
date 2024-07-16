<?php

namespace App\Http\Controllers;

use App\Models\BisPariwisata;
use App\Models\SuratJalan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class PariwisataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bisPariwisata = BisPariwisata::joinSuratJalan();

        Log::debug('apa evidence image nya ' .json_encode($bisPariwisata));

        return view('pariwisata', [
            'bisPariwisata' => $bisPariwisata
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'plat_nomor' => 'required|string|max:15',
            'tahun_kendaraan' => 'required|integer',
            'karoseri' => 'required|string|max:50',
            'no_rangka' => 'required|string|max:50',
            'evidence_image' => 'nullable|file|max:2048'
        ]);

        try {
            // Upload the file if present
            $evidenceImagePath = null;
            if ($request->hasFile('evidence_image')) {
                $evidenceImagePath = $request->file('evidence_image')->store('evidence_images', 'public');
            }

            // Create BisPariwisata entry
            $bisPariwisata = BisPariwisata::create([
                'plat_nomor' => $request->plat_nomor,
                'tahun_kendaraan' => $request->tahun_kendaraan,
                'karoseri' => $request->karoseri,
                'no_rangka' => $request->no_rangka,
            ]);

            $latestSuratJalan = SuratJalan::where('bis_pariwisata_id', $bisPariwisata->id)
                ->orderBy('version', 'desc')
                ->first();

            $newVersion = $latestSuratJalan ? (floatval($latestSuratJalan->version) + 1.0) : 1.0;
            $newVersion = number_format($newVersion, 1); // format X.0

            // Create SuratJalan entry
            SuratJalan::create([
                'bis_pariwisata_id' => $bisPariwisata->id,
                'evidence_image' => $evidenceImagePath,
                'version' => $newVersion
            ]);

            return redirect()->route('pariwisata.index')->with('berhasil', 'Data berhasil disimpan.');
        } catch (Exception $e) {
            Log::error('Error during store: ' . $e->getMessage());
            return back()->with('gagal', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        Log::debug('Req ID ' . json_encode($request->all));
        Log::debug('route ID ' . json_encode($id));
        try {
            $validateData = $request->validate([
                'plat_nomor' => 'required|string|max:9',
                'tahun_kendaraan' => 'required|integer',
                'brand' => 'required|max:50',
                'no_rangka' => 'required|max:50'
            ]);

            $pariwisata = BisPariwisata::findOrFail($id);

            $pariwisata->plat_nomor = $validateData['plat_nomor'];
            $pariwisata->tahun_kendaraan = $validateData['tahun_kendaraan'];
            $pariwisata->brand = $validateData['brand'];
            $pariwisata->no_rangka = $validateData['no_rangka'];

            Log::debug('route ID' . json_encode($pariwisata));

            $pariwisata->save();

            return redirect()->route('pariwisata.index')->with('berhasil', 'Data berhasil diperbarui!');
        } catch (Throwable $e) {
            return redirect()->route('pariwisata.index')->withErrors(['gagal' => 'Data gagal diperbarui: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Log::debug('route ID' . json_encode($id));
        try {
            $pariwisata = BisPariwisata::findOrFail($id);
            $pariwisata->delete();

            Log::debug('array pariwisata' . json_encode($pariwisata));

            return redirect()->route('pariwisata.index')->with('berhasil', 'Evidence Code berhasil dihapus');
        } catch (Throwable $e) {
            return redirect()->route('pariwisata.index')->with('gagal', 'Terjadi kesalahan saat menghapus Evidence Code' . $e->getMessage());
        }
    }
}
