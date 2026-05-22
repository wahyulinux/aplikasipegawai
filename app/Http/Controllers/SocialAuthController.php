<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Gagal mengambil data dari Google.');
        }

        // --- SKENARIO 1: User Sedang Login (Account Binding) ---
        if (Auth::check()) {
            $currentUser = Auth::user();

            // Cek apakah akun Google ini sudah dipakai orang lain
            $existingUser = User::where('google_id', $googleUser->id)->first();
            if ($existingUser && $existingUser->id !== $currentUser->id) {
                return redirect()->route('payrolls.index')->with('error', 'Akun Google ini sudah tertaut dengan pengguna lain.');
            }

            // Tautkan Google ID ke user yang sedang login
            $currentUser->update([
                'google_id' => $googleUser->id,
                'google_token' => $googleUser->token,
            ]);

            return redirect()->route('payrolls.index')->with('success', 'Akun Google berhasil ditautkan! Sekarang Anda bisa login menggunakan Google.');
        }

        // --- SKENARIO 2: User Belum Login (Login via SSO) ---
        $user = User::where('google_id', $googleUser->id)->first();

        if ($user) {
            // Cek apakah akun aktif
            if (!$user->is_active) {
                return redirect()->route('login')->with('error', 'Akun Anda telah dinonaktifkan.');
            }

            Auth::login($user);
            request()->session()->regenerate();

            if ($user->isSuperadmin()) {
                return redirect()->route('users.index');
            }
            return redirect()->intended('payrolls');
        }

        // Jika Google ID tidak ditemukan, cek apakah emailnya terdaftar tapi belum ditautkan
        $userByEmail = User::where('email', $googleUser->email)->first();
        if ($userByEmail) {
            return redirect()->route('login')->with('error', 'Email Anda terdaftar, tapi belum ditautkan ke Google. Silakan login manual dulu lalu tautkan di menu Profil.');
        }

        return redirect()->route('login')->with('error', 'Akun Anda belum terdaftar di sistem. Hubungi HRD.');
    }
}
