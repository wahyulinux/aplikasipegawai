<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Services\TelegramService;
use App\Models\User;

class PayrollController extends Controller
{
    protected $telegram;

    public function __construct(TelegramService $telegram)
    {
        $this->telegram = $telegram;
    }

    public function getEmployeeComponents(Request $request, Employee $employee)
    {
        $bulan = $request->query('bulan'); // Format: YYYY-MM
        
        // 1. Hitung Potongan Pinjaman Aktif (Total Cicilan)
        $totalLoanDeduction = $employee->loans()
            ->where('status', \App\Models\Loan::STATUS_APPROVED)
            ->sum('nominal_cicilan');

        // 2. Hitung Total Uang PSB di bulan tersebut
        $totalPsb = 0;
        if ($bulan) {
            $totalPsb = \DB::table('employee_psb')
                ->join('psb_work_orders', 'employee_psb.psb_work_order_id', '=', 'psb_work_orders.id')
                ->where('employee_psb.employee_id', $employee->id)
                ->where('psb_work_orders.tanggal_pengerjaan', 'like', $bulan . '%')
                ->sum('nominal_diterima');
        }

        // 3. Hitung Total Insentif Tarik Jalur (ITJ) di bulan tersebut
        $totalItj = 0;
        if ($bulan) {
            $totalItj = \DB::table('employee_itj')
                ->join('itj_work_orders', 'employee_itj.itj_work_order_id', '=', 'itj_work_orders.id')
                ->where('employee_itj.employee_id', $employee->id)
                ->where('itj_work_orders.tanggal_pengerjaan', 'like', $bulan . '%')
                ->sum('nominal_diterima');
        }

        return response()->json([
            'loan_deduction' => $totalLoanDeduction,
            'psb_total' => $totalPsb,
            'itj_total' => $totalItj
        ]);
    }

    public function index()
    {
        $query = Payroll::with('employee')->latest();

        // Jika login sebagai pegawai, hanya tampilkan slip gaji miliknya (semua status)
        if (auth()->user()->role === 'pegawai') {
            $query->where('employee_id', auth()->user()->employee_id);
        }

        $payrolls = $query->get();
        return view('payrolls.index', compact('payrolls'));
    }

