<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Penggabungan; // Log MySQL
use App\Models\PstDetail;    // Oracle
use App\Models\Sppt;          // Oracle
use App\Models\DafnomOp;      // Oracle
use App\Models\DatObjekPajak; // Oracle
use App\Models\RefKecamatan;  // Oracle
use App\Models\RefKelurahan;  // Oracle
use Carbon\Carbon;

class PenggabunganController extends Controller
{
    public function create()
    {
        return view('penggabungan.create');
    }

    public function fetchData(Request $request)
    {
        $request->validate([
            'no_pelayanan_pembatalan' => 'required|string|size:11',
            'no_pelayanan_pembetulan' => 'required|string|size:11',
        ]);

        try {
            $pstPembatalan = PstDetail::on('oracle')->whereRaw("THN_PELAYANAN || BUNDEL_PELAYANAN || NO_URUT_PELAYANAN = ?", [$request->no_pelayanan_pembatalan])->first();
            $pstPembetulan = PstDetail::on('oracle')->whereRaw("THN_PELAYANAN || BUNDEL_PELAYANAN || NO_URUT_PELAYANAN = ?", [$request->no_pelayanan_pembetulan])->first();

            if (!$pstPembetulan || !$pstPembatalan) {
                return response()->json(['error' => 'Satu atau kedua Nomor Pelayanan tidak ditemukan.'], 404);
            }
            
            $nopBatal = $pstPembatalan->kd_propinsi_pemohon . $pstPembatalan->kd_dati2_pemohon . $pstPembatalan->kd_kecamatan_pemohon . $pstPembatalan->kd_kelurahan_pemohon . $pstPembatalan->kd_blok_pemohon . $pstPembatalan->no_urut_pemohon . $pstPembatalan->kd_jns_op_pemohon;
            $spptBatal = Sppt::on('oracle')->whereRaw("kd_propinsi||kd_dati2||kd_kecamatan||kd_kelurahan||kd_blok||no_urut||kd_jns_op = ?", [$nopBatal])->where('thn_pajak_sppt', $pstPembatalan->thn_pajak_permohonan)->first();
            
            $nopBetul = $pstPembetulan->kd_propinsi_pemohon . $pstPembetulan->kd_dati2_pemohon . $pstPembetulan->kd_kecamatan_pemohon . $pstPembetulan->kd_kelurahan_pemohon . $pstPembetulan->kd_blok_pemohon . $pstPembetulan->no_urut_pemohon . $pstPembetulan->kd_jns_op_pemohon;
            $spptBetul = Sppt::on('oracle')->whereRaw("kd_propinsi||kd_dati2||kd_kecamatan||kd_kelurahan||kd_blok||no_urut||kd_jns_op = ?", [$nopBetul])->where('thn_pajak_sppt', $pstPembetulan->thn_pajak_permohonan)->first();
            
            return response()->json([
                'tahun_pajak' => $pstPembatalan->thn_pajak_permohonan,
                'keterangan_nop' => substr($nopBetul, 13, 4) . '.' . substr($nopBetul, 17, 1),
                'nop_pembatalan' => $this->formatNop($nopBatal),
                'nama_wp_pembatalan' => $spptBatal ? $spptBatal->nm_wp_sppt : 'Nama WP tidak ditemukan',
                'nop_pembetulan' => $this->formatNop($nopBetul),
                'nama_wp_pembetulan' => $spptBetul ? $spptBetul->nm_wp_sppt : 'Nama WP tidak ditemukan',
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengambil data dari Oracle: ' . $e->getMessage()], 500);
        }
    }
    
    public function preview(Request $request)
    {
        $validated = $request->validate([ 
            'nomor_pelayanan_pembatalan' => 'required|string|size:11', 
            'nomor_pelayanan_pembetulan' => 'required|string|size:11', 
            'tahun_pajak' => 'required|digits:4', 
            'bidang' => 'required|in:Pelayanan,Pendataan', 
            'keterangan' => 'required|string', 
            'berkas' => 'required|file|mimes:pdf|max:24576', 
        ]);
        
        $berkasPath = $request->file('berkas')->store('temp_penggabungan', 'local');
        $request->session()->put('penggabungan_berkas_temp', $berkasPath);

        $nopsToProcess = PstDetail::on('oracle')->whereRaw("THN_PELAYANAN || BUNDEL_PELAYANAN || NO_URUT_PELAYANAN = ?", [$validated['nomor_pelayanan_pembatalan']])->get();
        if ($nopsToProcess->isEmpty()) { return redirect()->back()->withErrors('Tidak ada NOP yang ditemukan untuk Nomor Pelayanan Pembatalan yang diberikan.'); }
        
        $dataToProcess = []; 
        $tahunSekarang = date('Y');
        
        foreach ($nopsToProcess as $pst) {
            $nop = $pst->kd_propinsi_pemohon . $pst->kd_dati2_pemohon . $pst->kd_kecamatan_pemohon . $pst->kd_kelurahan_pemohon . $pst->kd_blok_pemohon . $pst->no_urut_pemohon . $pst->kd_jns_op_pemohon;
            $item = ['nop' => $nop, 'formatted_nop' => $this->formatNop($nop)]; 
            $item['thn_pajak_sppt'] = $validated['tahun_pajak'];
            $dafnom = DafnomOp::on('oracle')->whereRaw("kd_propinsi||kd_dati2||kd_kecamatan||kd_kelurahan||kd_blok||no_urut||kd_jns_op = ?", [$nop])->where('thn_pembentukan', $tahunSekarang)->where('kategori_op', 4)->first();
            
            if (!$dafnom) {
                $item['status'] = 'Gagal'; 
                $item['message'] = 'Data Daftar Nominatif tidak valid (Bukan Kategori 4 / Tahun Pembentukan salah).';
            } else {
                 $sppt = Sppt::on('oracle')->whereRaw("kd_propinsi||kd_dati2||kd_kecamatan||kd_kelurahan||kd_blok||no_urut||kd_jns_op = ?", [$nop])->where('thn_pajak_sppt', $validated['tahun_pajak'])->first();
                if ($sppt && $sppt->status_pembayaran_sppt == 1) { 
                    $item['status'] = 'Lunas'; 
                    $item['message'] = 'SPPT sudah lunas/terbayar. Hanya Dafnom yang akan diupdate.';
                } elseif (!$sppt || $sppt->status_pembayaran_sppt != 0) { 
                    $item['status'] = 'Gagal'; 
                    $item['message'] = 'SPPT tidak ditemukan atau status tidak memungkinkan untuk dibatalkan.';
                } else { 
                    $item['status'] = 'Siap Diproses'; 
                    $item['message'] = 'Data valid dan siap untuk digabungkan.'; 
                }
            }
            $item['data_preview'] = $this->getPreviewData($nop, $validated['tahun_pajak']); 
            $dataToProcess[] = $item;
        }

        // ===============================================================
        // =============== PERBAIKAN UNTUK MENGHINDARI ERROR ===============
        // ===============================================================
        // Hapus key 'berkas' dari array sebelum disimpan ke session
        unset($validated['berkas']);
        
        $request->session()->put('penggabungan_data_preview', [ 
            'data' => $dataToProcess, 
            'keterangan' => $validated['keterangan'], 
            'form_data' => $validated, // Sekarang $validated sudah aman untuk disimpan
        ]);
        
        return view('penggabungan.preview', ['preview' => session('penggabungan_data_preview')]);
    }

    public function store(Request $request)
    {
        $previewData = $request->session()->get('penggabungan_data_preview'); $berkasTempPath = $request->session()->get('penggabungan_berkas_temp');
        if (!$previewData) { return redirect()->route('penggabungan.create')->withErrors('Sesi pratinjau tidak ditemukan. Silakan ulangi proses.'); }
        $finalBerkasPath = null;
        if ($berkasTempPath && Storage::disk('local')->exists($berkasTempPath)) {
            $finalBerkasPath = Storage::disk('public')->putFile('berkas_penggabungan', new \Illuminate\Http\File(storage_path('app/' . $berkasTempPath))); Storage::disk('local')->delete($berkasTempPath);
        }
        $user = Auth::user(); $tahunSekarang = date('Y');
        DB::connection('oracle')->beginTransaction();
        try {
            foreach ($previewData['data'] as $item) {
                if (in_array($item['status'], ['Siap Diproses', 'Lunas'])) {
                    $nop = $item['nop'];
                    DafnomOp::on('oracle')->whereRaw("kd_propinsi||kd_dati2||kd_kecamatan||kd_kelurahan||kd_blok||no_urut||kd_jns_op = ?", [$nop])->where('thn_pembentukan', $tahunSekarang)->update([ 'kategori_op' => 3, 'keterangan' => $previewData['keterangan'], 'tgl_pemutakhiran' => DB::raw('SYSDATE'), 'nip_pemutakhir' => $user->nip, ]);
                    if ($item['status'] === 'Siap Diproses') { Sppt::on('oracle')->whereRaw("kd_propinsi||kd_dati2||kd_kecamatan||kd_kelurahan||kd_blok||no_urut||kd_jns_op = ?", [$nop])->where('thn_pajak_sppt', $item['thn_pajak_sppt'])->where('status_pembayaran_sppt', 0)->update(['status_pembayaran_sppt' => 2]); }
                    $this->logPenggabungan($item, $previewData['form_data'], $user, $finalBerkasPath);
                }
            }
            DB::connection('oracle')->commit();
        } catch (\Exception $e) { DB::connection('oracle')->rollBack(); return redirect()->route('penggabungan.create')->withErrors('Terjadi kesalahan fatal saat memproses data di Oracle: ' . $e->getMessage()); }
        $request->session()->forget(['penggabungan_data_preview', 'penggabungan_berkas_temp']);
        return redirect()->route('penggabungan.index')->with('success', 'Proses penggabungan SPPT selesai.');
    }

    public function index(Request $request)
    {
        $query = Penggabungan::orderBy('created_at', 'desc');
        if ($request->has('search') && $request->input('search') != '') {
            $search = strtolower($request->input('search')); $nopSearch = preg_replace('/[^0-9]/', '', $search);
            $query->where(function ($q) use ($search, $nopSearch) {
                if (!empty($nopSearch)) { $q->orWhereRaw("LOWER(CONCAT(kd_propinsi, kd_dati2, kd_kecamatan, kd_kelurahan, kd_blok, no_urut, kd_jns_op)) LIKE ?", ['%' . $nopSearch . '%']); }
                $q->orWhereRaw("LOWER(nm_wp_sppt) LIKE ?", ['%' . $search . '%'])->orWhereRaw("CAST(thn_pajak_sppt AS CHAR) LIKE ?", ['%' . $search . '%'])->orWhereRaw("LOWER(keterangan_penggabungan) LIKE ?", ['%' . $search . '%']);
            });
        }
        $laporan = $query->paginate(25);
        if ($request->ajax()) { return view('penggabungan.partials.laporan_table', compact('laporan'))->render(); }
        return view('penggabungan.index', compact('laporan'));
    }

    private function getPreviewData($nop, $tahun) {
        $sppt = Sppt::on('oracle')->whereRaw("kd_propinsi||kd_dati2||kd_kecamatan||kd_kelurahan||kd_blok||no_urut||kd_jns_op = ?", [$nop])->where('thn_pajak_sppt', $tahun)->first();
        if(!$sppt) return [];
        $op = DatObjekPajak::on('oracle')->where('kd_propinsi', $sppt->kd_propinsi)->where('kd_dati2', $sppt->kd_dati2)->where('kd_kecamatan', $sppt->kd_kecamatan)->where('kd_kelurahan', $sppt->kd_kelurahan)->where('kd_blok', $sppt->kd_blok)->where('no_urut', $sppt->no_urut)->where('kd_jns_op', $sppt->kd_jns_op)->first();
        $kec = RefKecamatan::on('oracle')->where('kd_kecamatan', $sppt->kd_kecamatan)->first();
        $kel = RefKelurahan::on('oracle')->where('kd_kecamatan', $sppt->kd_kecamatan)->where('kd_kelurahan', $sppt->kd_kelurahan)->first();
        $alamatWp = trim(($sppt->jln_wp_sppt ?? '') . ' RT. ' . ($sppt->rt_wp_sppt ?? '') . ' RW. ' . ($sppt->rw_wp_sppt ?? '') . ' Kel/Desa ' . ($sppt->kelurahan_wp_sppt ?? '') . ' Kab/Kota ' . ($sppt->kota_wp_sppt ?? ''));
        $alamatOp = trim(($op->jalan_op ?? '') . ' RT. ' . ($op->rt_op ?? '') . ' RW. ' . ($op->rw_op ?? '') . ' Kel/Desa ' . ($kel->nm_kelurahan ?? '') . ' Kec. ' . ($kec->nm_kecamatan ?? '') . ' Kab/Kota Nganjuk');
        return [ 'nm_wp_sppt' => $sppt->nm_wp_sppt, 'alamat_wp' => $alamatWp, 'alamat_op' => $alamatOp, 'luas_bumi' => $sppt->luas_bumi_sppt, 'luas_bangunan' => $sppt->luas_bng_sppt, 'pbb_baku' => $sppt->pbb_yg_harus_dibayar_sppt, ];
    }
    
    private function logPenggabungan($item, $formData, $user, $berkasPath) {
        $nop = $item['nop'];
        Penggabungan::create([
            'kd_propinsi' => substr($nop, 0, 2), 'kd_dati2' => substr($nop, 2, 2), 'kd_kecamatan' => substr($nop, 4, 3), 'kd_kelurahan' => substr($nop, 7, 3), 'kd_blok' => substr($nop, 10, 3), 'no_urut' => substr($nop, 13, 4), 'kd_jns_op' => substr($nop, 17, 1),
            'thn_pajak_sppt' => $item['thn_pajak_sppt'], 'nm_wp_sppt' => $item['data_preview']['nm_wp_sppt'] ?? null,
            'alamat_wp' => $item['data_preview']['alamat_wp'] ?? null, 'letak_op' => $item['data_preview']['alamat_op'] ?? null,
            'luas_bumi_sppt' => $item['data_preview']['luas_bumi'] ?? 0, 'luas_bng_sppt' => $item['data_preview']['luas_bangunan'] ?? 0,
            'pbb_terhutang_sppt' => $item['data_preview']['pbb_baku'] ?? 0,
            'nomor_pelayanan_pembatalan' => $formData['nomor_pelayanan_pembatalan'], 'nomor_pelayanan_pembetulan' => $formData['nomor_pelayanan_pembetulan'],
            'bidang' => $formData['bidang'], 'keterangan_penggabungan' => $formData['keterangan'],
            'berkas_path' => $berkasPath, 'status_proses' => $item['status'], 'pesan_proses' => $item['message'], 'operator' => $user->name, 'nip_operator' => $user->nip,
        ]);
    }

    private function formatNop(string $nopRaw): string {
        if (strlen($nopRaw) == 18) {
            return substr($nopRaw, 0, 2) . '.' . substr($nopRaw, 2, 2) . '.' . substr($nopRaw, 4, 3) . '.' .
                   substr($nopRaw, 7, 3) . '.' . substr($nopRaw, 10, 3) . '.' . substr($nopRaw, 13, 4) . '.' .
                   substr($nopRaw, 17, 1);
        }
        return $nopRaw;
    }
}
