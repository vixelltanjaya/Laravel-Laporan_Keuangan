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

    public function userDestroy($id)
    {
        Log::debug('cek id' .json_encode($id));

        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('user-management.index')->with('berhasil', 'User deleted successfully.');
    }
}
