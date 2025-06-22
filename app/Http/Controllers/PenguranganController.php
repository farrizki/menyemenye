<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sppt;
use App\Models\Pengurangan;
use App\Models\DatObjekPajak;
use App\Models\RefKelurahan;
use App\Models\RefKecamatan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once base_path('vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Spreadsheet.php');
require_once base_path('vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Writer/Xlsx.php');

class PenguranganController extends Controller
{
    public function create()
    {
        return view('pengurangan.create');
    }

    public function preview(Request $request)
    {
        // Validasi, berkas tidak wajib saat edit (nullable)
        $request->validate([
            'nop' => 'required|string',
            'thn_pajak_sppt_input' => 'required|integer|min:2000|max:2100',
            'jenis_pengurangan_dropdown' => 'required|string',
            'persentase' => 'required|numeric|min:0|max:100',
            'nomor_sk' => 'required|string|max:255',
            'tahun_sk' => 'required|integer|min:2000|max:2100',
            'tgl_sk_pengurangan' => 'required|date',
            'berkas' => 'nullable|file|mimes:pdf|max:24576',
            'pengurangan_id' => 'sometimes|integer|exists:pengurangan,id'
        ]);
        
        // Simpan ID jika ini adalah proses edit
        if ($request->has('pengurangan_id')) {
            $request->session()->put('pengurangan_id_to_update', $request->input('pengurangan_id'));
        } else {
            $request->session()->forget('pengurangan_id_to_update');
        }

        $nopArray = array_map('trim', explode(',', $request->input('nop')));
        $thnUpdateOracle = (int) $request->input('thn_pajak_sppt_input');
        $jenisPengurangan = $request->input('jenis_pengurangan_dropdown');
        $persentasePengurangan = (float) $request->input('persentase');
        $inputNomorSk = $request->input('nomor_sk');
        $inputTahunSk = $request->input('tahun_sk');
        $tglSkPengurangan = $request->input('tgl_sk_pengurangan');
        $noSkPengurangan = '100.3.3.2/' . $inputNomorSk . '/K/411.403/' . $inputTahunSk;

        if ($request->hasFile('berkas')) {
            $file = $request->file('berkas');
            $tempPath = $file->storeAs('temp_berkas_uploads', Str::random(40) . '.' . $file->getClientOriginalExtension(), 'local');
            $request->session()->put('berkas_temp_path', $tempPath);
        } else {
            $request->session()->forget('berkas_temp_path');
        }

        $dataToProcess = [];
        $thnBetweenEnd = $thnUpdateOracle - 1;

        foreach ($nopArray as $nop) {
            if (strlen($nop) != 18) {
                $dataToProcess[] = [ 'nop' => $nop, 'status_validasi' => 'Gagal', 'message' => 'Format NOP tidak valid.' ];
                continue;
            }

            $kdPropinsi   = substr($nop, 0, 2); $kdDati2      = substr($nop, 2, 2);
            $kdKecamatan  = substr($nop, 4, 3); $kdKelurahan  = substr($nop, 7, 3);
            $kdBlok       = substr($nop, 10, 3); $noUrut       = substr($nop, 13, 4);
            $kdJnsOp      = substr($nop, 17, 1);

            try {
                $countBayar = DB::connection('oracle')->table('SPPT')
                    ->where('kd_propinsi', $kdPropinsi)->where('kd_dati2', $kdDati2)
                    ->where('kd_kecamatan', $kdKecamatan)->where('kd_kelurahan', $kdKelurahan)
                    ->where('kd_blok', $kdBlok)->where('no_urut', $noUrut)->where('kd_jns_op', $kdJnsOp)
                    ->whereBetween('thn_pajak_sppt', [2014, $thnBetweenEnd])
                    ->where('status_pembayaran_sppt', 0)->count();

                if ($countBayar > 0) {
                    $dataToProcess[] = [ 'nop' => $nop, 'formatted_nop' => $this->formatNop($nop), 'status_validasi' => 'Tidak Diproses', 'message' => 'Ditemukan tunggakan tahun sebelumnya.' ];
                    continue;
                }

                $spptToUpdate = Sppt::on('oracle')->where('kd_propinsi', $kdPropinsi)
                    ->where('kd_dati2', $kdDati2)->where('kd_kecamatan', $kdKecamatan)->where('kd_kelurahan', $kdKelurahan)
                    ->where('kd_blok', $kdBlok)->where('no_urut', $noUrut)->where('kd_jns_op', $kdJnsOp)
                    ->where('thn_pajak_sppt', $thnUpdateOracle)->where('status_pembayaran_sppt', 0)->first();

                if ($spptToUpdate) {
                    $pbbTerhutang = (float)($spptToUpdate->pbb_terhutang_sppt ?? 0.0);
                    $objekPajak = DatObjekPajak::on('oracle')
                        ->where('kd_propinsi', $kdPropinsi)->where('kd_dati2', $kdDati2)
                        ->where('kd_kecamatan', $kdKecamatan)->where('kd_kelurahan', $kdKelurahan)
                        ->where('kd_blok', $kdBlok)->where('no_urut', $noUrut)->where('kd_jns_op', $kdJnsOp)->first();
                    $nmKelurahanOp = RefKelurahan::on('oracle')->where('kd_kecamatan', $kdKecamatan)->where('kd_kelurahan', $kdKelurahan)->value('nm_kelurahan');
                    $nmKecamatanOp = RefKecamatan::on('oracle')->where('kd_kecamatan', $kdKecamatan)->value('nm_kecamatan');
                    
                    $alamatWp = trim(($spptToUpdate->jln_wp_sppt ?? '') . ' RT. ' . ($spptToUpdate->rt_wp_sppt ?? '') . ' RW. ' . ($spptToUpdate->rw_wp_sppt ?? '') . ' Kel/Desa. ' . ($spptToUpdate->kelurahan_wp_sppt ?? '') . ' Kab/Kota. ' . ($spptToUpdate->kota_wp_sppt ?? '-'));
                    $letakOp = trim(($objekPajak->jalan_op ?? '') . ' RT. ' . ($objekPajak->rt_op ?? '') . ' RW. ' . ($objekPajak->rw_op ?? '') . ' Kel/Desa. ' . ($nmKelurahanOp ?? '') . ' Kec. ' . ($nmKecamatanOp ?? '') . ' Kab/Kota. Nganjuk');

                    $faktorPengurang = $pbbTerhutang * ($persentasePengurangan / 100);
                    $pbbYgHarusDibayar = ceil($pbbTerhutang - $faktorPengurang);
                    if ($pbbYgHarusDibayar <= 20000) $pbbYgHarusDibayar = 20000;

                    $dataToProcess[] = [
                        'kd_propinsi' => $kdPropinsi, 'kd_dati2' => $kdDati2,
                        'kd_kecamatan' => $kdKecamatan, 'kd_kelurahan' => $kdKelurahan,
                        'kd_blok' => $kdBlok, 'no_urut' => $noUrut, 'kd_jns_op' => $kdJnsOp,
                        'nop' => $nop, 'formatted_nop' => $this->formatNop($nop),
                        'thn_pajak_sppt' => $thnUpdateOracle, 'nm_wp_sppt' => $spptToUpdate->nm_wp_sppt ?? '-',
                        'alamat_wp' => $alamatWp, 'letak_op' => $letakOp,
                        'luas_bumi_sppt' => (float)($spptToUpdate->luas_bumi_sppt ?? 0),
                        'luas_bng_sppt' => (float)($spptToUpdate->luas_bng_sppt ?? 0),
                        'pbb_terhutang_sppt_lama' => $pbbTerhutang,
                        'jumlah_pengurangan_baru' => $faktorPengurang, 'ketetapan_baru' => $pbbYgHarusDibayar,
                        'persentase' => $persentasePengurangan, 'jenis_pengurangan' => $jenisPengurangan,
                        'no_sk_pengurangan' => $noSkPengurangan, 'tgl_sk_pengurangan' => $tglSkPengurangan,
                        'status_validasi' => 'Siap Diproses',
                        'message' => 'Data ditemukan dan siap untuk pengurangan.'
                    ];
                } else {
                    $dataToProcess[] = [ 'nop' => $nop, 'formatted_nop' => $this->formatNop($nop), 'status_validasi' => 'Gagal', 'message' => 'SPPT tidak ditemukan atau sudah lunas.' ];
                }
            } catch (\Exception $e) {
                $dataToProcess[] = [ 'nop' => $nop, 'formatted_nop' => $this->formatNop($nop), 'status_validasi' => 'Error', 'message' => 'Kesalahan sistem: ' . $e->getMessage() ];
            }
        }

        $request->session()->put('pengurangan_data_preview', $dataToProcess);
        $request->session()->put('persentase_pengurangan_preview', $persentasePengurangan);
        $request->session()->put('no_sk_pengurangan_preview', $noSkPengurangan);
        $request->session()->put('tgl_sk_pengurangan_preview', $tglSkPengurangan);
        $request->session()->put('thn_update_oracle_preview', $thnUpdateOracle);
        $request->session()->put('jenis_pengurangan_preview', $jenisPengurangan);

        return view('pengurangan.preview', compact('dataToProcess', 'persentasePengurangan', 'noSkPengurangan', 'tglSkPengurangan', 'thnUpdateOracle', 'jenisPengurangan'));
    }

