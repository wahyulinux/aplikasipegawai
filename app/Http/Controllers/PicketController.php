<?php

namespace App\Http\Controllers;

use App\Models\Picket;
use App\Models\Employee;
use App\Models\Setting;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class PicketController extends Controller
{
    protected $telegram;

    public function __construct(TelegramService $telegram)
    {
        $this->telegram = $telegram;
    }

    public function index()
    {
        $pickets = Picket::with('employees')->latest('tanggal')->simplePaginate(50);
        return view('picket.index', compact('pickets'));
    }

    public function create()
    {
        $employees = Employee::orderBy('nama')->get();
        $defaultNominal = Setting::get('piket_nominal_default', 75000);
        return view('picket.create', compact('employees', 'defaultNominal'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'exists:employees,id',
            'keterangan' => 'nullable|string',
        ]);

        $nominalPerOrang = Setting::get('piket_nominal_default', 75000);
        
        $picket = Picket::create([
            'tanggal' => $request->tanggal,
            'nominal_per_orang' => $nominalPerOrang,
            'keterangan' => $request->keterangan,
        ]);

        $employeeNames = [];
        foreach ($request->employee_ids as $employeeId) {
            $picket->employees()->attach($employeeId);
            
            $emp = Employee::find($employeeId);
            $employeeNames[] = $emp->nama;

            if ($emp->telegram_chat_id) {
                $msgPegawai = "🛡️ *Tugas Piket Baru*\n\n"
                            . "Halo *{$emp->nama}*, Anda telah ditugaskan untuk Piket (Standby).\n\n"
                            . "Tanggal: *{$request->tanggal}*\n"
                            . "Pendapatan Piket: *Rp " . number_format($nominalPerOrang, 0, ',', '.') . "*\n\n"
                            . "_Nominal ini akan otomatis masuk ke slip gaji bulan ini._";
                $this->telegram->sendMessage($emp->telegram_chat_id, $msgPegawai);
            }
        }

        $hrds = User::where('role', User::ROLE_HRD)->where('is_active', true)->get();
        $namaPekerja = implode(", ", $employeeNames);
        $message = "🛡️ *Data Piket Baru*\n\n"
                 . "Tanggal: *{$request->tanggal}*\n"
                 . "Pekerja: *{$namaPekerja}*\n"
                 . "Nominal Per Orang: *Rp " . number_format($nominalPerOrang, 0, ',', '.') . "*\n\n"
                 . "_Telah diinput oleh Staff._";

        foreach ($hrds as $hrd) {
            if ($hrd->telegram_chat_id) {
                $this->telegram->sendMessage($hrd->telegram_chat_id, $message);
            }
        }

        return redirect()->route('picket.index')->with('success', 'Data Piket berhasil disimpan.');
    }

    public function destroy(Picket $picket)
    {
        $picket->delete();
        return redirect()->route('picket.index')->with('success', 'Data Piket berhasil dihapus.');
    }

    public function printReport(Request $request)
    {
        $bulan = $request->query('bulan');
        if (!$bulan) return back()->with('error', 'Silakan pilih bulan laporan.');

        $reportData = \DB::table('employee_picket')
            ->join('pickets', 'employee_picket.picket_id', '=', 'pickets.id')
            ->join('employees', 'employee_picket.employee_id', '=', 'employees.id')
            ->select(
                'employees.nama',
                'employees.nip',
                'employees.jabatan',
                \DB::raw('COUNT(pickets.id) as total_sesi'),
                \DB::raw('SUM(pickets.nominal_per_orang) as total_nominal')
            )
            ->where('pickets.tanggal', 'like', $bulan . '%')
            ->groupBy('employees.id', 'employees.nama', 'employees.nip', 'employees.jabatan')
            ->get();

        return view('picket.report', compact('reportData', 'bulan'));
    }

    public function printDetailReport(Request $request)
    {
        $bulan = $request->query('bulan');
        if (!$bulan) return back()->with('error', 'Silakan pilih bulan laporan.');

        $pickets = Picket::with('employees')
            ->where('tanggal', 'like', $bulan . '%')
            ->orderBy('tanggal', 'asc')
            ->get();

        return view('picket.report_detail', compact('pickets', 'bulan'));
    }
}
