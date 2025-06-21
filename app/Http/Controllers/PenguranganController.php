<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sppt;
use App\Models\Pengurangan;
use App\Models\DatObjekPajak;
use App\Models\RefKelurahan;
use App\Models\RefKecamatan; // Ada typo '->' harusnya '\'
use Illuminate\Support\Facades\DB; // Ada typo '->' harusnya '\'
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PDF;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;

use PhpOffice\PhpSpreadsheet\Spreadsheet; // PENTING: Import ini
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;  // PENTING: Import ini

// PERBAIKAN PENTING: Tambahkan require_once manual ini
// Ini adalah fallback terakhir jika autoloader gagal menemukan kelas Spreadsheet
require_once base_path('vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Spreadsheet.php');
// PERBAIKAN: Tambahkan juga untuk Writer jika nanti error WriterNotFound
require_once base_path('vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Writer/Xlsx.php');


class PenguranganController extends Controller
{
    public function create()
    {
        return view('pengurangan.create');
    }

    public function preview(Request $request)
    {
        $request->validate([
            'nop' => 'required|string',
            'thn_pajak_sppt_input' => 'required|integer|min:2000|max:2100',
            'jenis_pengurangan_dropdown' => 'required|string', // PERBAIKAN: Tambah required
            'persentase' => 'required|numeric|min:0|max:100', // PERBAIKAN: Tambah required
            'nomor_sk' => 'required|string|max:255', // PERBAIKAN: Tambah required
            'tahun_sk' => 'required|integer|min:2000|max:2100', // PERBAIKAN: Tambah required
            'tgl_sk_pengurangan' => 'required|date', // PERBAIKAN: Tambah required
            'berkas' => 'required|file|mimes:pdf|max:24576', // PERBAIKAN: Tambah required
        ]);

        // ... (sisa kode preview() tetap sama) ...
        $nopArray = array_map('trim', explode(',', $request->input('nop')));
        $thnUpdateOracle = (int) $request->input('thn_pajak_sppt_input');
        $jenisPengurangan = $request->input('jenis_pengurangan_dropdown');
        $persentasePengurangan = (float) $request->input('persentase');
        $inputNomorSk = $request->input('nomor_sk');
        $inputTahunSk = $request->input('tahun_sk');
        $tglSkPengurangan = $request->input('tgl_sk_pengurangan');

        $noSkPengurangan = null;
        if (!empty($inputNomorSk) && !empty($inputTahunSk)) {
            $noSkPengurangan = '100.3.3.2/' . $inputNomorSk . '/K/411.403/' . $inputTahunSk;
        } else {
            $noSkPengurangan = null;
        }

        $berkasPath = null;
        if ($request->hasFile('berkas')) {
            $file = $request->file('berkas');
            $originalFileName = $file->getClientOriginalName();
            $fileExtension = $file->getClientOriginalExtension();
            $fileName = Str::random(40) . '.' . $fileExtension;

            $tempPath = $file->storeAs('temp_berkas_uploads', $fileName, 'local'); 

            $request->session()->put('berkas_temp_path', $tempPath);
            $request->session()->put('berkas_original_name', $originalFileName);
        } else {
            // PERBAIKAN: Jika berkas tidak diupload, jangan hapus session yang mungkin menyimpan path lama dari old()
            // Tapi karena sekarang required, ini tidak terlalu masalah.
            $request->session()->forget('berkas_temp_path');
            $request->session()->forget('berkas_original_name');
        }

        $dataToProcess = [];
        $thnBetweenEnd = $thnUpdateOracle - 1;

        foreach ($nopArray as $nop) {
            if (strlen($nop) != 18) {
                $dataToProcess[] = [
                    'nop' => $nop,
                    'status_validasi' => 'Gagal',
                    'message' => 'Format NOP tidak valid: ' . $nop . '. Harusnya 18 karakter (gabungan KD_PROPINSI s/d KD_JNS_OP).'
                ];
                continue;
            }

            $kdPropinsi   = substr($nop, 0, 2);
            $kdDati2      = substr($nop, 2, 2);
            $kdKecamatan  = substr($nop, 4, 3);
            $kdKelurahan  = substr($nop, 7, 3);
            $kdBlok       = substr($nop, 10, 3);
            $noUrut       = substr($nop, 13, 4);
            $kdJnsOp      = substr($nop, 17, 1);

            try {
                $countBayar = DB::connection('oracle')->table('SPPT')
                    ->where('kd_propinsi', $kdPropinsi)
                    ->where('kd_dati2', $kdDati2)
                    ->where('kd_kecamatan', $kdKecamatan)
                    ->where('kd_kelurahan', $kdKelurahan)
                    ->where('kd_blok', $kdBlok)
                    ->where('no_urut', $noUrut)
                    ->where('kd_jns_op', $kdJnsOp)
                    ->whereBetween('thn_pajak_sppt', [2014, $thnBetweenEnd])
                    ->where('status_pembayaran_sppt', 0)
                    ->count();

                if ($countBayar == 0) {
                    $spptToUpdate = Sppt::on('oracle')
                        ->where('kd_propinsi', $kdPropinsi)
                        ->where('kd_dati2', $kdDati2)
                        ->where('kd_kecamatan', $kdKecamatan)
                        ->where('kd_kelurahan', $kdKelurahan)
                        ->where('kd_blok', $kdBlok)
                        ->where('no_urut', $noUrut)
                        ->where('kd_jns_op', $kdJnsOp)
                        ->where('thn_pajak_sppt', $thnUpdateOracle)
                        ->where('status_pembayaran_sppt', 0)
                        ->first();

                    if ($spptToUpdate) {
                        $pbbTerhutang = (float) ($spptToUpdate->pbb_terhutang_sppt ?? 0.0);

                        $objekPajak = DatObjekPajak::on('oracle')
                            ->where('kd_propinsi', $kdPropinsi)
                            ->where('kd_dati2', $kdDati2)
                            ->where('kd_kecamatan', $kdKecamatan)
                            ->where('kd_kelurahan', $kdKelurahan)
                            ->where('kd_blok', $kdBlok)
                            ->where('no_urut', $noUrut)
                            ->where('kd_jns_op', $kdJnsOp)
                            ->first();

                        $nmKelurahanOp = null;
                        if ($objekPajak) {
                            $refKelurahan = RefKelurahan::on('oracle')
                                ->where('kd_propinsi', $objekPajak->kd_propinsi)
                                ->where('kd_dati2', $objekPajak->kd_dati2)
                                ->where('kd_kecamatan', $objekPajak->kd_kecamatan)
                                ->where('kd_kelurahan', $objekPajak->kd_kelurahan)
                                ->first();
                            if ($refKelurahan) {
                                $nmKelurahanOp = $refKelurahan->nm_kelurahan;
                            }
                        }

                        $nmKecamatanOp = null;
                        if ($objekPajak) {
                            $refKecamatan = RefKecamatan::on('oracle')
                                ->where('kd_propinsi', $objekPajak->kd_propinsi)
                                ->where('kd_dati2', $objekPajak->kd_dati2)
                                ->where('kd_kecamatan', $objekPajak->kd_kecamatan)
                                ->first();
                            if ($refKecamatan) {
                                $nmKecamatanOp = $refKecamatan->nm_kecamatan;
                            }
                        }
                        
                        $alamatWp = ($spptToUpdate->jln_wp_sppt ?? '-') .
                                    ' RT. ' . ($spptToUpdate->rt_wp_sppt ?? '-') .
                                    ' RW. ' . ($spptToUpdate->rw_wp_sppt ?? '-') .
                                    ' Kel/Desa. ' . ($spptToUpdate->kelurahan_wp_sppt ?? '-') .
                                    ' Kab/Kota. ' . ($spptToUpdate->kota_wp_sppt ?? '-');

                        $letakOp = ($objekPajak->jalan_op ?? '-') .
                                ' RT. ' . ($objekPajak->rt_op ?? '-') .
                                ' RW. ' . ($objekPajak->rw_op ?? '-') .
                                ' Kel/Desa. ' . ($nmKelurahanOp ?? '-') .
                                ' Kec. ' . ($nmKecamatanOp ?? '-') .
                                ' Kab/Kota. Nganjuk';

                        $faktorPengurang = $pbbTerhutang * ($persentasePengurangan / 100);
                        $pbbYgHarusDibayar_sebelum_min_check = $pbbTerhutang - $faktorPengurang;
                        $pbbYgHarusDibayar = ceil($pbbYgHarusDibayar_sebelum_min_check);

                        if ($pbbYgHarusDibayar <= 20000) {
                            $pbbYgHarusDibayar = 20000;
                        }

                        $dataToProcess[] = [
                            'kd_propinsi' => $kdPropinsi, 'kd_dati2' => $kdDati2,
                            'kd_kecamatan' => $kdKecamatan, 'kd_kelurahan' => $kdKelurahan,
                            'kd_blok' => $kdBlok, 'no_urut' => $noUrut, 'kd_jns_op' => $kdJnsOp,
                            'nop' => $nop,
                            'formatted_nop' => $this->formatNop($nop),
                            'thn_pajak_sppt' => $thnUpdateOracle,
                            'nm_wp_sppt' => $spptToUpdate->nm_wp_sppt ?? '-',
                            'alamat_wp' => $alamatWp,
                            'letak_op' => $letakOp,
                            'luas_bumi_sppt' => (float)($spptToUpdate->luas_bumi_sppt ?? 0),
                            'luas_bng_sppt' => (float)($spptToUpdate->luas_bng_sppt ?? 0),
                            'pbb_terhutang_sppt_lama' => $pbbTerhutang,
                            'jumlah_pengurangan_baru' => $faktorPengurang,
                            'ketetapan_baru' => $pbbYgHarusDibayar,

                            'persentase' => $persentasePengurangan,
                            'jenis_pengurangan' => $jenisPengurangan,
                            'no_sk_pengurangan' => $noSkPengurangan,
                            'tgl_sk_pengurangan' => $tglSkPengurangan,
                            'status_validasi' => 'Siap Diproses',
                            'message' => 'Data ditemukan dan siap untuk pengurangan.'
                        ];
                    } else {
                        $dataToProcess[] = [
                            'nop' => $nop,
                            'status_validasi' => 'Gagal',
                            'message' => 'Data SPPT target update tidak ditemukan di Oracle untuk tahun ' . $thnUpdateOracle . ' atau sudah terbayar.'
                        ];
                    }
                } else {
                    $dataToProcess[] = [
                        'nop' => $nop,
                        'status_validasi' => 'Tidak Diproses',
                        'message' => 'SPPT tidak memenuhi kriteria (ditemukan ' . $countBayar . ' record dengan status pembayaran 0 antara 2014 dan ' . $thnBetweenEnd . ').'
                    ];
                }
            } catch (\Exception $e) {
                $dataToProcess[] = [
                    'nop' => $nop,
                    'status_validasi' => 'Error',
                    'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage() . ' (Line: ' . $e->getLine() . ')'
                ];
            }
        }

        $request->session()->put('pengurangan_data_preview', $dataToProcess);
        $request->session()->put('persentase_pengurangan_preview', $persentasePengurangan);
        $request->session()->put('no_sk_pengurangan_preview', $noSkPengurangan);
        $request->session()->put('tgl_sk_pengurangan_preview', $tglSkPengurangan);
        $request->session()->put('thn_update_oracle_preview', $thnUpdateOracle);
        $request->session()->put('jenis_pengurangan_preview', $jenisPengurangan);
        
        // PERBAIKAN: Simpan path berkas sementara di session untuk digunakan di confirmStore
        if ($request->hasFile('berkas')) {
            $file = $request->file('berkas');
            $originalFileName = $file->getClientOriginalName();
            $fileExtension = $file->getClientOriginalExtension();
            $fileName = Str::random(40) . '.' . $fileExtension;
            
            // Simpan file ke direktori 'temp_berkas_uploads' pada disk 'local' (storage/app/temp_berkas_uploads)
            $tempPath = $file->storeAs('temp_berkas_uploads', $fileName, 'local'); 
            
            $request->session()->put('berkas_temp_path', $tempPath);
            $request->session()->put('berkas_original_name', $originalFileName);
        } else {
            $request->session()->forget('berkas_temp_path');
            $request->session()->forget('berkas_original_name');
        }

        return view('pengurangan.preview', compact('dataToProcess', 'persentasePengurangan', 'noSkPengurangan', 'tglSkPengurangan', 'thnUpdateOracle', 'jenisPengurangan'));
    }

