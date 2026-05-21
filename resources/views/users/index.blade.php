@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Manajemen Pengguna</h1>
    <a href="{{ route('users.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-bold shadow-md transition">Tambah User Operasional</a>
</div>

<div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
    <table class="min-w-full">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-bold tracking-wider">
            <tr>
                <th class="py-4 px-6 text-left">Nama Lengkap</th>
                <th class="py-4 px-6 text-left">Email</th>
                <th class="py-4 px-6 text-center">Role</th>
                <th class="py-4 px-6 text-center">Status</th>
                <th class="py-4 px-6 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 text-sm">
            @foreach($users as $user)
            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                <td class="py-4 px-6 font-bold text-gray-800">{{ $user->name }}</td>
                <td class="py-4 px-6">{{ $user->email }}</td>
                <td class="py-4 px-6 text-center">
                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest
                        {{ $user->role === 'superadmin' ? 'bg-purple-100 text-purple-700' : '' }}
                        {{ $user->role === 'hrd' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $user->role === 'staff' ? 'bg-indigo-100 text-indigo-700' : '' }}
                        {{ $user->role === 'finance' ? 'bg-orange-100 text-orange-700' : '' }}
                    ">
                        {{ $user->role }}
                    </span>
                </td>
                <td class="py-4 px-6 text-center">
                    @if($user->is_active)
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-[10px] font-bold uppercase">Aktif</span>
                    @else
                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-[10px] font-bold uppercase">Non-Aktif</span>
                    @endif
                </td>
                <td class="py-4 px-6 text-center space-x-2">
                    <a href="{{ route('users.edit', $user->id) }}" class="text-blue-600 hover:underline font-bold">Edit</a>
                    
                    @if($user->id !== auth()->id())
                        <form action="{{ route('users.toggle', $user->id) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="{{ $user->is_active ? 'text-red-500' : 'text-green-500' }} hover:underline font-bold" 
                                onclick="return confirm('Apakah Anda yakin ingin {{ $user->is_active ? 'menonaktifkan' : 'mengaktifkan' }} user ini?')">
                                {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
