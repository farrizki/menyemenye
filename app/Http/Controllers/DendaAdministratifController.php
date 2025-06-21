<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sppt; // Tabel Oracle
use App\Models\DatObjekPajak; // Tabel Oracle
use App\Models\RefKelurahan;  // Tabel Oracle
use App\Models\RefKecamatan;  // Tabel Oracle
use App\Models\DendaAdministratif; // Model Log MySQL
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PDF; // Untuk cetak PDF

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;

use PhpOffice\PhpSpreadsheet\Spreadsheet; // PENTING: Import PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;  // PENTING: Import PhpSpreadsheet Writer

class DendaAdministratifController extends Controller
{
    /**
     * Method untuk menampilkan form input Penghapusan Denda Administratif.
     */
    public function create()
    {
        // Data untuk dropdown kecamatan/kelurahan
        // Sesuaikan kd_propinsi dan kd_dati2 jika berbeda untuk Nganjuk
        $kecamatans = RefKecamatan::on('oracle')
            ->where('kd_propinsi', '35') // Nganjuk
            ->where('kd_dati2', '18')    // Nganjuk
            ->orderBy('nm_kecamatan')
            ->get();

        return view('denda_administratif.create', compact('kecamatans'));
    }

    /**
     * Method untuk mengambil kelurahan berdasarkan kecamatan (AJAX).
     * Digunakan di form create.
     */
    public function getKelurahanByKecamatan(Request $request)
    {
        $kdPropinsi = '35'; // Permanent Nganjuk
        $kdDati2 = '18';    // Permanent Nganjuk
        $kdKecamatan = $request->input('kd_kecamatan');

        $kelurahans = RefKelurahan::on('oracle')
            ->where('kd_propinsi', $kdPropinsi)
            ->where('kd_dati2', $kdDati2)
            ->where('kd_kecamatan', $kdKecamatan)
            ->orderBy('nm_kelurahan')
            ->get();

        return response()->json($kelurahans);
    }


