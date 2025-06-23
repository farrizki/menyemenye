<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sppt;
use App\Models\DatObjekPajak;
use App\Models\RefKelurahan;
use App\Models\RefKecamatan;
use App\Models\DendaAdministratif;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PDF;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DendaAdministratifController extends Controller
{
    /**
     * Menampilkan daftar laporan denda administratif.
     */
    public function index(Request $request)
    {
        $query = DendaAdministratif::orderBy('created_at', 'desc');

        if ($request->has('search') && $request->input('search') != '') {
            $search = strtolower($request->input('search'));
            $nopSearch = preg_replace('/[^0-9]/', '', $search);

            $query->where(function ($q) use ($search, $nopSearch) {
                if (!empty($nopSearch)) {
                    $q->orWhereRaw("LOWER(CONCAT(kd_propinsi, kd_dati2, kd_kecamatan, kd_kelurahan, kd_blok, no_urut, kd_jns_op)) LIKE ?", ['%' . $nopSearch . '%']);
                }
                $q->orWhereRaw("LOWER(nm_wp_sppt) LIKE ?", ['%' . $search . '%'])
                  ->orWhereRaw("CAST(thn_pajak_sppt AS CHAR) LIKE ?", ['%' . $search . '%'])
                  ->orWhereRaw("LOWER(no_sk) LIKE ?", ['%' . $search . '%']);
            });
        }
        
        $laporanDenda = $query->paginate(25);
        $flashResults = session('denda_results', []);
        
        if ($request->ajax()) {
            return view('denda_administratif.partials.laporan_table', compact('laporanDenda'))->render();
        }

        return view('denda_administratif.index', compact('laporanDenda', 'flashResults'));
    }
    
    /**
     * Menampilkan form untuk membuat data baru.
     */
    public function create()
    {
        $kecamatans = RefKecamatan::on('oracle')
            ->where('kd_propinsi', '35')->where('kd_dati2', '18')
            ->orderBy('nm_kecamatan')->get();
        return view('denda_administratif.create', compact('kecamatans'));
    }

    /**
     * Menampilkan halaman edit untuk satu record.
     */
    public function edit(int $id)
    {
        $denda = DendaAdministratif::findOrFail($id);
        
        // PERBAIKAN: Menyiapkan semua data yang dibutuhkan oleh view
        $denda->formatted_nop = $this->formatNop($denda->kd_propinsi . $denda->kd_dati2 . $denda->kd_kecamatan . $denda->kd_kelurahan . $denda->kd_blok . $denda->no_urut . $denda->kd_jns_op);
        
        // Memecah no_sk untuk pre-fill form
        $parts = explode('/', $denda->no_sk);
        $denda->nomor_sk_raw = $parts[1] ?? '';
        $denda->tahun_sk_raw = $parts[4] ?? '';
        
        return view('denda_administratif.edit', compact('denda'));
    }

    /**
     * Mengambil data kelurahan via AJAX.
     */
    public function getKelurahanByKecamatan(Request $request)
    {
        $kelurahans = RefKelurahan::on('oracle')
            ->where('kd_propinsi', '35')->where('kd_dati2', '18')
            ->where('kd_kecamatan', $request->input('kd_kecamatan'))
            ->orderBy('nm_kelurahan')->get();
        return response()->json($kelurahans);
    }

    /**
     * Method untuk preview data sebelum update denda.
     * Sekarang menangani 'create' dan 'edit'.
     */
    public function preview(Request $request)
    {
        // Validasi, berkas tidak wajib saat edit (nullable)
        $request->validate([
            'input_type' => 'required|in:nop_manual,upload_excel,satu_desa',
            'nop_manual' => 'required_if:input_type,nop_manual|string|nullable',
            'excel_file' => 'required_if:input_type,upload_excel|file|mimes:xls,xlsx|max:24576',
            'kd_kecamatan_desa' => 'required_if:input_type,satu_desa|string|nullable',
            'kd_kelurahan_desa' => 'required_if:input_type,satu_desa|string|nullable',
            'thn_pajak_input' => 'required|string',
            'tgl_jatuh_tempo_baru' => 'required|date',
            'nomor_sk' => 'required|string|max:255',
            'tahun_sk' => 'required|integer|min:2000|max:2100',
            'tgl_sk' => 'required|date',
            'berkas' => 'nullable|file|mimes:pdf|max:24576',
            'denda_id' => 'sometimes|integer|exists:denda_administratif,id'
        ]);

        // Simpan ID jika ini adalah proses edit
        if ($request->has('denda_id')) {
            $request->session()->put('denda_id_to_update', $request->input('denda_id'));
        } else {
            $request->session()->forget('denda_id_to_update');
        }
        
        $nopTahunPairs = $this->getNopTahunPairs($request);
        
        if (empty($nopTahunPairs)) {
            return redirect()->back()->withErrors(['nop' => 'Tidak ada NOP yang valid untuk diproses.'])->withInput();
        }

        $tglJatuhTempoBaru = $request->input('tgl_jatuh_tempo_baru');
        $noSkLengkap = '100.3.3.2/' . $request->input('nomor_sk') . '/K/411.403/' . $request->input('tahun_sk');

        if ($request->hasFile('berkas')) {
            $file = $request->file('berkas');
            $berkasPath = $file->storeAs('temp_denda_uploads', Str::random(40) . '.' . $file->getClientOriginalExtension(), 'local');
            $request->session()->put('berkas_temp_path', $berkasPath);
            $request->session()->put('berkas_original_name', $file->getClientOriginalName());
        }

        $dataToProcess = [];
        foreach ($nopTahunPairs as $pair) {
            $dataToProcess[] = $this->getDendaDetails($pair['nop'], $pair['tahun']);
        }

        $request->session()->put('denda_data_preview', $dataToProcess);
        $request->session()->put('tgl_jatuh_tempo_baru', $tglJatuhTempoBaru);
        $request->session()->put('no_sk_lengkap', $noSkLengkap);
        $request->session()->put('tgl_sk', $request->input('tgl_sk'));
        
        return view('denda_administratif.preview', [
            'dataToProcess' => $dataToProcess,
            'tglJatuhTempoBaru' => $tglJatuhTempoBaru,
            'noSkLengkap' => $noSkLengkap,
            'tglSk' => $request->input('tgl_sk'),
            'inputType' => $request->input('input_type')
        ]);
    }

    /**
     * Method konfirmasi untuk menyimpan data, baik CREATE maupun UPDATE.
     */
    public function confirmStore(Request $request)
    {
        $dataToProcess = $request->session()->get('denda_data_preview');
        $dendaId = $request->session()->get('denda_id_to_update');
        $tglJatuhTempoBaru = $request->session()->get('tgl_jatuh_tempo_baru');
        $noSkLengkap = $request->session()->get('no_sk_lengkap');
        $tglSk = $request->session()->get('tgl_sk');
        
        if (empty($dataToProcess)) {
            return redirect()->back()->withErrors('Sesi habis, silakan ulangi proses.');
        }

        $berkasTempPath = $request->session()->get('berkas_temp_path');
        $berkasOriginalName = $request->session()->get('berkas_original_name');
        $finalBerkasPath = null;

        if ($berkasTempPath && Storage::disk('local')->exists($berkasTempPath)) {
            $newFileName = Str::random(40) . '.' . pathinfo($berkasOriginalName, PATHINFO_EXTENSION);
            $finalBerkasPath = Storage::disk('public')->putFileAs('berkas_denda', new \Illuminate\Http\File(storage_path('app/' . $berkasTempPath)), $newFileName);
            Storage::disk('local')->delete($berkasTempPath);
        }

        $results = [];
        foreach ($dataToProcess as $data) {
            if ($data['status_validasi'] === 'Siap Diproses') {
                try {
                    $updatedOracle = DB::connection('oracle')->table('SPPT')
                        ->where('kd_propinsi', $data['kd_propinsi'])->where('kd_dati2', $data['kd_dati2'])
                        ->where('kd_kecamatan', $data['kd_kecamatan'])->where('kd_kelurahan', $data['kd_kelurahan'])
                        ->where('kd_blok', $data['kd_blok'])->where('no_urut', $data['no_urut'])->where('kd_jns_op', $data['kd_jns_op'])
                        ->where('thn_pajak_sppt', $data['thn_pajak_sppt'])->where('status_pembayaran_sppt', 0)
                        ->update(['tgl_jatuh_tempo_sppt' => $tglJatuhTempoBaru]);

                    if ($updatedOracle > 0) {
                        $logData = [
                            'kd_propinsi' => $data['kd_propinsi'], 'kd_dati2' => $data['kd_dati2'], 'kd_kecamatan' => $data['kd_kecamatan'],
                            'kd_kelurahan' => $data['kd_kelurahan'], 'kd_blok' => $data['kd_blok'], 'no_urut' => $data['no_urut'], 'kd_jns_op' => $data['kd_jns_op'],
                            'thn_pajak_sppt' => $data['thn_pajak_sppt'], 'nm_wp_sppt' => $data['nm_wp_sppt'], 'alamat_wp' => $data['alamat_wp'],
                            'letak_op' => $data['letak_op'], 'pokok' => $data['pokok'], 'denda' => $data['denda'], 'jumlah_pajak' => $data['jumlah_pajak'],
                            'sanksi_administratif' => $data['sanksi_administratif'], 'yang_harus_dibayar' => $data['yang_harus_dibayar'],
                            'no_sk' => $noSkLengkap, 'tgl_sk' => $tglSk, 'tgl_jatuh_tempo_baru' => $tglJatuhTempoBaru,
                            'original_jatuh_tempo' => $data['tgl_jatuh_tempo_sppt_lama'], 'operator' => Auth::user()->name ?? 'System',
                        ];

                        if ($dendaId) {
                            $denda = DendaAdministratif::find($dendaId);
                            if ($denda) {
                                if ($finalBerkasPath) {
                                    if ($denda->berkas_path) Storage::disk('public')->delete($denda->berkas_path);
                                    $logData['berkas_path'] = $finalBerkasPath;
                                }
                                $denda->update($logData);
                            }
                        } else {
                            $logData['berkas_path'] = $finalBerkasPath;
                            DendaAdministratif::create($logData);
                        }
                        $results[] = ['nop' => $data['formatted_nop'], 'status' => 'Berhasil', 'message' => 'Data berhasil diproses.'];
                    } else {
                         $results[] = ['nop' => $data['formatted_nop'], 'status' => 'Gagal', 'message' => 'Gagal update Oracle (data tidak ditemukan/lunas).'];
                    }
                } catch (\Exception $e) {
                     $results[] = ['nop' => $data['formatted_nop'], 'status' => 'Error', 'message' => 'Kesalahan sistem: ' . $e->getMessage()];
                }
            } else {
                $results[] = ['nop' => $data['formatted_nop'], 'status' => $data['status_validasi'], 'message' => $data['message']];
            }
        }
        
        $request->session()->forget(['denda_data_preview', 'denda_id_to_update', 'berkas_temp_path', 'berkas_original_name', 'tgl_jatuh_tempo_baru', 'no_sk_lengkap', 'tgl_sk']);
        $request->session()->flash('denda_results', $results);
        return redirect()->route('denda_administratif.index')->with('success', 'Proses penghapusan denda selesai.');
    }

    public function destroy(int $id)
    {
        $denda = DendaAdministratif::findOrFail($id);
        if ($denda->berkas_path && Storage::disk('public')->exists($denda->berkas_path)) {
            Storage::disk('public')->delete($denda->berkas_path);
        }
        try {
            $updatedOracle = DB::connection('oracle')->table('SPPT')
                ->where('kd_propinsi', $denda->kd_propinsi)->where('kd_dati2', $denda->kd_dati2)
                ->where('kd_kecamatan', $denda->kd_kecamatan)->where('kd_kelurahan', $denda->kd_kelurahan)
                ->where('kd_blok', $denda->kd_blok)->where('no_urut', $denda->no_urut)->where('kd_jns_op', $denda->kd_jns_op)
                ->where('thn_pajak_sppt', $denda->thn_pajak_sppt)->where('status_pembayaran_sppt', 0)
                ->update(['tgl_jatuh_tempo_sppt' => $denda->original_jatuh_tempo]);

            if ($updatedOracle > 0) {
                $denda->delete();
                return redirect()->route('denda_administratif.index')->with('success', 'Record denda berhasil dihapus dan data Oracle dikembalikan.');
            }
            return redirect()->route('denda_administratif.index')->withErrors('Gagal mengembalikan data Oracle. Mungkin status pembayaran sudah berubah.');
        } catch (\Exception $e) {
            return redirect()->route('denda_administratif.index')->withErrors('Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function cetakFilteredPdf(Request $request)
    {
        $request->validate([ 'tahun_pajak' => 'nullable|integer', 'kd_kecamatan' => 'nullable|string', 'no_sk' => 'nullable|string', 'format' => 'required|in:pdf,excel', ]);
        $query = DendaAdministratif::orderBy('created_at', 'asc');
        if ($request->filled('tahun_pajak')) $query->where('thn_pajak_sppt', $request->input('tahun_pajak'));
        if ($request->filled('kd_kecamatan')) $query->where('kd_kecamatan', $request->input('kd_kecamatan'));
        if ($request->filled('no_sk')) $query->where('no_sk', 'LIKE', '%' . $request->input('no_sk') . '%');
        $dataLaporan = $query->get();
        $fileNameBase = 'laporan_denda_administratif_' . now()->format('Ymd_His');
        if ($request->input('format') === 'pdf') {
            $f4LandscapeCustomPaper = array(0, 0, 935.433, 609.448);
            $pdf = PDF::loadView('denda_administratif.laporan_pdf', compact('dataLaporan'))->setPaper($f4LandscapeCustomPaper);
            return $pdf->download($fileNameBase . '.pdf');
        } elseif ($request->input('format') === 'excel') {
            $spreadsheet = new Spreadsheet(); $sheet = $spreadsheet->getActiveSheet();
            $headers = ['NOP', 'Tahun Pajak', 'Nama WP', 'Alamat WP', 'Letak OP', 'Pokok', 'Denda', 'Jumlah Pajak', 'Sanksi Administratif', 'Yang Harus Dibayar', 'No SK', 'Tgl SK', 'Tgl Jatuh Tempo Baru', 'Tgl Proses', 'Operator'];
            $sheet->fromArray([$headers], null, 'A1');
            $rowNum = 2;
            foreach ($dataLaporan as $data) {
                $rowData = [ $this->formatNop($data->kd_propinsi . $data->kd_dati2 . $data->kd_kecamatan . $data->kd_kelurahan . $data->kd_blok . $data->no_urut . $data->kd_jns_op), $data->thn_pajak_sppt, $data->nm_wp_sppt ?? '-', $data->alamat_wp ?? '-', $data->letak_op ?? '-', (float)($data->pokok ?? 0), (float)($data->denda ?? 0), (float)($data->jumlah_pajak ?? 0), (float)($data->sanksi_administratif ?? 0), (float)($data->yang_harus_dibayar ?? 0), $data->no_sk ?? '-', $data->tgl_sk ? Carbon::parse($data->tgl_sk)->format('d-m-Y') : '-', $data->tgl_jatuh_tempo_baru ? Carbon::parse($data->tgl_jatuh_tempo_baru)->format('d-m-Y') : '-', $data->created_at->format('d-m-Y H:i:s'), $data->operator ?? '-' ];
                $sheet->fromArray([$rowData], null, 'A' . $rowNum);
                $rowNum++;
            }
            $writer = new Xlsx($spreadsheet);
            return response()->stream(fn() => $writer->save('php://output'), 200, [ 'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'Content-Disposition' => 'attachment; filename="' . $fileNameBase . '.xlsx"', ]);
        }
    }
    
    public function cetakSinglePdf(int $id)
    {
        $denda = DendaAdministratif::findOrFail($id);
        $denda->formatted_nop = $this->formatNop($denda->kd_propinsi . $denda->kd_dati2 . $denda->kd_kecamatan . $denda->kd_kelurahan . $denda->kd_blok . $denda->no_urut . $denda->kd_jns_op);
        $dataLaporan = collect([$denda]);
        $f4LandscapeCustomPaper = array(0, 0, 935.433, 609.448);
        $pdf = PDF::loadView('denda_administratif.laporan_pdf', compact('dataLaporan'))->setPaper($f4LandscapeCustomPaper);
        return $pdf->download('denda_sppt_' . str_replace('.', '', $denda->formatted_nop) . '_' . $denda->thn_pajak_sppt . '.pdf');
    }
    
    protected function formatNop(string $nopRaw): string
    {
        if (strlen($nopRaw) == 18) {
            return substr($nopRaw, 0, 2) . '.' . substr($nopRaw, 2, 2) . '.' . substr($nopRaw, 4, 3) . '.' . substr($nopRaw, 7, 3) . '.' . substr($nopRaw, 10, 3) . '.' . substr($nopRaw, 13, 4) . '.' . substr($nopRaw, 17, 1);
        }
        return $nopRaw;
    }
    
    private function getNopTahunPairs(Request $request) {
        $pairs = []; $inputType = $request->input('input_type');
        if ($inputType === 'nop_manual') {
            $inputNopList = array_map('trim', explode(',', $request->input('nop_manual')));
            $thnInputYears = array_map('trim', explode(',', $request->input('thn_pajak_input')));
            foreach ($inputNopList as $nopItem) {
                if (empty($nopItem)) continue;
                foreach ($thnInputYears as $year) {
                    if (empty($year)) continue;
                    $pairs[] = ['nop' => $nopItem, 'tahun' => $year];
                }
            }
        } elseif ($inputType === 'upload_excel') {
            $file = $request->file('excel_file');
            try {
                $spreadsheet = IOFactory::load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                for ($row = 2; $row <= $sheet->getHighestRow(); $row++) {
                    $nopExcel = str_replace(['.', '-', ' '], '', (string)$sheet->getCell('A' . $row)->getValue());
                    $tahunExcel = (string)$sheet->getCell('B' . $row)->getValue();
                    if (!empty($nopExcel) && ctype_digit($nopExcel) && strlen($nopExcel) == 18 && !empty($tahunExcel)) {
                        $pairs[] = ['nop' => $nopExcel, 'tahun' => $tahunExcel];
                    }
                }
            } catch (ReaderException $e) { /* ... */ }
        } elseif ($inputType === 'satu_desa') {
            $spptRecords = Sppt::on('oracle')
                ->select('kd_propinsi', 'kd_dati2', 'kd_kecamatan', 'kd_kelurahan', 'kd_blok', 'no_urut', 'kd_jns_op')
                ->where('kd_propinsi', '35')->where('kd_dati2', '18')
                ->where('kd_kecamatan', $request->input('kd_kecamatan_desa'))->where('kd_kelurahan', $request->input('kd_kelurahan_desa'))
                ->distinct()->get();
            $thnInputYears = array_map('trim', explode(',', $request->input('thn_pajak_input')));
            foreach ($spptRecords as $sppt) {
                $nopFull = $sppt->kd_propinsi . $sppt->kd_dati2 . $sppt->kd_kecamatan . $sppt->kd_kelurahan . $sppt->kd_blok . $sppt->no_urut . $sppt->kd_jns_op;
                foreach ($thnInputYears as $year) {
                    if (empty($year)) continue;
                    $pairs[] = ['nop' => $nopFull, 'tahun' => $year];
                }
            }
        }
        return $pairs;
    }

    private function getDendaDetails(string $nop, string $tahun) {
        $kdPropinsi = substr($nop, 0, 2); $kdDati2 = substr($nop, 2, 2);
        $kdKecamatan = substr($nop, 4, 3); $kdKelurahan = substr($nop, 7, 3);
        $kdBlok = substr($nop, 10, 3); $noUrut = substr($nop, 13, 4); $kdJnsOp = substr($nop, 17, 1);
        $spptData = Sppt::on('oracle')->where('kd_propinsi', $kdPropinsi)->where('kd_dati2', $kdDati2)->where('kd_kecamatan', $kdKecamatan)->where('kd_kelurahan', $kdKelurahan)->where('kd_blok', $kdBlok)->where('no_urut', $noUrut)->where('kd_jns_op', $kdJnsOp)->where('thn_pajak_sppt', $tahun)->first();
        if (!$spptData) {
            return ['nop' => $nop, 'thn_pajak_sppt' => $tahun, 'formatted_nop' => $this->formatNop($nop), 'status_validasi' => 'Gagal', 'message' => 'NOP/Tahun tidak ditemukan.'];
        }
        if ((int)$spptData->status_pembayaran_sppt !== 0) {
            return ['nop' => $nop, 'thn_pajak_sppt' => $tahun, 'formatted_nop' => $this->formatNop($nop), 'nm_wp_sppt' => $spptData->nm_wp_sppt, 'status_validasi' => 'Tidak Diproses', 'message' => 'NOP sudah lunas.'];
        }
        $objekPajak = DatObjekPajak::on('oracle')->where('kd_propinsi', $kdPropinsi)->where('kd_dati2', $kdDati2)->where('kd_kecamatan', $kdKecamatan)->where('kd_kelurahan', $kdKelurahan)->where('kd_blok', $kdBlok)->where('no_urut', $noUrut)->where('kd_jns_op', $kdJnsOp)->first();
        $nmKelurahanOp = RefKelurahan::on('oracle')->where('kd_kecamatan', $kdKecamatan)->where('kd_kelurahan', $kdKelurahan)->value('nm_kelurahan');
        $nmKecamatanOp = RefKecamatan::on('oracle')->where('kd_kecamatan', $kdKecamatan)->value('nm_kecamatan');
        $alamatWp = trim(($spptData->jln_wp_sppt ?? '') . ' RT. ' . ($spptData->rt_wp_sppt ?? '') . ' RW. ' . ($spptData->rw_wp_sppt ?? '') . ' Kel/Desa. ' . ($spptData->kelurahan_wp_sppt ?? '') . ' Kab/Kota. ' . ($spptData->kota_wp_sppt ?? '-'));
        $letakOp = trim(($objekPajak->jalan_op ?? '') . ' RT. ' . ($objekPajak->rt_op ?? '') . ' RW. ' . ($objekPajak->rw_op ?? '') . ' Kel/Desa. ' . ($nmKelurahanOp ?? '') . ' Kec. ' . ($nmKecamatanOp ?? '') . ' Kab/Kota. Nganjuk');
        $pokok = ceil((float)($spptData->pbb_yg_harus_dibayar_sppt ?? 0.0));
        $tglJatuhTempoSpptAsli = Carbon::parse($spptData->tgl_jatuh_tempo_sppt ?? now());
        $denda = ceil($this->calculateDenda($pokok, (int)$tahun, $tglJatuhTempoSpptAsli));
        $jumlahPajak = $pokok + $denda;
        return [
            'kd_propinsi' => $kdPropinsi, 'kd_dati2' => $kdDati2, 'kd_kecamatan' => $kdKecamatan, 'kd_kelurahan' => $kdKelurahan,
            'kd_blok' => $kdBlok, 'no_urut' => $noUrut, 'kd_jns_op' => $kdJnsOp,
            'nop' => $nop, 'formatted_nop' => $this->formatNop($nop), 'thn_pajak_sppt' => $tahun,
            'nm_wp_sppt' => $spptData->nm_wp_sppt, 'alamat_wp' => $alamatWp, 'letak_op' => $letakOp,
            'pokok' => $pokok, 'denda' => $denda, 'jumlah_pajak' => $jumlahPajak,
            'sanksi_administratif' => $denda, 'yang_harus_dibayar' => $pokok,
            'original_jatuh_tempo' => $tglJatuhTempoSpptAsli, 'tgl_jatuh_tempo_sppt_lama' => $tglJatuhTempoSpptAsli,
            'status_validasi' => 'Siap Diproses', 'message' => 'Data valid dan siap diproses.'
        ];
    }
    
    private function calculateDenda(float $pokok, int $tahun, Carbon $tglJatuhTempo) {
        $denda = 0.0; $today = Carbon::today();
        if ($today->lessThanOrEqualTo($tglJatuhTempo)) return $denda;
        $diffInMonths = $tglJatuhTempo->diffInMonths($today);
        if ($today->day > $tglJatuhTempo->day) $diffInMonths++;
        if ($diffInMonths > 0) {
            $persenDendaPerBulan = ($tahun <= 2023) ? 0.02 : 0.01;
            $denda = $pokok * $persenDendaPerBulan * $diffInMonths;
        }
        $maxDendaPercentage = ($tahun <= 2023) ? 0.30 : 0.15;
        $maxDendaAmount = $pokok * $maxDendaPercentage;
        return ($denda > $maxDendaAmount) ? $maxDendaAmount : $denda;
    }
}
