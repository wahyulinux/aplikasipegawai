@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white p-10 rounded-2xl shadow-2xl border border-gray-100">
    <h1 class="text-2xl font-bold mb-6">Edit User Operasional</h1>
    
    <form action="{{ route('users.update', $user->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Role</label>
            <select name="role" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="staff" {{ $user->role === 'staff' ? 'selected' : '' }}>Staff (Maker Gaji)</option>
                <option value="hrd" {{ $user->role === 'hrd' ? 'selected' : '' }}>HRD (Approver Gaji)</option>
                <option value="finance" {{ $user->role === 'finance' ? 'selected' : '' }}>Finance (Approver Pinjaman)</option>
                <option value="superadmin" {{ $user->role === 'superadmin' ? 'selected' : '' }}>Superadmin (IT Support)</option>
            </select>
        </div>

        <div class="pt-4 border-t">
            <p class="text-xs text-gray-500 mb-2 italic">Kosongkan password jika tidak ingin mengubahnya.</p>
            <label class="block text-sm font-bold text-gray-700 mb-1">Password Baru</label>
            <input type="password" name="password" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
        </div>

        <div class="flex items-center justify-between pt-6">
            <a href="{{ route('users.index') }}" class="text-gray-500 hover:underline">Batal</a>
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold shadow-lg">Update User</button>
        </div>
    </form>
</div>
@endsection
