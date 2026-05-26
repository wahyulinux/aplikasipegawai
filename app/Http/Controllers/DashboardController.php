<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Loan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Jika bukan pegawai, redirect ke halaman payroll (Dashboard HRD/Staff)
        if ($user->role !== 'pegawai') {
            return redirect()->route('payrolls.index');
        }

        $employeeId = $user->employee_id;
        $currentMonth = Carbon::now()->format('Y-m');

        // 1. Ambil Pinjaman Aktif (Sisa Hutang)
        $activeLoans = Loan::where('employee_id', $employeeId)
            ->whereIn('status', [Loan::STATUS_PENDING, Loan::STATUS_APPROVED])
            ->get();
        
        $totalSisaHutang = $activeLoans->sum('sisa_pinjaman');
        $totalCicilanBulanIni = $activeLoans->where('status', Loan::STATUS_APPROVED)->sum('nominal_cicilan');

        // 2. Ambil Penghasilan WO Bulan Ini (PSB)
        $totalPsbBulanIni = DB::table('employee_psb')
            ->join('psb_work_orders', 'employee_psb.psb_work_order_id', '=', 'psb_work_orders.id')
            ->where('employee_psb.employee_id', $employeeId)
            ->where('psb_work_orders.tanggal_pengerjaan', 'like', $currentMonth . '%')
            ->sum('nominal_diterima');

        // 3. Ambil Penghasilan WO Bulan Ini (ITJ)
        $totalItjBulanIni = DB::table('employee_itj')
            ->join('itj_work_orders', 'employee_itj.itj_work_order_id', '=', 'itj_work_orders.id')
            ->where('employee_itj.employee_id', $employeeId)
            ->where('itj_work_orders.tanggal_pengerjaan', 'like', $currentMonth . '%')
            ->sum('nominal_diterima');

        $totalWoBulanIni = $totalPsbBulanIni + $totalItjBulanIni;

        return view('dashboard.pegawai', compact(
            'activeLoans', 
            'totalSisaHutang', 
            'totalCicilanBulanIni', 
            'totalPsbBulanIni', 
            'totalItjBulanIni', 
            'totalWoBulanIni',
            'currentMonth'
        ));
    }
}