    public function confirmStore(Request $request)
    {
        $dataToProcess = $request->session()->get('pengurangan_data_preview');
        $penguranganId = $request->session()->get('pengurangan_id_to_update');
        $persentasePengurangan = $request->session()->get('persentase_pengurangan_preview');
        $noSkPengurangan = $request->session()->get('no_sk_pengurangan_preview');
        $tglSkPengurangan = $request->session()->get('tgl_sk_pengurangan_preview');
        $jenisPengurangan = $request->session()->get('jenis_pengurangan_preview');
        $thnUpdateOracle = $request->session()->get('thn_update_oracle_preview');
        
        if (empty($dataToProcess)) {
            return redirect()->back()->withErrors('Sesi habis, silakan ulangi proses.');
        }

        $berkasTempPath = $request->session()->get('berkas_temp_path');
        $finalBerkasPath = null;

        if ($berkasTempPath && Storage::disk('local')->exists($berkasTempPath)) {
            $newFileName = Str::random(40) . '.pdf';
            $finalBerkasPath = Storage::disk('public')->putFileAs('berkas_pengurangan', new \Illuminate\Http\File(storage_path('app/' . $berkasTempPath)), $newFileName);
            Storage::disk('local')->delete($berkasTempPath);
        }

        $results = [];
        foreach ($dataToProcess as $data) {
            if ($data['status_validasi'] === 'Siap Diproses') {
                try {
                    $updated = DB::connection('oracle')->table('SPPT')
                        ->where('kd_propinsi', $data['kd_propinsi'])->where('kd_dati2', $data['kd_dati2'])
                        ->where('kd_kecamatan', $data['kd_kecamatan'])->where('kd_kelurahan', $data['kd_kelurahan'])
                        ->where('kd_blok', $data['kd_blok'])->where('no_urut', $data['no_urut'])->where('kd_jns_op', $data['kd_jns_op'])
                        ->where('thn_pajak_sppt', $thnUpdateOracle)->where('status_pembayaran_sppt', 0)
                        ->update([
                            'faktor_pengurang_sppt' => $data['jumlah_pengurangan_baru'],
                            'pbb_yg_harus_dibayar_sppt' => $data['ketetapan_baru'],
                        ]);
                    
                    if ($updated) {
                        $logData = [
                            'kd_propinsi' => $data['kd_propinsi'], 'kd_dati2' => $data['kd_dati2'],
                            'kd_kecamatan' => $data['kd_kecamatan'], 'kd_kelurahan' => $data['kd_kelurahan'],
                            'kd_blok' => $data['kd_blok'], 'no_urut' => $data['no_urut'], 'kd_jns_op' => $data['kd_jns_op'],
                            'thn_pajak_sppt' => $thnUpdateOracle,
                            'faktor_pengurang_sppt' => $data['jumlah_pengurangan_baru'],
                            'pbb_yg_harus_dibayar_sppt' => $data['ketetapan_baru'],
                            'persentase' => $persentasePengurangan, 'jenis_pengurangan' => $jenisPengurangan,
                            'no_sk_pengurangan' => $noSkPengurangan, 'tgl_sk_pengurangan' => $tglSkPengurangan,
                            'nm_wp_sppt' => $data['nm_wp_sppt'], 'alamat_wp' => $data['alamat_wp'], 'letak_op' => $data['letak_op'],
                            'luas_bumi_sppt' => $data['luas_bumi_sppt'], 'luas_bng_sppt' => $data['luas_bng_sppt'],
                            'pbb_terhutang_sppt_lama' => $data['pbb_terhutang_sppt_lama'],
                            'jumlah_pengurangan_baru' => $data['jumlah_pengurangan_baru'], 'ketetapan_baru' => $data['ketetapan_baru'],
                            'operator' => Auth::user()->name ?? 'System',
                        ];

                        if ($penguranganId) {
                            $pengurangan = Pengurangan::find($penguranganId);
                            if ($pengurangan) {
                                if ($finalBerkasPath) {
                                    if ($pengurangan->berkas_path) {
                                        Storage::disk('public')->delete($pengurangan->berkas_path);
                                    }
                                    $logData['berkas_path'] = $finalBerkasPath;
                                }
                                $pengurangan->update($logData);
                            }
                        } else {
                            $logData['berkas_path'] = $finalBerkasPath;
                            Pengurangan::create($logData);
                        }
                        $results[] = ['nop' => $data['nop'], 'status' => 'Berhasil', 'message' => 'Data berhasil diproses.'];
                    } else {
                        $results[] = ['nop' => $data['nop'], 'status' => 'Gagal', 'message' => 'Gagal update Oracle (data mungkin sudah lunas).'];
                    }
                } catch (\Exception $e) {
                     $results[] = ['nop' => $data['nop'], 'status' => 'Error', 'message' => 'Kesalahan sistem: ' . $e->getMessage()];
                }
            } else {
                $results[] = ['nop' => $data['nop'], 'status' => $data['status_validasi'], 'message' => $data['message']];
            }
        }

        $request->session()->forget(['pengurangan_data_preview', 'pengurangan_id_to_update', 'berkas_temp_path', 'persentase_pengurangan_preview', 'no_sk_pengurangan_preview', 'tgl_sk_pengurangan_preview', 'thn_update_oracle_preview', 'jenis_pengurangan_preview']);
        $request->session()->flash('pengurangan_results', $results);
        return redirect()->route('laporan.pengurangan')->with('success', 'Proses konfirmasi pengurangan selesai.');
    }
    
