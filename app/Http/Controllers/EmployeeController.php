<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

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
            'email' => 'nullable|email',
            'jabatan' => 'required',
            'nomor_rekening' => 'nullable|string',
        ]);

        Employee::create($request->all());

        return redirect()->route('employees.index')->with('success', 'Pegawai berhasil ditambahkan');
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
            'email' => 'nullable|email',
            'jabatan' => 'required',
            'nomor_rekening' => 'nullable|string',
        ]);

        $employee->update($request->all());

        return redirect()->route('employees.index')->with('success', 'Data pegawai berhasil diperbarui');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Pegawai berhasil dihapus');
    }
}
