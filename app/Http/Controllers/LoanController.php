<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    public function index()
    {
        $query = Loan::with('employee')->latest();

        if (Auth::user()->role === 'pegawai') {
            $query->where('employee_id', Auth::user()->employee_id);
        }

        $loans = $query->get();
        return view('loans.index', compact('loans'));
    }

    public function create()
    {
        if (Auth::user()->role !== 'pegawai') {
            return redirect()->route('loans.index')->with('error', 'Hanya pegawai yang dapat mengajukan pinjaman.');
        }
        return view('loans.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'pegawai') {
            abort(403);
        }

        // Hitung pinjaman aktif (pending atau approved)
        $activeLoansCount = Loan::where('employee_id', Auth::user()->employee_id)
            ->whereIn('status', [Loan::STATUS_PENDING, Loan::STATUS_APPROVED])
            ->count();

        if ($activeLoansCount >= 3) {
            return back()->with('error', 'Anda sudah memiliki 3 pinjaman aktif. Selesaikan pinjaman sebelumnya terlebih dahulu.');
        }

        $request->validate([
            'nominal_pinjaman' => 'required|numeric|min:1000',
            'tenor_bulan' => 'required|integer|min:1|max:60',
            'keterangan' => 'nullable|string',
        ]);

        $nominal_cicilan = $request->nominal_pinjaman / $request->tenor_bulan;

        Loan::create([
            'employee_id' => Auth::user()->employee_id,
            'nominal_pinjaman' => $request->nominal_pinjaman,
            'tenor_bulan' => $request->tenor_bulan,
            'nominal_cicilan' => $nominal_cicilan,
            'sisa_pinjaman' => $request->nominal_pinjaman,
            'keterangan' => $request->keterangan,
            'status' => Loan::STATUS_PENDING,
        ]);

        return redirect()->route('loans.index')->with('success', 'Pengajuan pinjaman berhasil dikirim.');
    }

    public function approve(Loan $loan)
    {
        if (Auth::user()->role !== 'finance') {
            abort(403);
        }

        if ($loan->status !== Loan::STATUS_PENDING) {
            return back()->with('error', 'Pinjaman ini tidak dapat disetujui.');
        }

        $loan->update([
            'status' => Loan::STATUS_APPROVED,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Pinjaman berhasil disetujui.');
    }

    public function reject(Loan $loan)
    {
        if (Auth::user()->role !== 'finance') {
            abort(403);
        }

        if ($loan->status !== Loan::STATUS_PENDING) {
            return back()->with('error', 'Pinjaman ini tidak dapat ditolak.');
        }

        $loan->update([
            'status' => Loan::STATUS_REJECTED,
        ]);

        return back()->with('success', 'Pinjaman telah ditolak.');
    }
}
