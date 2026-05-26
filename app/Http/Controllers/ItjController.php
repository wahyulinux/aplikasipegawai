<?php

namespace App\Http\Controllers;

use App\Models\ItjWorkOrder;
use App\Models\Employee;
use App\Models\Setting;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class ItjController extends Controller
{
    protected $telegram;

    public function __construct(TelegramService $telegram)
    {
        $this->telegram = $telegram;
    }

    public function index()
    {
        $workOrders = ItjWorkOrder::with('employees')->latest()->get();
        return view('itj.index', compact('workOrders'));
    }

    public function create()
    {
        $employees = Employee::orderBy('nama')->get();
        $defaultNominal = Setting::get('itj_nominal_default', 100000);
        return view('itj.create', compact('employees', 'defaultNominal'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_pengerjaan' => 'required|date',
            'employee_ids' => 'required|array|min:1', // Tidak ada batas maksimal (max:3 dihapus)
            'employee_ids.*' => 'exists:employees,id',
            'keterangan' => 'nullable|string',
        ]);

        // Generate Kode WO Otomatis: ITJ-YYYYMM-XXXX
        $latestWo = ItjWorkOrder::latest('id')->first();
        $nextId = $latestWo ? $latestWo->id + 1 : 1;
        $kode_wo = 'ITJ-' . date('Ym') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        // Ambil nominal dari settings (SNAPSHOT)
        $nominalTotal = Setting::get('itj_nominal_default', 100000);
        
        $workOrder = ItjWorkOrder::create([
            'kode_wo' => $kode_wo,
            'tanggal_pengerjaan' => $request->tanggal_pengerjaan,
            'nominal_total' => $nominalTotal,
            'keterangan' => $request->keterangan,
        ]);

        // Hitung pembagian nominal
        $count = count($request->employee_ids);
        $nominalPerPerson = floor($nominalTotal / $count); // Pembulatan ke bawah (Integer)

        // Simpan ke pivot table
        $employeeNames = [];
        foreach ($request->employee_ids as $employeeId) {
            $workOrder->employees()->attach($employeeId, [
                'nominal_diterima' => $nominalPerPerson
            ]);
            
            $emp = Employee::find($employeeId);
            $employeeNames[] = $emp->nama;

            // Notifikasi ke Pegawai
            if ($emp->telegram_chat_id) {
                $msgPegawai = "🔌 *Tugas Tarik Jalur (ITJ) Baru*\n\n"
                            . "Halo *{$emp->nama}*, Anda telah ditugaskan untuk pekerjaan Tarik Jalur.\n\n"
                            . "Kode: *{$kode_wo}*\n"
                            . "Tanggal: *{$request->tanggal_pengerjaan}*\n"
                            . "Pendapatan Anda: *Rp " . number_format($nominalPerPerson, 0, ',', '.') . "*\n\n"
                            . "_Semangat bekerja! Nominal ini akan otomatis masuk ke slip gaji bulan ini._";
                $this->telegram->sendMessage($emp->telegram_chat_id, $msgPegawai);
            }
        }

        // Notifikasi ke HRD
        $hrds = User::where('role', User::ROLE_HRD)->where('is_active', true)->get();
        $namaPekerja = implode(", ", $employeeNames);
        $message = "🔌 *Work Order ITJ (Tarik Jalur) Baru*\n\n"
                 . "Kode: *{$kode_wo}*\n"
                 . "Tanggal: *{$request->tanggal_pengerjaan}*\n"
                 . "Pekerja: *{$namaPekerja}*\n"
                 . "Nominal Per Orang: *Rp " . number_format($nominalPerPerson, 0, ',', '.') . "*\n\n"
                 . "_Telah diinput oleh Staff dan akan otomatis masuk ke perhitungan gaji bulan ini._";

        foreach ($hrds as $hrd) {
            if ($hrd->telegram_chat_id) {
                $this->telegram->sendMessage($hrd->telegram_chat_id, $message);
            }
        }

        return redirect()->route('itj.index')->with('success', 'Data WO Tarik Jalur (ITJ) berhasil disimpan.');
    }

    public function destroy(ItjWorkOrder $itj)
    {
        $itj->delete();
        return redirect()->route('itj.index')->with('success', 'Data WO Tarik Jalur (ITJ) berhasil dihapus.');
    }
}