    public function confirmStore(Request $request)
    {
        $dataToProcess = $request->session()->get('pengurangan_data_preview');
        $persentasePengurangan = $request->session()->get('persentase_pengurangan_preview');
        $noSkPengurangan = $request->session()->get('no_sk_pengurangan_preview');
        $tglSkPengurangan = $request->session()->get('tgl_sk_pengurangan_preview');
        $thnUpdateOracle = $request->session()->get('thn_update_oracle_preview');
        $jenisPengurangan = $request->session()->get('jenis_pengurangan_preview');
        $finalBerkasPath = Storage::disk('public')->putFileAs('berkas_pengurangan', new \Illuminate\Http\File(Storage::disk('local')->path($berkasTempPath)), $newFileName);
// Pastikan $finalBerkasPath ini yang disimpan ke DB
        
        $berkasTempPath = $request->session()->get('berkas_temp_path');
        $berkasOriginalName = $request->session()->get('berkas_original_name');

        if (empty($dataToProcess)) {
            return redirect()->route('pengurangan.create')->withErrors('Tidak ada data untuk diproses. Silakan ulangi dari awal.');
        }

        $results = [];
        $finalBerkasPath = null;

        // PERBAIKAN PENTING: Memindahkan file berkas dari temp ke lokasi permanen HANYA SEKALI
        if ($berkasTempPath && Storage::disk('local')->exists($berkasTempPath)) {
            $newFileName = Str::random(40) . '.' . pathinfo($berkasOriginalName, PATHINFO_EXTENSION);
            
            // PERBAIKAN: Gunakan putFileAs() di disk 'public' secara langsung, ini akan mengembalikan path
            // Pastikan disk 'public' yang digunakan untuk penyimpanan final
            $finalBerkasPath = Storage::disk('public')->putFileAs('berkas_pengurangan', Storage::disk('local')->path($berkasTempPath), $newFileName); 
            // putFileAs dengan argumen ke-2 path absolut dari file_get_contents.
            // Atau cara yang lebih sederhana:
            // $finalBerkasPath = Storage::disk('public')->put('berkas_pengurangan/' . $newFileName, Storage::disk('local')->get($berkasTempPath));
            // Hapus file sementara dari disk 'local'
            Storage::disk('local')->delete($berkasTempPath);
        }


        foreach ($dataToProcess as $data) {
            if (isset($data['status_validasi']) && $data['status_validasi'] === 'Siap Diproses') {
                try {
                    $updated = DB::connection('oracle')->table('SPPT')
                        ->where('kd_propinsi', $data['kd_propinsi'])
                        ->where('kd_dati2', $data['kd_dati2'])
                        ->where('kd_kecamatan', $data['kd_kecamatan'])
                        ->where('kd_kelurahan', $data['kd_kelurahan'])
                        ->where('kd_blok', $data['kd_blok'])
                        ->where('no_urut', $data['no_urut'])
                        ->where('kd_jns_op', $data['kd_jns_op'])
                        ->where('thn_pajak_sppt', $thnUpdateOracle)
                        ->where('status_pembayaran_sppt', 0)
                        ->update([
                            'faktor_pengurang_sppt' => $data['jumlah_pengurangan_baru'],
                            'pbb_yg_harus_dibayar_sppt' => $data['ketetapan_baru'],
                        ]);

                    if ($updated) {
                        Pengurangan::create([
                            'kd_propinsi' => $data['kd_propinsi'],
                            'kd_dati2' => $data['kd_dati2'],
                            'kd_kecamatan' => $data['kd_kecamatan'],
                            'kd_kelurahan' => $data['kd_kelurahan'],
                            'kd_blok' => $data['kd_blok'],
                            'no_urut' => $data['no_urut'],
                            'kd_jns_op' => $data['kd_jns_op'],
                            'thn_pajak_sppt' => $thnUpdateOracle,
                            'faktor_pengurang_sppt' => $data['jumlah_pengurangan_baru'],
                            'pbb_yg_harus_dibayar_sppt' => $data['ketetapan_baru'],
                            'persentase' => $persentasePengurangan,
                            'jenis_pengurangan' => $jenisPengurangan,
                            'no_sk_pengurangan' => $noSkPengurangan,
                            'tgl_sk_pengurangan' => $tglSkPengurangan,
                            'nm_wp_sppt' => $data['nm_wp_sppt'] ?? null,
                            'alamat_wp' => $data['alamat_wp'] ?? null,
                            'letak_op' => $data['letak_op'] ?? null,
                            'luas_bumi_sppt' => $data['luas_bumi_sppt'] ?? null,
                            'luas_bng_sppt' => $data['luas_bng_sppt'] ?? null,
                            'pbb_terhutang_sppt_lama' => $data['pbb_terhutang_sppt_lama'] ?? null,
                            'jumlah_pengurangan_baru' => $data['jumlah_pengurangan_baru'] ?? null,
                            'ketetapan_baru' => $data['ketetapan_baru'] ?? null,
                            'operator' => Auth::user()->name ?? 'System',
                            'berkas_path' => $finalBerkasPath,
                        ]);
                        $results[] = [
                            'nop' => $data['nop'] ?? ($data['kd_propinsi'] . $data['kd_dati2'] . $data['kd_kecamatan'] . $data['kd_kelurahan'] . $data['kd_blok'] . $data['no_urut'] . $data['kd_jns_op']),
                            'status' => 'Berhasil',
                            'message' => 'SPPT berhasil diupdate di Oracle dan log disimpan di MySQL.'
                        ];
                    } else {
                        $results[] = [
                            'nop' => $data['nop'] ?? 'N/A',
                            'status' => 'Gagal',
                            'message' => 'Gagal update SPPT di Oracle (record tidak ditemukan saat konfirmasi atau status pembayaran berubah).'
                        ];
                    }
                } catch (\Exception $e) {
                    $results[] = [
                        'nop' => 'N/A',
                        'status' => 'Error',
                        'message' => 'Terjadi kesalahan sistem saat konfirmasi: ' . $e->getMessage() . ' (Line: ' . $e->getLine() . ')'
                    ];
                }
            } else {
                $results[] = [
                    'nop' => $data['nop'] ?? 'N/A',
                    'status' => 'Dilewati',
                    'message' => 'Data tidak diproses karena tidak valid pada tahap pratinjau.'
                ];
            }
        }

        $request->session()->forget('berkas_temp_path');
        $request->session()->forget('berkas_original_name');

        $request->session()->forget('pengurangan_data_preview');
        $request->session()->forget('persentase_pengurangan_preview');
        $request->session()->forget('no_sk_pengurangan_preview');
        $request->session()->forget('tgl_sk_pengurangan_preview');
        $request->session()->forget('thn_update_oracle_preview');
        $request->session()->forget('jenis_pengurangan_preview');

        $request->session()->flash('pengurangan_results', $results);
        return redirect()->route('laporan.pengurangan')->with('success', 'Proses konfirmasi pengurangan selesai. Detail di bawah.');
    }