    /**
     * Method untuk preview data sebelum update denda.
     * Ini adalah titik di mana semua perhitungan denda dilakukan.
     */
    public function preview(Request $request)
    {
        $request->validate([
            'input_type' => 'required|in:nop_manual,upload_excel,satu_desa', // Tipe input NOP
            'nop_manual' => 'required_if:input_type,nop_manual|string|nullable',
            'excel_file' => 'required_if:input_type,upload_excel|file|mimes:xls,xlsx|max:24576',
            'kd_kecamatan_desa' => 'required_if:input_type,satu_desa|string|nullable',
            'kd_kelurahan_desa' => 'required_if:input_type,satu_desa|string|nullable',

            'thn_pajak_input' => 'required_if:input_type,nop_manual,satu_desa|string', // Bisa multiple tahun dipisah koma
            'tgl_jatuh_tempo_baru' => 'required|date',
            'nomor_sk' => 'required|string|max:255',
            'tahun_sk' => 'required|integer|min:2000|max:2100',
            'tgl_sk' => 'required|date',
            'berkas' => 'required|file|mimes:pdf|max:24576',
        ]);

        $inputNopList = [];
        $thnUpdateOracleListPerNop = [];
        $inputType = $request->input('input_type');

        if ($request->input('input_type') === 'nop_manual') {
            $inputNopList = array_map('trim', explode(',', $request->input('nop_manual')));
            $thnInputYears = array_map('trim', explode(',', $request->input('thn_pajak_input')));
            foreach ($inputNopList as $nopItem) {
                $thnUpdateOracleListPerNop[$nopItem] = $thnInputYears;
            }

        } elseif ($request->input('input_type') === 'upload_excel') {
            $file = $request->file('excel_file');
            $thnUpdateOracleListPerNop = [];

            try {
                $spreadsheet = IOFactory::load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                $highestRow = $sheet->getHighestRow();

                for ($row = 2; $row <= $highestRow; $row++) {
                    $nopExcelRaw = (string) $sheet->getCell('A' . $row)->getValue();
                    $nopExcel = str_replace(['.', '-', ' '], '', $nopExcelRaw);

                    $tahunExcel = (string) $sheet->getCell('B' . $row)->getValue();
                    if (!empty($nopExcel) && !empty($tahunExcel)) {
                        $inputNopList[] = $nopExcel;
                        $thnUpdateOracleListPerNop[$nopExcel][] = $tahunExcel;
                    }
                }
            } catch (ReaderException $e) {
                return redirect()->back()->withErrors(['excel_file' => 'Gagal membaca file Excel: ' . $e->getMessage()]);
            }
            $inputNopList = array_unique($inputNopList);

        } elseif ($request->input('input_type') === 'satu_desa') {
            $kdPropinsi = '35'; $kdDati2 = '18';
            $kdKecamatan = $request->input('kd_kecamatan_desa');
            $kdKelurahan = $request->input('kd_kelurahan_desa');
            $thnInputYears = array_map('trim', explode(',', $request->input('thn_pajak_input')));

            $spptRecords = Sppt::on('oracle')
                ->select('kd_propinsi', 'kd_dati2', 'kd_kecamatan', 'kd_kelurahan', 'kd_blok', 'no_urut', 'kd_jns_op')
                ->where('kd_propinsi', $kdPropinsi)->where('kd_dati2', $kdDati2)
                ->where('kd_kecamatan', $kdKecamatan)->where('kd_kelurahan', $kdKelurahan)
                ->distinct()->get();

            foreach ($spptRecords as $sppt) {
                $nopFull = $sppt->kd_propinsi . $sppt->kd_dati2 . $sppt->kd_kecamatan . $sppt->kd_kelurahan . $sppt->kd_blok . $sppt->no_urut . $sppt->kd_jns_op;
                $inputNopList[] = $nopFull;
                $thnUpdateOracleListPerNop[$nopFull] = $thnInputYears;
            }
            $inputNopList = array_unique($inputNopList);
        }

        if (empty($inputNopList)) {
            return redirect()->back()->withErrors(['nop' => 'Tidak ada NOP yang ditemukan untuk diproses.']);
        }

        $tglJatuhTempoBaru = Carbon::parse($request->input('tgl_jatuh_tempo_baru'));
        $inputNomorSk = $request->input('nomor_sk');
        $inputTahunSk = $request->input('tahun_sk');
        $tglSk = Carbon::parse($request->input('tgl_sk'));

        $noSkLengkap = '100.3.3.2/' . $inputNomorSk . '/K/411.403/' . $inputTahunSk;

        $berkasTempPath = null;
        $berkasOriginalName = null;
        if ($request->hasFile('berkas')) {
            $file = $request->file('berkas');
            $berkasOriginalName = $file->getClientOriginalName();
            $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $berkasTempPath = $file->storeAs('temp_denda_uploads', $fileName, 'local'); 
        }

        $dataToProcess = [];

        foreach ($inputNopList as $nop) {
            if (strlen($nop) != 18) {
                $dataToProcess[] = [
                    'nop' => $nop, 'thn_pajak_sppt' => 'N/A', 'status_validasi' => 'Gagal', 'message' => 'Format NOP tidak valid: ' . $nop . '. Must be 18 characters.'
                ]; continue;
            }

            $kdPropinsi   = substr($nop, 0, 2); $kdDati2      = substr($nop, 2, 2);
            $kdKecamatan  = substr($nop, 4, 3); $kdKelurahan  = substr($nop, 7, 3);
            $kdBlok       = substr($nop, 10, 3); $noUrut       = substr($nop, 13, 4);
            $kdJnsOp      = substr($nop, 17, 1);

            $targetYearsForNop = $thnUpdateOracleListPerNop[$nop] ?? [];

            if (empty($targetYearsForNop)) {
                 $dataToProcess[] = [
                    'nop' => $nop, 'thn_pajak_sppt' => 'N/A', 'status_validasi' => 'Gagal',
                    'message' => 'No tax year specified for this NOP.'
                ]; continue;
            }

            foreach ($targetYearsForNop as $thnUpdateOracle) {
                if (empty($thnUpdateOracle)) continue;

                try {
                    $spptData = Sppt::on('oracle')
                        ->where('kd_propinsi', $kdPropinsi)->where('kd_dati2', $kdDati2)
                        ->where('kd_kecamatan', $kdKecamatan)->where('kd_kelurahan', $kdKelurahan)
                        ->where('kd_blok', $kdBlok)->where('no_urut', $noUrut)->where('kd_jns_op', $kdJnsOp)
                        ->where('thn_pajak_sppt', (string)$thnUpdateOracle)
                        ->first();

                    $statusValidasi = 'Gagal';
                    $message = 'Data SPPT not found for NOP/Year.';

                    if ($spptData) {
                        if ((int)($spptData->status_pembayaran_sppt ?? 99) !== 0) {
                            $statusValidasi = 'Tidak Diproses'; 
                            $message = 'SPPT already paid (Status: ' . ($spptData->status_pembayaran_sppt ?? 'NULL') . ').';
                        } else {
                            $statusValidasi = 'Siap Diproses';
                            $message = 'Data found and ready for fine processing.';
                        }
                    }

                    if ($statusValidasi !== 'Tidak Diproses') {
                        $nmWp = $spptData->nm_wp_sppt ?? '-';
                        $alamatWp = ($spptData->jln_wp_sppt ?? '-') . ' RT. ' . ($spptData->rt_wp_sppt ?? '-') .
                                    ' RW. ' . ($spptData->rw_wp_sppt ?? '-') . ' Kel/Desa. ' . ($spptData->kelurahan_wp_sppt ?? '-') .
                                    ' Kab/Kota. ' . ($spptData->kota_wp_sppt ?? '-');

                        $objekPajak = null;
                        if ($spptData) {
                            $objekPajak = DatObjekPajak::on('oracle')
                                ->where('kd_propinsi', $kdPropinsi)->where('kd_dati2', $kdDati2)
                                ->where('kd_kecamatan', $kdKecamatan)->where('kd_kelurahan', $kdKelurahan)
                                ->where('kd_blok', $kdBlok)->where('no_urut', $noUrut)->where('kd_jns_op', $kdJnsOp)
                                ->first();
                        }

                        $nmKelurahanOp = null; $nmKecamatanOp = null;
                        if ($objekPajak) {
                            $refKelurahan = RefKelurahan::on('oracle')
                                ->where('kd_propinsi', $objekPajak->kd_propinsi)->where('kd_dati2', $objekPajak->kd_dati2)
                                ->where('kd_kecamatan', $objekPajak->kd_kecamatan)->where('kd_kelurahan', $objekPajak->kd_kelurahan)
                                ->first();
                            if ($refKelurahan) $nmKelurahanOp = $refKelurahan->nm_kelurahan;
                            $refKecamatan = RefKecamatan::on('oracle')
                                ->where('kd_propinsi', $objekPajak->kd_propinsi)->where('kd_dati2', $objekPajak->kd_dati2)
                                ->where('kd_kecamatan', $objekPajak->kd_kecamatan)
                                ->first();
                            if ($refKecamatan) $nmKecamatanOp = $refKecamatan->nm_kecamatan;
                        }
                        $letakOp = ($objekPajak->jalan_op ?? '-') . ' RT. ' . ($objekPajak->rt_op ?? '-') .
                            ' RW. ' . ($objekPajak->rw_op ?? '-') . ' Kel/Desa. ' . ($nmKelurahanOp ?? '-') .
                            ' Kec. ' . ($nmKecamatanOp ?? '-') . ' Kab/Kota. Nganjuk';

                        $pokok = (float)($spptData->pbb_yg_harus_dibayar_sppt ?? 0.0);
                        $rawTglJatuhTempoSpptAsli = $spptData->tgl_jatuh_tempo_sppt;
                        if (is_null($rawTglJatuhTempoSpptAsli) || empty($rawTglJatuhTempoSpptAsli)) {
                            $tglJatuhTempoSpptAsli = Carbon::create((int)$thnUpdateOracle, 1, 1);
                        } else {
                            $tglJatuhTempoSpptAsli = Carbon::parse($rawTglJatuhTempoSpptAsli);
                        }

                        $denda = 0.0;
                        $today = Carbon::today();

                        $diffInMonths = 0;
                        if ($today->greaterThan($tglJatuhTempoSpptAsli->copy()->endOfMonth())) {
                            $startCountingDate = $tglJatuhTempoSpptAsli->copy()->endOfMonth()->addDay();
                            while ($startCountingDate->lessThanOrEqualTo($today)) {
                                $diffInMonths++;
                                $startCountingDate->addMonth();
                            }

                            if ($diffInMonths === 0 && $today->greaterThan($tglJatuhTempoSpptAsli)) {
                                $diffInMonths = 1;
                            }
                        }

                        if ($diffInMonths > 0) {
                            $persenDendaPerBulan = ((int)$thnUpdateOracle <= 2023) ? 0.02 : 0.01;
                            $denda = $pokok * $persenDendaPerBulan * $diffInMonths;
                        }

                        $maxDendaPercentage = ((int)$thnUpdateOracle <= 2023) ? 0.30 : 0.15;
                        $maxDendaAmount = $pokok * $maxDendaPercentage;

                        if ($denda > $maxDendaAmount) {
                            $denda = $maxDendaAmount;
                        }

                        $pokok = ceil($pokok);
                        $denda = ceil($denda);

                        $jumlahPajak = $pokok + $denda;
                        $jumlahPajak = ceil($jumlahPajak);

                        $sanksiAdministratif = $denda;
                        $yangHarusDibayar = $pokok;

                        $dataToProcess[] = [
                            'kd_propinsi' => $kdPropinsi, 'kd_dati2' => $kdDati2,
                            'kd_kecamatan' => $kdKecamatan, 'kd_kelurahan' => $kdKelurahan,
                            'kd_blok' => $kdBlok, 'no_urut' => $noUrut, 'kd_jns_op' => $kdJnsOp,
                            'nop' => $nop,
                            'formatted_nop' => $this->formatNop($nop),
                            'thn_pajak_sppt' => (string)$thnUpdateOracle,
                            'nm_wp_sppt' => $nmWp,
                            'alamat_wp' => $alamatWp,
                            'letak_op' => $letakOp,
                            'pokok' => $pokok,
                            'denda' => $denda,
                            'jumlah_pajak' => $jumlahPajak,
                            'sanksi_administratif' => $sanksiAdministratif,
                            'yang_harus_dibayar' => $yangHarusDibayar,
                            'no_sk' => $noSkLengkap,
                            'tgl_sk' => $tglSk,
                            'tgl_jatuh_tempo_baru' => $tglJatuhTempoBaru,
                            'tgl_jatuh_tempo_sppt_lama' => $tglJatuhTempoSpptAsli,
                            'status_validasi' => $statusValidasi,
                            'message' => $message,
                        ];
                    } else {
                        if ($inputType !== 'satu_desa') {
                            $dataToProcess[] = [
                                'nop' => $nop, 'thn_pajak_sppt' => (string)$thnUpdateOracle, 'status_validasi' => 'Gagal',
                                'message' => 'Data SPPT tidak ditemukan untuk NOP/Tahun ini.'
                            ];
                        }
                    }
                } catch (\Exception $e) {
                    $dataToProcess[] = [
                        'nop' => $nop, 'thn_pajak_sppt' => (string)$thnUpdateOracle, 'status_validasi' => 'Error',
                        'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage() . ' (Line: ' . $e->getLine() . ')'
                    ];
                }
            }
        }

        $request->session()->put('denda_data_preview', $dataToProcess);
        if($berkasTempPath){
            $request->session()->put('berkas_temp_path', $berkasTempPath);
            $request->session()->put('berkas_original_name', $berkasOriginalName);
        } else {
            $request->session()->forget('berkas_temp_path');
            $request->session()->forget('berkas_original_name');
        }

        return view('denda_administratif.preview', compact('dataToProcess', 'noSkLengkap', 'tglSk', 'tglJatuhTempoBaru', 'inputType'));
    }