    public function create()
    {
        $employees = Employee::all();
        $defaultBpjsTk = \App\Models\Setting::get('bpjs_ketenagakerjaan_default', 0);
        $defaultBpjsKes = \App\Models\Setting::get('bpjs_kesehatan_default', 0);
        
        return view('payrolls.create', compact('employees', 'defaultBpjsTk', 'defaultBpjsKes'));
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
            'bpjs_kesehatan' => 'nullable|numeric',
            'potongan_pinjaman' => 'nullable|numeric',
        ]);

        $payroll = Payroll::create($data);

        // Notifikasi ke HRD: Draft Gaji Baru
        $hrds = User::where('role', User::ROLE_HRD)->where('is_active', true)->get();
        $message = "📄 *Draft Gaji Baru Memerlukan Persetujuan*\n\n"
                 . "Halo HRD, draft slip gaji untuk pegawai *{$payroll->employee->nama}* periode *{$payroll->bulan}* telah dibuat oleh Staff.\n\n"
                 . "Total THP: *Rp " . number_format($payroll->gaji_bersih, 0, ',', '.') . "*\n"
                 . "Silakan login ke aplikasi untuk mereview.";

        foreach ($hrds as $hrd) {
            if ($hrd->telegram_chat_id) {
                $this->telegram->sendMessage($hrd->telegram_chat_id, $message);
            }
        }

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
            'bpjs_kesehatan' => 'nullable|numeric',
            'potongan_pinjaman' => 'nullable|numeric',
        ]);

        $payroll->update($data);

        return redirect()->route('payrolls.index')->with('success', 'Payroll berhasil diperbarui');
    }

    public function show(Request $request, Payroll $payroll)
    {
        // Cek apakah ini akses publik melalui scan QR Code
        $verifyToken = $request->query('verify');
        if ($verifyToken) {
            if ($payroll->verification_code === $verifyToken && $payroll->status === Payroll::STATUS_APPROVED) {
                // Token valid dan slip sudah di-approve, izinkan akses publik
                return view('payrolls.show', compact('payroll'));
            } else {
                // Token tidak valid atau slip belum di-approve
                abort(403, 'Kode verifikasi tidak valid atau slip gaji belum disetujui.');
            }
        }

        // Jika tidak ada token verify, pastikan user sudah login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Proteksi: Pegawai hanya boleh melihat slip gajinya sendiri
        if (auth()->user()->role === 'pegawai') {
            if ($payroll->employee_id !== auth()->user()->employee_id) {
                abort(403, 'Anda tidak memiliki akses untuk melihat slip gaji ini.');
            }
        }

        return view('payrolls.show', compact('payroll'));
    }

    public function acknowledge(Payroll $payroll)
    {
        if (auth()->user()->role !== 'pegawai' || $payroll->employee_id !== auth()->user()->employee_id) {
            abort(403, 'Anda tidak berhak melakukan aksi ini.');
        }

        if ($payroll->status !== Payroll::STATUS_APPROVED) {
            return back()->with('error', 'Hanya slip gaji yang sudah di-approve yang bisa dikonfirmasi.');
        }

        $payroll->update([
            'is_acknowledged' => true,
            'acknowledged_at' => now(),
        ]);

        return back()->with('success', 'Terima kasih, Anda telah mengonfirmasi penerimaan slip gaji ini.');
    }

    public function printAll(Request $request)
    {
        $bulan = $request->query('bulan');
        
        $query = Payroll::with('employee')
            ->whereIn('status', [Payroll::STATUS_APPROVED, Payroll::STATUS_PENDING]);
        
        if ($bulan) {
            $query->where('bulan', $bulan);
        }
        
        $payrolls = $query->latest()->get();
        
        return view('payrolls.print_all', compact('payrolls', 'bulan'));
    }

    public function approve(Payroll $payroll)
    {
        $payroll->update([
            'status' => Payroll::STATUS_APPROVED,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Notifikasi ke Pegawai: Gaji Disetujui
        if ($payroll->employee->telegram_chat_id) {
            $message = "💰 *Slip Gaji Tersedia!*\n\n"
                     . "Halo *{$payroll->employee->nama}*, slip gaji Anda untuk periode *{$payroll->bulan}* telah diterbitkan dan disetujui.\n\n"
                     . "Silakan cek dan konfirmasi penerimaan di aplikasi Payroll.";
            $this->telegram->sendMessage($payroll->employee->telegram_chat_id, $message);
        }

        // Logic Pinjaman: Kurangi sisa pinjaman jika ada potongan_pinjaman di slip ini
        if ($payroll->potongan_pinjaman > 0) {
            $remainingDeduction = $payroll->potongan_pinjaman;
            
            // Ambil semua pinjaman aktif (APPROVED) milik pegawai ini, urutkan dari yang terlama
            $activeLoans = $payroll->employee->loans()
                ->where('status', \App\Models\Loan::STATUS_APPROVED)
                ->orderBy('created_at', 'asc')
                ->get();

            foreach ($activeLoans as $loan) {
                if ($remainingDeduction <= 0) break;

                // Tentukan berapa yang bisa dikurangi dari pinjaman ini
                // Biasanya sebesar nominal_cicilan, tapi kita pastikan tidak melebihi sisa_pinjaman
                $amountToSubtract = min($loan->nominal_cicilan, $loan->sisa_pinjaman, $remainingDeduction);

                $newBalance = $loan->sisa_pinjaman - $amountToSubtract;
                $loan->update([
                    'sisa_pinjaman' => $newBalance,
                    'status' => $newBalance <= 0 ? \App\Models\Loan::STATUS_LUNAS : \App\Models\Loan::STATUS_APPROVED
                ]);

                $remainingDeduction -= $amountToSubtract;
            }
        }

        return redirect()->back()->with('success', 'Payroll berhasil disetujui dan saldo pinjaman pegawai telah diperbarui');
    }

    public function destroy(Payroll $payroll)
    {
        $payroll->delete();
        return redirect()->route('payrolls.index')->with('success', 'Data penggajian berhasil dihapus');
    }

    public function exportExcel(Request $request)
    {
        $bulan = $request->query('bulan');
        
        $query = Payroll::with('employee')
            ->where('status', Payroll::STATUS_APPROVED);
        
        if ($bulan) {
            $query->where('bulan', $bulan);
        }
        
        $payrolls = $query->latest()->get();
        $filename = "payroll_" . ($bulan ?: 'all') . "_" . date('Ymd') . ".xlsx";

        // Path ke file template
        $templatePath = base_path('Payroll.xlsx');

        if (!file_exists($templatePath)) {
            return back()->with('error', 'File template Payroll.xlsx tidak ditemukan di root project.');
        }

        // Load file template
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Asumsi data diisi mulai dari baris ke-2 (karena baris 1 adalah header dari template Anda)
        $rowNum = 2;
        foreach ($payrolls as $payroll) {
            // Tunjangan Gabungan
            $tunjangan_lainnya = $payroll->uang_kinerja + $payroll->uang_psb + 
                                 $payroll->uang_kerajinan + $payroll->uang_extra_fooding + 
                                 $payroll->insentif_narik_jalur + $payroll->uang_piket;
                                 
            // Potongan Gabungan
            $potongan_lainnya = $payroll->potongan_kinerja + $payroll->potongan_pinjaman;

            $sheet->setCellValue('A' . $rowNum, $payroll->employee->nip);
            $sheet->setCellValue('B' . $rowNum, $payroll->employee->nama);
            $sheet->setCellValue('C' . $rowNum, $payroll->employee->jabatan);
            $sheet->setCellValue('D' . $rowNum, $payroll->employee->email);
            // Set nomor rekening sebagai string eksplisit
            $sheet->setCellValueExplicit('E' . $rowNum, $payroll->employee->nomor_rekening, DataType::TYPE_STRING);
            $sheet->setCellValue('F' . $rowNum, $payroll->gaji_pokok);
            $sheet->setCellValue('G' . $rowNum, $payroll->uang_lembur);
            $sheet->setCellValue('H' . $rowNum, $payroll->tunjangan_jabatan);
            $sheet->setCellValue('I' . $rowNum, $payroll->uang_makan);
            $sheet->setCellValue('J' . $rowNum, $tunjangan_lainnya);
            $sheet->setCellValue('K' . $rowNum, "Tunjangan Kerja");
            $sheet->setCellValue('L' . $rowNum, 0); // Pajak Pendapatan
            $sheet->setCellValue('M' . $rowNum, $payroll->bpjs_kesehatan);
            $sheet->setCellValue('N' . $rowNum, $payroll->bpjs_ketenagakerjaan);
            $sheet->setCellValue('O' . $rowNum, $potongan_lainnya);
            $sheet->setCellValue('P' . $rowNum, "Potongan Kehadiran");

            $rowNum++;
        }

        $writer = new Xlsx($spreadsheet);
        
        // Bersihkan output buffer
        if (ob_get_length()) {
            ob_end_clean();
        }

        // Simpan file sementara di storage
        $tempPath = storage_path('app/' . $filename);
        $writer->save($tempPath);
        
        // Download file dan hapus dari storage setelah berhasil dikirim
        return response()->download($tempPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