     public function update(Request $request, int $id)
    {
        // PERBAIKAN: Tambahkan aturan required untuk update juga jika perlu
        $request->validate([
            'nomor_sk' => 'required|string|max:255', // PERBAIKAN: Tambah required
            'tahun_sk' => 'required|integer|min:2000|max:2100', // PERBAIKAN: Tambah required
            'tgl_sk_pengurangan' => 'required|date', // PERBAIKAN: Tambah required
            'persentase' => 'required|numeric|min:0|max:100', // Tetap required
            'jenis_pengurangan_dropdown' => 'required|string', // Tetap required
            'berkas' => 'nullable|file|mimes:pdf|max:24576', // Berkas di edit bisa nullable
        ]);

       $pengurangan = Pengurangan::findOrFail($id);

        $inputNomorSk = $request->input('nomor_sk');
        $inputTahunSk = $request->input('tahun_sk');
        $tglSkPengurangan = $request->input('tgl_sk_pengurangan');

        $jenisPengurangan = $request->input('jenis_pengurangan_dropdown');
        $persentaseBaru = (float)($request->input('persentase'));

        $noSkPengurangan = null;
        if (!empty($inputNomorSk) && !empty($inputTahunSk)) {
            $noSkPengurangan = '100.3.3.2/' . $inputNomorSk . '/K/411.403/' . $inputTahunSk;
        }

        $pbbTerhutangLama = (float)($pengurangan->pbb_terhutang_sppt_lama ?? 0.0);

        $faktorPengurangBaru = $pbbTerhutangLama * ($persentaseBaru / 100);
        $pbbYgHarusDibayar_sebelum_min_check = $pbbTerhutangLama - $faktorPengurangBaru;
        $ketetapanBaru = ceil($pbbYgHarusDibayar_sebelum_min_check);

        if ($ketetapanBaru <= 20000) {
            $ketetapanBaru = 20000;
        }

        $berkasPath = $pengurangan->berkas_path;
        if ($request->hasFile('berkas')) {
            if ($pengurangan->berkas_path && Storage::exists($pengurangan->berkas_path)) {
                Storage::delete($pengurangan->berkas_path);
            }
            $file = $request->file('berkas');
            $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $berkasPath = Storage::disk('public')->putFileAs('berkas_pengurangan', $file, $fileName);
        } else if ($request->boolean('remove_berkas')) {
            if ($pengurangan->berkas_path && Storage::exists($pengurangan->berkas_path)) {
                Storage::delete($pengurangan->berkas_path);
            }
            $berkasPath = null;
        }

        $pengurangan->update([
            'no_sk_pengurangan' => $noSkPengurangan,
            'tgl_sk_pengurangan' => $tglSkPengurangan,
            'persentase' => $persentaseBaru,
            'jenis_pengurangan' => $jenisPengurangan,
            'faktor_pengurang_sppt' => $faktorPengurangBaru,
            'pbb_yg_harus_dibayar_sppt' => $ketetapanBaru,
            'jumlah_pengurangan_baru' => $faktorPengurangBaru,
            'ketetapan_baru' => $ketetapanBaru,
            'operator' => Auth::user()->name ?? 'System',
            'berkas_path' => $berkasPath,
        ]);

        return redirect()->route('laporan.pengurangan')->with('success', 'Data pengurangan berhasil diperbarui.');
    }


