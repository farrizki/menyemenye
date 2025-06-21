<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LogDafnom;
use App\Jobs\ProcessDafnom;
use App\Models\RefKecamatan;
use App\Models\RefKelurahan;

class DafnomController extends Controller
{
    public function create()
    {
        $kecamatans = RefKecamatan::orderBy('nm_kecamatan')->get();
        return view('dafnom.create', compact('kecamatans'));
    }

    public function getKelurahanByKecamatan($kd_kecamatan)
    {
        $kelurahans = RefKelurahan::where('kd_kecamatan', $kd_kecamatan)
                                ->orderBy('nm_kelurahan')
                                ->get();
        return response()->json($kelurahans);
    }
    
    public function store(Request $request)
{
    $request->validate([
        'jenis_wilayah' => 'required|in:semua,kecamatan,kelurahan',
        'kd_kecamatan' => 'required_if:jenis_wilayah,kecamatan,kelurahan',
        'kd_kelurahan' => 'required_if:jenis_wilayah,kelurahan',
        'tahun_pembentukan' => 'required|integer',
        'metode' => 'required|in:ulang,susulan',
        'no_formulir' => 'nullable|string|max:255',
    ]);

    $wilayahText = 'Semua Kecamatan';
    if ($request->jenis_wilayah === 'kecamatan') {
        // Gunakan koneksi oracle untuk query referensi
        $namaKecamatan = RefKecamatan::on('oracle')->where('kd_kecamatan', $request->kd_kecamatan)->first()->nm_kecamatan;
        $wilayahText = 'Kecamatan: ' . $namaKecamatan;
    } elseif ($request->jenis_wilayah === 'kelurahan') {
        // Gunakan koneksi oracle untuk query referensi
        $kecamatan = RefKecamatan::on('oracle')->where('kd_kecamatan', $request->kd_kecamatan)->first()->nm_kecamatan;
        $kelurahan = RefKelurahan::on('oracle')->where('kd_kecamatan', $request->kd_kecamatan)->where('kd_kelurahan', $request->kd_kelurahan)->first()->nm_kelurahan;
        $wilayahText = "Kelurahan: $kelurahan, Kecamatan: $kecamatan";
    }

    // Simpan semua informasi yang dibutuhkan oleh Job ke dalam Log
    $log = LogDafnom::create([
        'tahun_pembentukan' => $request->tahun_pembentukan,
        'metode' => $request->metode,
        'wilayah_text' => $wilayahText,
        'kd_kecamatan' => $request->kd_kecamatan,
        'kd_kelurahan' => $request->kd_kelurahan,
        'no_formulir' => $request->no_formulir,
        'user_id' => Auth::id(),
        'user_name' => Auth::user()->name,
        'user_nip' => Auth::user()->nip,
        'status' => 'pending',
    ]);

    // Panggil Job hanya dengan ID, ini cara paling stabil.
    ProcessDafnom::dispatch($log->id);

    return redirect()->route('dafnom.create')
        ->with('success', 'Permintaan pembentukan Dafnom telah diterima dan sedang diproses di latar belakang.')
        ->with('monitoring_log_id', $log->id);
}

    public function getLogStatus(LogDafnom $log)
    {
        if ($log->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return response()->json($log);
    }
}