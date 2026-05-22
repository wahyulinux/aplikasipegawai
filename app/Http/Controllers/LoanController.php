<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    protected $telegram;

    public function __construct(TelegramService $telegram)
    {
        $this->telegram = $telegram;
    }

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

        $loan = Loan::create([
            'employee_id' => Auth::user()->employee_id,
            'nominal_pinjaman' => $request->nominal_pinjaman,
            'tenor_bulan' => $request->tenor_bulan,
            'nominal_cicilan' => $nominal_cicilan,
            'sisa_pinjaman' => $request->nominal_pinjaman,
            'keterangan' => $request->keterangan,
            'status' => Loan::STATUS_PENDING,
        ]);

        // Notifikasi ke Finance: Pengajuan Pinjaman Baru
        $finances = User::where('role', User::ROLE_FINANCE)->where('is_active', true)->get();
        $message = "💰 *Pengajuan Kasbon Baru*\n\n"
                 . "Halo Finance, ada pengajuan pinjaman baru dari *" . Auth::user()->name . "*.\n"
                 . "Nominal: *Rp " . number_format($request->nominal_pinjaman, 0, ',', '.') . "*\n"
                 . "Tenor: *{$request->tenor_bulan} Bulan*\n\n"
                 . "Silakan login untuk memproses.";

        foreach ($finances as $finance) {
            if ($finance->telegram_chat_id) {
                $this->telegram->sendMessage($finance->telegram_chat_id, $message);
            }
        }

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

        // Notifikasi ke Pegawai: Pinjaman Disetujui
        if ($loan->employee->telegram_chat_id) {
            $message = "✅ *Kasbon Disetujui!*\n\n"
                     . "Halo *{$loan->employee->nama}*, pengajuan pinjaman Anda senilai *Rp " . number_format($loan->nominal_pinjaman, 0, ',', '.') . "* telah disetujui oleh Finance dan sedang diproses cair.";
            $this->telegram->sendMessage($loan->employee->telegram_chat_id, $message);
        }

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