    public function destroy(int $id)
    {
        $pengurangan = Pengurangan::findOrFail($id);

        $nop = $pengurangan->kd_propinsi . $pengurangan->kd_dati2 . $pengurangan->kd_kecamatan . $pengurangan->kd_kelurahan . $pengurangan->kd_blok . $pengurangan->no_urut . $pengurangan->kd_jns_op;
        $thnUpdateOracle = $pengurangan->thn_pajak_sppt;

        try {
            if ($pengurangan->berkas_path && Storage::exists($pengurangan->berkas_path)) {
                Storage::delete($pengurangan->berkas_path);
            }

            $updatedOracle = DB::connection('oracle')->table('SPPT')
                ->where('kd_propinsi', $pengurangan->kd_propinsi)
                ->where('kd_dati2', $pengurangan->kd_dati2)
                ->where('kd_kecamatan', $pengurangan->kd_kecamatan)
                ->where('kd_kelurahan', $pengurangan->kd_kelurahan)
                ->where('kd_blok', $pengurangan->kd_blok)
                ->where('no_urut', $pengurangan->no_urut)
                ->where('kd_jns_op', $pengurangan->kd_jns_op)
                ->where('thn_pajak_sppt', $thnUpdateOracle)
                ->where('status_pembayaran_sppt', 0)
                ->update([
                    'faktor_pengurang_sppt' => 0,
                    'pbb_yg_harus_dibayar_sppt' => DB::raw('pbb_terhutang_sppt'),
                ]);

            if ($updatedOracle > 0) {
                $pengurangan->delete();
                return redirect()->route('laporan.pengurangan')->with('success', 'Record pengurangan berhasil dihapus dan data Oracle berhasil dikembalikan.');
            } else {
                return redirect()->route('laporan.pengurangan')->with('error', 'Gagal mengembalikan data Oracle. Mungkin status pembayaran sudah berubah atau data tidak ditemukan.');
            }

        } catch (\Exception $e) {
            return redirect()->route('laporan.pengurangan')->with('error', 'Terjadi kesalahan saat menghapus record: ' . $e->getMessage());
        }
    }

