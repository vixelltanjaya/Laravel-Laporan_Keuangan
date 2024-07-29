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

    public function updateRole(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        $role = Role::findOrFail($id);
        $role->name = $request->input('name');
        $role->description = $request->input('description');
        $role->save();

        return redirect()->route('user-management.index')->with('success', 'Role updated successfully.');
    }

    public function updateUser(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::findOrFail($id);
        $user->name = $request->input('name');
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->role_id = $request->input('role_id');
        $user->save();

        return redirect()->route('user-management.index')->with('success', 'User updated successfully.');
    }

    public function roleDestroy(string $id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return redirect()->route('user-management.index')->with('success', 'Role deleted successfully.');
    }


    public function userDestroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('user-management.index')->with('success', 'User deleted successfully.');
    }
}
