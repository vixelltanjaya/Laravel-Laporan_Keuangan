<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    public function index()
    {
        Log::info('masuk ke func index');
        $customers = Customer::orderBy('name', 'asc')->get();
        return view('customer', compact('customers'));
    }

    public function store(Request $request)
    {

        try {
            Log::debug('Received name: ' . $request->name);

            $request->validate([
                'name' => 'required|string|max:255',
                'no_telp' => 'required|string|numeric|digits_between:10,12',
                'alamat' => 'required|string|max:255',
                'email' => 'required|string|email'
            ]);

            Log::debug('Request data: ' . json_encode($request->all()));

            Customer::create($request->all());

            return redirect()->route('customer.index')->with('berhasil', 'Customer berhasil ditambahkan.');
        } catch (QueryException $e) {
            if ($e->getCode() == 23505) {
                $message = 'Email sudah terdaftar. Silakan gunakan email yang berbeda.';
            } else {
                $message = 'Terjadi kesalahan pada database. Silakan coba lagi.';
            }
    
            Log::error('Error creating customer: ' . $e->getMessage());
    
            return redirect()->route('customer.index')->with('gagal', $message);
        } catch (\Exception $e) {
            Log::error('Error creating customer: ' . $e->getMessage());
    
            return redirect()->route('customer.index')->with('gagal', 'Terjadi kesalahan saat menambahkan customer. Silakan coba lagi.');
        }
    }

    public function update(Request $request, $id)
    {

        Log::info('masuk ke func update');
        Log::debug('Request ID: ' . json_encode($request->id));
        Log::debug('Route ID: ' . json_encode($id));

        $request->validate([
            'name' => 'required|string|max:255',
            'no_telp' => 'required|string|numeric|digits_between:10,12',
            'alamat' => 'required|string|max:255',
            'email' => 'required|string|email'
        ]);

        try {
            $customer = Customer::findOrFail($id);

            $customer->update($request->all());

            Log::info('Data updated customer ' . json_encode($customer));
            return redirect()->route('customer.index')->with('berhasil', 'Customer berhasil diupdate.');
        } catch (QueryException $e) {
            if ($e->getCode() == 23505) {
                $message = 'Email sudah terdaftar. Silakan gunakan email yang berbeda.';
            } else {
                $message = 'Terjadi kesalahan pada database. Silakan coba lagi.';
            }
    
            Log::error('Error creating customer: ' . $e->getMessage());
    
            return redirect()->route('customer.index')->with('gagal', $message);
        } catch (\Exception $e) {
            Log::error('Error creating customer: ' . $e->getMessage());
    
            return redirect()->route('customer.index')->with('gagal', 'Terjadi kesalahan saat menambahkan customer. Silakan coba lagi.');
        }
    }

    public function destroy($id)
    {
        Customer::find($id)->delete();

        return redirect()->route('customer.index')->with('berhasil', 'Customer Berhasil dihapus');
    }
}
