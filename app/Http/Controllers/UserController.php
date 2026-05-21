<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        // Superadmin mengelola Staff, HRD, Finance, dan Superadmin lain (opsional)
        // Kita exclude 'pegawai' karena dikelola otomatis di data Master Pegawai
        $users = User::where('role', '!=', User::ROLE_PEGAWAI)->latest()->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in([User::ROLE_SUPERADMIN, User::ROLE_STAFF, User::ROLE_HRD, User::ROLE_FINANCE])],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => true,
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        // Mencegah edit akun pegawai dari sini
        if ($user->role === User::ROLE_PEGAWAI) {
            abort(403);
        }
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->role === User::ROLE_PEGAWAI) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => ['required', Rule::in([User::ROLE_SUPERADMIN, User::ROLE_STAFF, User::ROLE_HRD, User::ROLE_FINANCE])],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Data user berhasil diperbarui.');
    }

    public function toggleStatus(User $user)
    {
        // Jangan biarkan superadmin menonaktifkan dirinya sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menonaktifkan akun Anda sendiri.');
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "User $user->name berhasil $status.");
    }
}
