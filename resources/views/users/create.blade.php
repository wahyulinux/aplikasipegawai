@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white p-10 rounded-2xl shadow-2xl border border-gray-100">
    <h1 class="text-2xl font-bold mb-6">Tambah User Operasional</h1>
    
    <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Lengkap</label>
            <input type="text" name="name" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Email</label>
            <input type="email" name="email" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Role</label>
            <select name="role" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="staff">Staff (Maker Gaji)</option>
                <option value="hrd">HRD (Approver Gaji)</option>
                <option value="finance">Finance (Approver Pinjaman)</option>
                <option value="superadmin">Superadmin (IT Support)</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Password</label>
            <input type="password" name="password" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
        </div>

        <div class="flex items-center justify-between pt-6">
            <a href="{{ route('users.index') }}" class="text-gray-500 hover:underline">Batal</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold shadow-lg">Simpan User</button>
        </div>
    </form>
</div>
@endsection
