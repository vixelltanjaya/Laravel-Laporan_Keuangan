<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $UserManagement = User::joinUserManagementRole();
        $role = Role::all();
        $users = User::all();

        return view('user-management', compact(['UserManagement', 'role', 'users']));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'role' => 'required|exists:role,id',
            'name' => 'required|exists:users,id',
        ]);

        // Find the user and update the role_id
        $user = User::find($request->name);
        $user->role_id = $request->role;
        $user->save();

        // Redirect back with success message
        return redirect()->route('user-management.index')->with('success', 'User role updated successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
