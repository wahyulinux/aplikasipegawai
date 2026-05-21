<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::all();
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|unique:employees',
            'nama' => 'required',
            'email' => 'nullable|email|unique:users,email',
            'jabatan' => 'required',
            'nomor_rekening' => 'nullable|string',
        ]);

        $employee = Employee::create($request->all());

        // Otomatis buatkan akun user untuk pegawai jika email diisi
        if ($employee->email) {
            User::create([
                'name' => $employee->nama,
                'email' => $employee->email,
                'password' => Hash::make($employee->nip), // Default password adalah NIP
                'role' => 'pegawai',
                'employee_id' => $employee->id,
            ]);
        }

        return redirect()->route('employees.index')->with('success', 'Pegawai berhasil ditambahkan dan Akun Login otomatis dibuat (Password: NIP).');
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'nip' => 'required|unique:employees,nip,' . $employee->id,
            'nama' => 'required',
            'email' => 'nullable|email|unique:users,email,' . ($employee->user ? $employee->user->id : ''),
            'jabatan' => 'required',
            'nomor_rekening' => 'nullable|string',
        ]);

        $employee->update($request->all());

        // Update data user terkait jika ada
        if ($employee->email) {
            $user = User::where('employee_id', $employee->id)->first();
            if ($user) {
                $user->update([
                    'name' => $employee->nama,
                    'email' => $employee->email,
                ]);
            } else {
                // Buat jika sebelumnya belum punya
                User::create([
                    'name' => $employee->nama,
                    'email' => $employee->email,
                    'password' => Hash::make($employee->nip),
                    'role' => 'pegawai',
                    'employee_id' => $employee->id,
                ]);
            }
        }

        return redirect()->route('employees.index')->with('success', 'Data pegawai berhasil diperbarui');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete(); // User terkait akan ikut terhapus karena onDelete('cascade') di migration
        return redirect()->route('employees.index')->with('success', 'Pegawai berhasil dihapus');
    }
}
