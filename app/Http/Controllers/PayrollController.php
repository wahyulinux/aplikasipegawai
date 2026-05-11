<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index()
    {
        $payrolls = Payroll::with('employee')->latest()->get();
        return view('payrolls.index', compact('payrolls'));
    }

    public function create()
    {
        $employees = Employee::all();
        return view('payrolls.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'bulan' => 'required',
            'gaji_pokok' => 'nullable|numeric',
            'tunjangan_jabatan' => 'nullable|numeric',
            'uang_makan' => 'nullable|numeric',
            'uang_kinerja' => 'nullable|numeric',
            'uang_psb' => 'nullable|numeric',
            'uang_kerajinan' => 'nullable|numeric',
            'uang_extra_fooding' => 'nullable|numeric',
            'insentif_narik_jalur' => 'nullable|numeric',
            'uang_lembur' => 'nullable|numeric',
            'uang_piket' => 'nullable|numeric',
            'potongan_kinerja' => 'nullable|numeric',
            'bpjs_ketenagakerjaan' => 'nullable|numeric',
            'potongan_pinjaman' => 'nullable|numeric',
        ]);

        Payroll::create($data);

        return redirect()->route('payrolls.index')->with('success', 'Payroll berhasil disimpan');
    }

    public function edit(Payroll $payroll)
    {
        if ($payroll->status === Payroll::STATUS_APPROVED) {
            return redirect()->route('payrolls.index')->with('error', 'Data yang sudah disetujui tidak dapat diubah');
        }

        $employees = Employee::all();
        return view('payrolls.edit', compact('payroll', 'employees'));
    }

    public function update(Request $request, Payroll $payroll)
    {
        if ($payroll->status === Payroll::STATUS_APPROVED) {
            return redirect()->route('payrolls.index')->with('error', 'Data yang sudah disetujui tidak dapat diubah');
        }

        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'bulan' => 'required',
            'gaji_pokok' => 'nullable|numeric',
            'tunjangan_jabatan' => 'nullable|numeric',
            'uang_makan' => 'nullable|numeric',
            'uang_kinerja' => 'nullable|numeric',
            'uang_psb' => 'nullable|numeric',
            'uang_kerajinan' => 'nullable|numeric',
            'uang_extra_fooding' => 'nullable|numeric',
            'insentif_narik_jalur' => 'nullable|numeric',
            'uang_lembur' => 'nullable|numeric',
            'uang_piket' => 'nullable|numeric',
            'potongan_kinerja' => 'nullable|numeric',
            'bpjs_ketenagakerjaan' => 'nullable|numeric',
            'potongan_pinjaman' => 'nullable|numeric',
        ]);

        $payroll->update($data);

        return redirect()->route('payrolls.index')->with('success', 'Payroll berhasil diperbarui');
    }

    public function show(Payroll $payroll)
    {
        return view('payrolls.show', compact('payroll'));
    }

    public function approve(Payroll $payroll)
    {
        $payroll->update(['status' => Payroll::STATUS_APPROVED]);
        return redirect()->back()->with('success', 'Payroll berhasil disetujui oleh HRD');
    }

    public function destroy(Payroll $payroll)
    {
        $payroll->delete();
        return redirect()->route('payrolls.index')->with('success', 'Data penggajian berhasil dihapus');
    }
}
