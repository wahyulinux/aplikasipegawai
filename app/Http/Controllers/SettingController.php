<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all();
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'psb_nominal_default' => 'required|numeric|min:0',
            'itj_nominal_default' => 'required|numeric|min:0',
            'bpjs_ketenagakerjaan_default' => 'required|numeric|min:0',
            'bpjs_kesehatan_default' => 'required|numeric|min:0',
        ]);

        $settingPsb = Setting::where('key', 'psb_nominal_default')->first();
        if($settingPsb) {
            $settingPsb->update(['value' => $request->psb_nominal_default]);
        }

        $settingItj = Setting::where('key', 'itj_nominal_default')->first();
        if($settingItj) {
            $settingItj->update(['value' => $request->itj_nominal_default]);
        }

        $settingBpjsTk = Setting::where('key', 'bpjs_ketenagakerjaan_default')->first();
        if($settingBpjsTk) {
            $settingBpjsTk->update(['value' => $request->bpjs_ketenagakerjaan_default]);
        }

        $settingBpjsKes = Setting::where('key', 'bpjs_kesehatan_default')->first();
        if($settingBpjsKes) {
            $settingBpjsKes->update(['value' => $request->bpjs_kesehatan_default]);
        }

        return back()->with('success', 'Pengaturan berhasil diperbarui.');
    }
}
