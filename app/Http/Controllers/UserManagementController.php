<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function index()
    {
        $query = User::with('roles');
        if (request('search')) {
            $s = request('search');
            $query->where(fn($q) => $q
                ->where('name', 'like', "%$s%")
                ->orWhere('email', 'like', "%$s%"));
        }
        $users = $query->latest()->paginate(20);
        $roles = Role::all();
        return view('users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'cnic'        => 'nullable|string|max:20|unique:users,cnic',
            'password'    => 'required|string|min:8',
            'role'        => 'required|exists:roles,name',
        ], [
            'cnic.unique' => 'A user with this CNIC already exists.',
            'email.unique' => 'A user with this email already exists.',
        ]);
        $user = User::create([
            'name'        => $data['name'],
            'father_name' => $data['father_name'] ?? null,
            'email'       => $data['email'],
            'cnic'        => $data['cnic'] ?? null,
            'password'    => Hash::make($data['password']),
        ]);
        $user->assignRole($data['role']);
        return redirect()->route('users.index')->with('success', 'User created.');
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate(['role' => 'required|exists:roles,name']);
        $user->syncRoles([$request->role]);
        return redirect()->route('users.index')->with('success', 'Role updated.');
    }

    public function updatePassword(Request $request, User $user)
    {
        $request->validate(['password' => 'required|string|min:8']);
        $user->update(['password' => Hash::make($request->password)]);
        return redirect()->route('users.index')->with('success', 'Password updated.');
    }

    public function destroy(User $user)
    {
        if (Auth::check() && $user->id === Auth::id()) {
            return redirect()->route('users.index')->with('error', 'Cannot delete your own account.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted.');
    }
}