    public function confirmStore(Request $request)
    {
        $dataToProcess = $request->session()->get('denda_data_preview');
        $berkasTempPath = $request->session()->get('berkas_temp_path');
        $berkasOriginalName = $request->session()->get('berkas_original_name');

        if (empty($dataToProcess)) {
            return redirect()->route('denda_administratif.create')->withErrors('Tidak ada data untuk diproses. Silakan ulangi dari awal.');
        }

        $results = [];
        $finalBerkasPath = null;

        if ($berkasTempPath && Storage::disk('local')->exists($berkasTempPath)) {
            $newFileName = Str::random(40) . '.' . pathinfo($berkasOriginalName, PATHINFO_EXTENSION);
            $finalBerkasPath = Storage::disk('public')->putFileAs('berkas_denda', new \Illuminate\Http\File(Storage::disk('local')->path($berkasTempPath)), $newFileName);

            Storage::disk('local')->delete($berkasTempPath);
        }

        foreach ($dataToProcess as $data) {
            if (isset($data['status_validasi']) && $data['status_validasi'] === 'Siap Diproses') {
                try {
                    $updatedOracle = DB::connection('oracle')->table('SPPT')
                        ->where('kd_propinsi', $data['kd_propinsi'])
                        ->where('kd_dati2', $data['kd_dati2'])
                        ->where('kd_kecamatan', $data['kd_kecamatan'])
                        ->where('kd_kelurahan', $data['kd_kelurahan'])
                        ->where('kd_blok', $data['kd_blok'])
                        ->where('no_urut', $data['no_urut'])
                        ->where('kd_jns_op', $data['kd_jns_op'])
                        ->where('thn_pajak_sppt', $data['thn_pajak_sppt'])
                        ->where('status_pembayaran_sppt', 0)
                        ->update([
                            'tgl_jatuh_tempo_sppt' => $data['tgl_jatuh_tempo_baru'],
                        ]);

                    if ($updatedOracle > 0) {
                        DendaAdministratif::create([
                            'kd_propinsi' => $data['kd_propinsi'], 'kd_dati2' => $data['kd_dati2'],
                            'kd_kecamatan' => $data['kd_kecamatan'], 'kd_kelurahan' => $data['kd_kelurahan'],
                            'kd_blok' => $data['kd_blok'], 'no_urut' => $data['no_urut'], 'kd_jns_op' => $data['kd_jns_op'],
                            'thn_pajak_sppt' => (string)$data['thn_pajak_sppt'],
                            'nm_wp_sppt' => $data['nm_wp_sppt'],
                            'alamat_wp' => $data['alamat_wp'],
                            'letak_op' => $data['letak_op'],
                            'pokok' => $data['pokok'],
                            'denda' => $data['denda'],
                            'jumlah_pajak' => $data['jumlah_pajak'],
                            'sanksi_administratif' => $data['sanksi_administratif'],
                            'yang_harus_dibayar' => $data['yang_harus_dibayar'],
                            'no_sk' => $data['no_sk'],
                            'tgl_sk' => $data['tgl_sk'],
                            'berkas_path' => $finalBerkasPath,
                            'tgl_jatuh_tempo_baru' => $data['tgl_jatuh_tempo_baru'],
                            'operator' => Auth::user()->name ?? 'System',
                            'tgl_jatuh_tempo_sppt_lama' => $data['tgl_jatuh_tempo_sppt_lama'],
                        ]);
                        $results[] = ['nop' => $data['formatted_nop'], 'status' => 'Berhasil', 'message' => 'Denda berhasil diupdate di Oracle dan log disimpan.'];
                    } else {
                        $results[] = ['nop' => $data['formatted_nop'], 'status' => 'Gagal', 'message' => 'Gagal update Oracle (data tidak ditemukan/status sudah bayar).'];
                    }
                } catch (\Exception $e) {
                    $results[] = ['nop' => $data['formatted_nop'] ?? $data['nop'], 'status' => 'Error', 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage() . ' (Line: ' . $e->getLine() . ')'];
                }
            } else {
                $results[] = ['nop' => $data['formatted_nop'] ?? $data['nop'], 'status' => $data['status_validasi'], 'message' => $data['message']];
            }
        }
        $request->session()->forget('berkas_temp_path');
        $request->session()->forget('berkas_original_name');
        $request->session()->forget('denda_data_preview');

        return redirect()->route('denda_administratif.index')->with('success', 'Proses penghapusan denda selesai. Detail di laporan.');
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'tgl_jatuh_tempo_baru' => 'required|date',
            'nomor_sk' => 'required|string|max:255',
            'tahun_sk' => 'required|integer|min:2000|max:2100',
            'tgl_sk' => 'required|date',
            'berkas' => 'nullable|file|mimes:pdf|max:24576',
        ]);

        $denda = DendaAdministratif::findOrFail($id);

        $tglJatuhTempoBaru = Carbon::parse($request->input('tgl_jatuh_tempo_baru'));
        $inputNomorSk = $request->input('nomor_sk');
        $inputTahunSk = $request->input('tahun_sk');
        $tglSk = Carbon::parse($request->input('tgl_sk'));

        $noSkLengkap = '100.3.3.2/' . $inputNomorSk . '/K/411.403/' . $inputTahunSk;

        $pokok = (float)($denda->pokok ?? 0.0);
        $thn_pajak_sppt = (int)($denda->thn_pajak_sppt);

        $dendaBaru = 0.0;
        $today = Carbon::today();
        $tglJatuhTempoSpptEndOfMonth = $tglJatuhTempoBaru->copy()->endOfMonth();

        if ($today->greaterThan($tglJatuhTempoSpptEndOfMonth)) {
            $diffInMonths = 0;
            $startCountingDate = $tglJatuhTempoSpptEndOfMonth->copy()->addDay(); // PERBAIKAN: Mulai dari addDay()

            while ($startCountingDate->lessThanOrEqualTo($today)) {
                $diffInMonths++;
                $startCountingDate->addMonth();
            }

            if ($diffInMonths > 0) {
                $persenDendaPerBulan = ($thn_pajak_sppt <= 2023) ? 0.02 : 0.01;
                $dendaBaru = $pokok * $persenDendaPerBulan * $diffInMonths;
            }
        }

        $maxDendaPercentage = ((int)$thn_pajak_sppt <= 2023) ? 0.30 : 0.15;
        $maxDendaAmount = $pokok * $maxDendaPercentage;

        if ($dendaBaru > $maxDendaAmount) {
            $dendaBaru = $maxDendaAmount;
        }

        $pokok = ceil($pokok);
        $dendaBaru = ceil($dendaBaru);

        $jumlahPajakBaru = $pokok + $dendaBaru;
        $jumlahPajakBaru = ceil($jumlahPajakBaru);

        $sanksiAdministratifBaru = $dendaBaru;
        $yangHarusDibayarBaru = $pokok;

        $berkasPath = $denda->berkas_path;
        if ($request->hasFile('berkas')) {
            if ($denda->berkas_path && Storage::exists($denda->berkas_path)) {
                Storage::delete($denda->berkas_path);
            }
            $file = $request->file('berkas');
            $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $berkasPath = Storage::disk('public')->putFileAs('berkas_denda', $file, $fileName);
        } elseif ($request->boolean('remove_berkas')) {
            if ($denda->berkas_path && Storage::exists($denda->berkas_path)) {
                Storage::delete($denda->berkas_path);
            }
            $berkasPath = null;
        }

        $denda->update([
            'tgl_jatuh_tempo_baru' => $tglJatuhTempoBaru,
            'no_sk' => $noSkLengkap,
            'tgl_sk' => $tglSk,
            'berkas_path' => $berkasPath,
            'pokok' => $pokok,
            'denda' => $dendaBaru,
            'jumlah_pajak' => $jumlahPajakBaru,
            'sanksi_administratif' => $sanksiAdministratifBaru,
            'yang_harus_dibayar' => $yangHarusDibayarBaru,
            'operator' => Auth::user()->name ?? 'System',
            'tgl_jatuh_tempo_sppt_lama' => $denda->tgl_jatuh_tempo_sppt_lama,
        ]);

        return redirect()->route('denda_administratif.index')->with('success', 'Data denda administratif berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $denda = DendaAdministratif::findOrFail($id);

        if ($denda->berkas_path && Storage::exists($denda->berkas_path)) {
            Storage::delete($denda->berkas_path);
        }

        try {
            $originalJatuhTempoSppt = $denda->tgl_jatuh_tempo_sppt_lama;

            if ($originalJatuhTempoSppt instanceof Carbon) {
                $originalJatuhTempoSppt = $originalJatuhTempoSppt->toDateString();
            } else if (!is_string($originalJatuhTempoSppt) || empty($originalJatuhTempoSppt)) {
                 $originalJatuhTempoSppt = Carbon::create((int)$denda->thn_pajak_sppt, 1, 1)->toDateString();
            }

            $updatedOracle = DB::connection('oracle')->table('SPPT')
                ->where('kd_propinsi', $denda->kd_propinsi)
                ->where('kd_dati2', $denda->kd_dati2)
                ->where('kd_kecamatan', $denda->kd_kecamatan)
                ->where('kd_kelurahan', $denda->kd_kelurahan)
                ->where('kd_blok', $denda->kd_blok)
                ->where('no_urut', $denda->no_urut)
                ->where('kd_jns_op', $denda->kd_jns_op)
                ->where('thn_pajak_sppt', $denda->thn_pajak_sppt)
                ->where('status_pembayaran_sppt', 0)
                ->update([
                    'tgl_jatuh_tempo_sppt' => $originalJatuhTempoSppt,
                ]);

            if ($updatedOracle > 0) {
                $denda->delete();
                return redirect()->route('denda_administratif.index')->with('success', 'Record denda berhasil dihapus dan data Oracle berhasil dikembalikan.');
            } else {
                return redirect()->route('denda_administratif.index')->with('error', 'Gagal mengembalikan data Oracle. Mungkin status pembayaran sudah berubah atau data tidak ditemukan.');
            }

        } catch (\Exception $e) {
            return redirect()->route('denda_administratif.index')->with('error', 'Terjadi kesalahan saat menghapus record: ' . $e->getMessage());
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
}