<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    public function edit()
    {
        // Proteksi: Pegawai tidak boleh ganti password dari sini
        if (auth()->user()->role === 'pegawai') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return view('auth.passwords.change');
    }

    public function update(Request $request)
    {
        if (auth()->user()->role === 'pegawai') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'min:8', 'confirmed'],
        ], [
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('payrolls.index')->with('success', 'Password Anda berhasil diperbarui.');
    }
}