    public function indexLaporan(Request $request)
    {
        $query = Pengurangan::orderBy('created_at', 'desc');
        if ($request->has('search') && $request->input('search') != '') {
            $search = strtolower($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->orWhereRaw("LOWER(CONCAT(kd_propinsi, kd_dati2, kd_kecamatan, kd_kelurahan, kd_blok, no_urut, kd_jns_op)) LIKE ?", ['%' . $search . '%'])
                  ->orWhereRaw("LOWER(nm_wp_sppt) LIKE ?", ['%' . $search . '%'])
                  ->orWhereRaw("CAST(thn_pajak_sppt AS CHAR) LIKE ?", ['%' . $search . '%'])
                  ->orWhereRaw("LOWER(no_sk_pengurangan) LIKE ?", ['%' . $search . '%']);
            });
        }
        $laporanPengurangan = $query->paginate(25);
        $laporanPengurangan->getCollection()->map(function ($item) {
            $item->formatted_nop = $this->formatNop($item->kd_propinsi . $item->kd_dati2 . $item->kd_kecamatan . $item->kd_kelurahan . $item->kd_blok . $item->no_urut . $item->kd_jns_op);
            return $item;
        });
        if ($request->ajax()) {
            return view('pengurangan.partials.laporan_table', compact('laporanPengurangan'))->render();
        }
        $flashResults = session('pengurangan_results', []);
        return view('pengurangan.laporan', compact('laporanPengurangan', 'flashResults'));
    }

    public function cetakFilteredPdf(Request $request)
    {
        $request->validate([ 'tahun_pajak' => 'nullable|integer', 'kd_kecamatan' => 'nullable|string', 'no_sk' => 'nullable|string', 'format' => 'required|in:pdf,excel', ]);
        $query = Pengurangan::orderBy('created_at', 'asc');
        if ($request->filled('tahun_pajak')) $query->where('thn_pajak_sppt', $request->input('tahun_pajak'));
        if ($request->filled('kd_kecamatan')) $query->where('kd_kecamatan', $request->input('kd_kecamatan'));
        if ($request->filled('no_sk')) $query->where('no_sk_pengurangan', 'LIKE', '%' . $request->input('no_sk') . '%');
        $dataLaporan = $query->get();
        
        $dataLaporan->map(function ($item) {
            $item->formatted_nop = $this->formatNop($item->kd_propinsi . $item->kd_dati2 . $item->kd_kecamatan . $item->kd_kelurahan . $item->kd_blok . $item->no_urut . $item->kd_jns_op);
            return $item;
        });
        
        $fileNameBase = 'laporan_pengurangan_' . now()->format('Ymd_His');

        if ($request->input('format') === 'pdf') {
            $pdf = PDF::loadView('pengurangan.laporan_pdf', compact('dataLaporan'))->setPaper('a4', 'landscape');
            return $pdf->download($fileNameBase . '.pdf');
        } elseif ($request->input('format') === 'excel') {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $headers = [ 'NOP', 'Tahun Pajak', 'Nama WP', 'Alamat WP', 'Letak OP', 'Bumi', 'Bangunan', 'Baku', 'Jenis Pengurangan', 'Pengurangan (%)', 'Jumlah Pengurangan', 'Ketetapan Yang Harus Dibayar', 'No SK', 'Tgl SK', 'Tgl Proses', 'Operator' ];
            $sheet->fromArray([$headers], null, 'A1');
            $rowNum = 2;
            foreach ($dataLaporan as $data) {
                $rowData = [ $data->formatted_nop, $data->thn_pajak_sppt, $data->nm_wp_sppt ?? '-', $data->alamat_wp ?? '-', $data->letak_op ?? '-', (float)($data->luas_bumi_sppt ?? 0), (float)($data->luas_bng_sppt ?? 0), (float)($data->pbb_terhutang_sppt_lama ?? 0.0), $data->jenis_pengurangan ?? '-', (float)($data->persentase ?? 0.0), (float)($data->jumlah_pengurangan_baru ?? 0.0), (float)($data->ketetapan_baru ?? 0.0), $data->no_sk_pengurangan ?? '-', ($data->tgl_sk_pengurangan ? Carbon::parse($data->tgl_sk_pengurangan)->format('d-m-Y') : '-'), $data->created_at->format('d-m-Y H:i:s'), $data->operator ?? '-' ];
                $sheet->fromArray([$rowData], null, 'A' . $rowNum);
                $rowNum++;
            }
            $writer = new Xlsx($spreadsheet);
            return response()->stream(fn() => $writer->save('php://output'), 200, [ 'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'Content-Disposition' => 'attachment; filename="' . $fileNameBase . '.xlsx"', ]);
        }
    }

    public function cetakSinglePdf(int $id)
    {
        $dataLaporan = collect([Pengurangan::findOrFail($id)]);
        $dataLaporan->map(function ($item) {
            $item->formatted_nop = $this->formatNop($item->kd_propinsi . $item->kd_dati2 . $item->kd_kecamatan . $item->kd_kelurahan . $item->kd_blok . $item->no_urut . $item->kd_jns_op);
            return $item;
        });
        $pdf = PDF::loadView('pengurangan.laporan_pdf', compact('dataLaporan'))->setPaper('a4', 'landscape');
        return $pdf->download('pengurangan_' . $dataLaporan->first()->formatted_nop . '_' . now()->format('Ymd_His') . '.pdf');
    }

    public function edit(int $id)
    {
        $pengurangan = Pengurangan::findOrFail($id);
        $pengurangan->formatted_nop = $this->formatNop($pengurangan->kd_propinsi . $pengurangan->kd_dati2 . $pengurangan->kd_kecamatan . $pengurangan->kd_kelurahan . $pengurangan->kd_blok . $pengurangan->no_urut . $pengurangan->kd_jns_op);
        return view('pengurangan.edit', compact('pengurangan'));
    }

    protected function formatNop(string $nopRaw): string
    {
        if (strlen($nopRaw) == 18) {
            return substr($nopRaw, 0, 2) . '.' . substr($nopRaw, 2, 2) . '.' . substr($nopRaw, 4, 3) . '.' . substr($nopRaw, 7, 3) . '.' . substr($nopRaw, 10, 3) . '.' . substr($nopRaw, 13, 4) . '.' . substr($nopRaw, 17, 1);
        }
        return $nopRaw;
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

}