    protected function formatNop(string $nopRaw): string
    {
        if (strlen($nopRaw) == 18) {
            return substr($nopRaw, 0, 2) . '.' .
                   substr($nopRaw, 2, 2) . '.' .
                   substr($nopRaw, 4, 3) . '.' .
                   substr($nopRaw, 7, 3) . '.' .
                   substr($nopRaw, 10, 3) . '.' .
                   substr($nopRaw, 13, 4) . '.' .
                   substr($nopRaw, 17, 1);
        }
        return $nopRaw;
    }
    
    public function indexLaporan(Request $request)
    {
        $query = Pengurangan::orderBy('created_at', 'desc');

        if ($request->has('search') && $request->input('search') != '') {
            $search = strtolower($request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->orWhereRaw("LOWER(CONCAT(kd_propinsi, kd_dati2, kd_kecamatan, kd_kelurahan, kd_blok, no_urut, kd_jns_op)) LIKE ?", ['%' . $search . '%']);
                $q->orWhereRaw("LOWER(nm_wp_sppt) LIKE ?", ['%' . $search . '%']);
                $q->orWhereRaw("CAST(thn_pajak_sppt AS CHAR) LIKE ?", ['%' . $search . '%']);
                $q->orWhereRaw("LOWER(alamat_wp) LIKE ?", ['%' . $search . '%']);
                $q->orWhereRaw("LOWER(no_sk_pengurangan) LIKE ?", ['%' . $search . '%']);
                $q->orWhereRaw("LOWER(jenis_pengurangan) LIKE ?", ['%' . $search . '%']);
            });
        }

