<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    public function index()
    {
        Log::info('masuk ke func index');
        $customers = Customer::all();
        return view('customer', compact('customers'));
    }

    public function store(Request $request)
    {

        Log::debug('name' .$request->name);

        $request->validate([
            'name' => 'required|string|max:255',
            'no_telp' => 'required|string|numeric|digits_between:10,12',
            'alamat' => 'required|string|max:255',
            'email' => 'required|string|email'
        ]);

        Log::debug('request' .json_encode($request->all()));

        Customer::create($request->all());

        return redirect()->route('customer.index')->with('berhasil', 'Customer berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {

        Log::info('masuk ke func update');
        Log::debug('Request ID: ' . json_encode($request->id));
        Log::debug('Route ID: ' . json_encode($id));

        $request->validate([
            'nama' => 'required|string|max:255',
            'no_telp' => 'required|string|numeric|digits_between:10,12',
            'alamat' => 'required|string|max:255',
            'email' => 'required|string|email'
        ]);

        try {
            $customer = Customer::findOrFail($id);

            $customer->update($request->all());
    
            Log::info('Data updated customer ' . json_encode($customer));
            return redirect()->route('customer.index')->with('berhasil', 'Customer berhasil diupdate.');
        } catch (\Exception $e) {
            Log::error('Error updating customer: ' . $e->getMessage());
            return redirect()->route('customer.index')->with('gagal', 'Terjadi kesalahan saat mengupdate customer: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        Customer::find($id)->delete();

        return redirect()->route('customer.index')->with('berhasil', 'Customer Berhasil dihapus');
    }
}
