<?php

namespace App\Http\Controllers;

use App\Models\Overtime;
use App\Models\Employee;
use App\Models\Setting;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class OvertimeController extends Controller
{
    protected $telegram;

    public function __construct(TelegramService $telegram)
    {
        $this->telegram = $telegram;
    }

    public function index()
    {
        $overtimes = Overtime::with('employees')->latest('tanggal')->simplePaginate(50);
        return view('overtime.index', compact('overtimes'));
    }

    public function create()
    {
        $employees = Employee::orderBy('nama')->get();
        $defaultNominal = Setting::get('lembur_nominal_default', 50000);
        return view('overtime.create', compact('employees', 'defaultNominal'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'exists:employees,id',
            'keterangan' => 'nullable|string',
        ]);

        $nominalPerOrang = Setting::get('lembur_nominal_default', 50000);
        
        $overtime = Overtime::create([
            'tanggal' => $request->tanggal,
            'nominal_per_orang' => $nominalPerOrang,
            'keterangan' => $request->keterangan,
        ]);

        $employeeNames = [];
        foreach ($request->employee_ids as $employeeId) {
            $overtime->employees()->attach($employeeId);
            
            $emp = Employee::find($employeeId);
            $employeeNames[] = $emp->nama;

            if ($emp->telegram_chat_id) {
                $msgPegawai = "⏰ *Tugas Lembur Baru*\n\n"
                            . "Halo *{$emp->nama}*, Anda telah ditugaskan untuk Lembur.\n\n"
                            . "Tanggal: *{$request->tanggal}*\n"
                            . "Pendapatan Lembur: *Rp " . number_format($nominalPerOrang, 0, ',', '.') . "*\n\n"
                            . "_Nominal ini akan otomatis masuk ke slip gaji bulan ini._";
                $this->telegram->sendMessage($emp->telegram_chat_id, $msgPegawai);
            }
        }

        $hrds = User::where('role', User::ROLE_HRD)->where('is_active', true)->get();
        $namaPekerja = implode(", ", $employeeNames);
        $message = "⏰ *Data Lembur Baru*\n\n"
                 . "Tanggal: *{$request->tanggal}*\n"
                 . "Pekerja: *{$namaPekerja}*\n"
                 . "Nominal Per Orang: *Rp " . number_format($nominalPerOrang, 0, ',', '.') . "*\n\n"
                 . "_Telah diinput oleh Staff._";

        foreach ($hrds as $hrd) {
            if ($hrd->telegram_chat_id) {
                $this->telegram->sendMessage($hrd->telegram_chat_id, $message);
            }
        }

        return redirect()->route('overtime.index')->with('success', 'Data Lembur berhasil disimpan.');
    }

    public function destroy(Overtime $overtime)
    {
        $overtime->delete();
        return redirect()->route('overtime.index')->with('success', 'Data Lembur berhasil dihapus.');
    }

    public function printReport(Request $request)
    {
        $bulan = $request->query('bulan');
        if (!$bulan) return back()->with('error', 'Silakan pilih bulan laporan.');

        $reportData = \DB::table('employee_overtime')
            ->join('overtimes', 'employee_overtime.overtime_id', '=', 'overtimes.id')
            ->join('employees', 'employee_overtime.employee_id', '=', 'employees.id')
            ->select(
                'employees.nama',
                'employees.nip',
                'employees.jabatan',
                \DB::raw('COUNT(overtimes.id) as total_sesi'),
                \DB::raw('SUM(overtimes.nominal_per_orang) as total_nominal')
            )
            ->where('overtimes.tanggal', 'like', $bulan . '%')
            ->groupBy('employees.id', 'employees.nama', 'employees.nip', 'employees.jabatan')
            ->get();

        return view('overtime.report', compact('reportData', 'bulan'));
    }

    public function printDetailReport(Request $request)
    {
        $bulan = $request->query('bulan');
        if (!$bulan) return back()->with('error', 'Silakan pilih bulan laporan.');

        $overtimes = Overtime::with('employees')
            ->where('tanggal', 'like', $bulan . '%')
            ->orderBy('tanggal', 'asc')
            ->get();

        return view('overtime.report_detail', compact('overtimes', 'bulan'));
    }
}
