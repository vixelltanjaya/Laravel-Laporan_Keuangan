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

        Log::debug('apa evidence image nya ' . json_encode($bisPariwisata));

        return view('pariwisata', [
            'bisPariwisata' => $bisPariwisata
        ]);
    }

    public function store(Request $request)
    {
        Log::debug('request' .json_encode($request->all()));
        $request->validate([
            'plat_nomor' => 'required|string|max:15',
            'tahun_kendaraan' => 'required|integer',
            'karoseri' => 'required|string|max:50',
            'no_rangka' => 'required|string|max:50',
            'selling_price' => 'required|numeric',
            'evidence_image' => 'nullable|file|max:2048',
            'evidence_image_bus' => 'nullable|file|max:2048',
            'chart_of_account' => 'required|string|max:50',
        ]);

        try {
            // Upload the file surat jalan
            $evidenceImagePath = null;
            if ($request->hasFile('evidence_image')) {
                $evidenceImagePath = $request->file('evidence_image')->store('evidence_images', 'public');
            }

            // Upload the file foto bis
            $evidenceImageBusPath = null;
            if ($request->hasFile('evidence_image_bus')) {
                $evidenceImageBusPath = $request->file('evidence_image_bus')->store('bus_images', 'public');
            }

            // Create BisPariwisata entry
            $bisPariwisata = BisPariwisata::create([
                'plat_nomor' => $request->plat_nomor,
                'tahun_kendaraan' => $request->tahun_kendaraan,
                'karoseri' => $request->karoseri,
                'selling_price' => $request->selling_price,
                'no_rangka' => $request->no_rangka,
                'evidence_image_bus' => $evidenceImageBusPath,
                'account_id' => $request->chart_of_account
            ]);

            Log::debug('bis pariwisata' .json_encode($bisPariwisata));

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

    public function update(Request $request, $id)
    {
        Log::debug('Req ID ' . json_encode($request->all()));
        Log::debug('route ID ' . json_encode($id));

        try {
            $validateData = $request->validate([
                'plat_nomor' => 'required|string|max:15',
                'tahun_kendaraan' => 'required|integer',
                'karoseri' => 'required|max:50',
                'no_rangka' => 'required|max:50',
                'selling_price' => 'required|numeric',
                'chart_of_account' => 'required|string|max:50',
                'evidence_image' => 'nullable|file|max:2048',
                'evidence_image_bus' => 'file|max:2048'
            ]);

            Log::debug('validate data ' . json_encode($validateData));
            $pariwisata = BisPariwisata::findOrFail($id);

            // Upload the file foto bis
            $evidenceImageBusPath = null;
            if ($request->hasFile('evidence_image_bus')) {
                $evidenceImageBusPath = $request->file('evidence_image_bus')->store('bus_images', 'public');
                $pariwisata->evidence_image_bus = $evidenceImageBusPath;
            }
            // cek evidence_image
            if ($request->hasFile('evidence_image')) {
                $filePath = $request->file('evidence_image')->store('evidence_images', 'public');

                // Insert a new record into surat_jalan table
                SuratJalan::create([
                    'bis_pariwisata_id' => $pariwisata->id,
                    'evidence_image' => $filePath,
                    'version' => SuratJalan::where('bis_pariwisata_id', $pariwisata->id)->count() + 1
                ]);
            }

            $pariwisata->plat_nomor = $validateData['plat_nomor'];
            $pariwisata->tahun_kendaraan = $validateData['tahun_kendaraan'];
            $pariwisata->karoseri = $validateData['karoseri'];
            $pariwisata->no_rangka = $validateData['no_rangka'];
            $pariwisata->selling_price = $validateData['selling_price'];
            $pariwisata->account_id = $validateData['chart_of_account'];

            Log::debug('pariwisata' . json_encode($pariwisata));

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

            return redirect()->route('pariwisata.index')->with('berhasil', 'Data Bis berhasil dihapus');
        } catch (Throwable $e) {
            return redirect()->route('pariwisata.index')->with('gagal', 'Terjadi kesalahan saat menghapus Data Bis' . $e->getMessage());
        }
    }
}
