<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{

    public function index (){

        return view ('add-role');
    }

    public function store(Request $request)
    {

        Log::debug('cek request' . json_encode($request->all()));

        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:role,name',
            'description' => 'required|string|max:255',
        ]);

        $slug = strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $validatedData['name']));

        Log::debug('apa nama slug' . $slug);

        try {
            $role = Role::create([
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'slug' => $slug
            ]);

            Log::info('Role created: ' . json_encode($role));

            return redirect()->route('user-management.index')->with('berhasil', 'Role berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error creating role: ' . $e->getMessage());
            return redirect()->route('user-management.index')->with('gagal', 'Terjadi kesalahan saat menambahkan role.');
        }
    }
}