        $laporanPengurangan = $query->paginate(25);

        $laporanPengurangan->getCollection()->map(function ($item) {
            $rawNop = $item->kd_propinsi . $item->kd_dati2 . $item->kd_kecamatan . $item->kd_kelurahan . $item->kd_blok . $item->no_urut . $item->kd_jns_op;
            $item->formatted_nop = $this->formatNop($rawNop);
            return $item;
        });

        // PERBAIKAN: Jika request adalah AJAX, kembalikan hanya partial view
        if ($request->ajax()) {
            return view('pengurangan.partials.laporan_table', compact('laporanPengurangan'))->render();
        }

        // Untuk request non-AJAX (pertama kali buka halaman), kembalikan full view
        $flashResults = session('pengurangan_results', []); // Flash results hanya untuk full view
        foreach ($flashResults as $key => $result) { // Masih diperlukan untuk tampilan flash awal
            $rawNop = $result['nop'] ?? $result['kode_sppt'] ?? '';
            $flashResults[$key]['formatted_nop'] = $this->formatNop($rawNop);
        }

        return view('pengurangan.laporan', compact('laporanPengurangan', 'flashResults'));
    }

    public function cetakPdf() // Nama method ini sesuai dengan route laporan.pengurangan.cetak-pdf
    {
        $kecamatans = RefKecamatan::on('oracle')
            ->where('kd_propinsi', '35') // Nganjuk
            ->where('kd_dati2', '18')    // Nganjuk
            ->orderBy('nm_kecamatan')
            ->get();
        return view('pengurangan.filter_cetak_pdf', compact('kecamatans'));
    }

    /**
     * PERBAIKAN PENTING: Method untuk mencetak laporan ke PDF atau Excel secara manual.
     * Ini akan dipanggil oleh form filter.
     */
    public function cetakFilteredPdf(Request $request)
    {
        $request->validate([
            'tahun_pajak' => 'nullable|integer|min:2000|max:2100',
            'kd_kecamatan' => 'nullable|string',
            'no_sk' => 'nullable|string',
            'format' => 'required|in:pdf,excel',
        ]);

        $query = Pengurangan::orderBy('created_at', 'asc');

        if ($request->filled('tahun_pajak')) {
            $query->where('thn_pajak_sppt', $request->input('tahun_pajak'));
        }
        if ($request->filled('kd_kecamatan')) {
            $searchKdKecamatan = $request->input('kd_kecamatan');
            $query->where(function($q) use ($searchKdKecamatan) {
                $q->where('kd_propinsi', '35')
                  ->where('kd_dati2', '18')
                  ->where('kd_kecamatan', $searchKdKecamatan);
            });
        }
        if ($request->filled('no_sk')) {
            $query->where('no_sk_pengurangan', 'LIKE', '%' . $request->input('no_sk') . '%');
        }

        $dataLaporan = $query->get(); // Ambil semua data yang sudah difilter

        // Format NOP untuk laporan (baik PDF maupun Excel)
        $dataLaporan->map(function ($item) {
            $rawNop = $item->kd_propinsi . $item->kd_dati2 . $item->kd_kecamatan . $item->kd_kelurahan . $item->kd_blok . $item->no_urut . $item->kd_jns_op;
            $item->formatted_nop = $this->formatNop($rawNop);
            return $item;
        });

        // Tentukan nama file
        $fileNameBase = 'laporan_pengurangan_sppt_';
        if ($request->filled('tahun_pajak')) $fileNameBase .= '_thn' . $request->input('tahun_pajak');
        if ($request->filled('kd_kecamatan')) $fileNameBase .= '_kec' . $request->input('kd_kecamatan');
        if ($request->filled('no_sk')) $fileNameBase .= '_nosk' . $request->input('no_sk');
        $fileNameBase .= '_' . Carbon::now()->format('Ymd_His');

        $format = $request->input('format');

        if ($format === 'pdf') {
            $pdf = PDF::loadView('pengurangan.laporan_pdf', compact('dataLaporan'))
                      ->setPaper(array(0, 0, 935.433, 609.448)); // F4 Landscape
            return $pdf->download($fileNameBase . '.pdf');

        } elseif ($format === 'excel') {
            // PERBAIKAN PENTING: Buat file Excel secara manual menggunakan PhpSpreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set header kolom
            $headers = [
                'NOP', 'Tahun Pajak', 'Nama WP', 'Alamat WP', 'Letak OP',
                'Bumi', 'Bangunan', 'Baku', 'Pengurangan (%)', 'Jumlah Pengurangan',
                'Ketetapan Yang Harus Dibayar', 'Jenis Pengurangan', 'No SK', 'Tgl SK',
                'Tgl Proses', 'Operator'
            ];
            $sheet->fromArray([$headers], null, 'A1'); // Tulis header di A1

            // Tulis data baris
            $rowNum = 2;
            foreach ($dataLaporan as $data) {
                $rowData = [
                    $data->formatted_nop,
                    $data->thn_pajak_sppt,
                    $data->nm_wp_sppt ?? '-',
                    $data->alamat_wp ?? '-',
                    $data->letak_op ?? '-',
                    (float)($data->luas_bumi_sppt ?? 0),
                    (float)($data->luas_bng_sppt ?? 0),
                    (float)($data->pbb_terhutang_sppt_lama ?? 0.0),
                    (float)($data->persentase ?? 0.0),
                    (float)($data->jumlah_pengurangan_baru ?? 0.0),
                    (float)($data->ketetapan_baru ?? 0.0),
                    $data->jenis_pengurangan ?? '-',
                    $data->no_sk_pengurangan ?? '-',
                    ($data->tgl_sk_pengurangan ? Carbon::parse($data->tgl_sk_pengurangan)->format('d-m-Y') : '-'),
                    $data->created_at->format('d-m-Y H:i:s'),
                    $data->operator ?? '-',
                ];
                $sheet->fromArray([$rowData], null, 'A' . $rowNum);
                $rowNum++;
            }

            // Pengaturan untuk download file Excel
            $writer = new Xlsx($spreadsheet);
            $response = response()->stream(
                function () use ($writer) {
                    $writer->save('php://output');
                },
                200,
                [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Content-Disposition' => 'attachment; filename="' . $fileNameBase . '.xlsx"',
                ]
            );
            return $response;
        }

        return redirect()->back()->with('error', 'Format export tidak valid.');
    }


    public function cetakSinglePdf(int $id)
    {
        $dataPengurangan = Pengurangan::findOrFail($id);
        $dataLaporan = collect([$dataPengurangan]); 

        $dataLaporan->map(function ($item) {
            $rawNop = $item->kd_propinsi . $item->kd_dati2 . $item->kd_kecamatan . $item->kd_kelurahan . $item->kd_blok . $item->no_urut . $item->kd_jns_op;
            $item->formatted_nop = $this->formatNop($rawNop);
            return $item;
        });

        $f4LandscapeCustomPaper = array(0, 0, 935.433, 609.448);

        $pdf = PDF::loadView('pengurangan.laporan_pdf', compact('dataLaporan'))
                  ->setPaper($f4LandscapeCustomPaper);

        return $pdf->download('pengurangan_sppt_' . $dataPengurangan->formatted_nop . '_' . Carbon::now()->format('Ymd_His') . '.pdf');
    }

    public function edit(int $id)
    {
        $pengurangan = Pengurangan::findOrFail($id);

        $rawNop = $pengurangan->kd_propinsi . $pengurangan->kd_dati2 . $pengurangan->kd_kecamatan . $pengurangan->kd_kelurahan . $pengurangan->kd_blok . $pengurangan->no_urut . $pengurangan->kd_jns_op;
        $pengurangan->formatted_nop = $this->formatNop($rawNop);

        return view('pengurangan.edit', compact('pengurangan'));
    }
}