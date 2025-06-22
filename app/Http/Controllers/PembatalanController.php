<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Pembatalan; // Log MySQL
use App\Models\Sppt; // Oracle
use App\Models\DafnomOp; // Oracle
use App\Models\DatObjekPajak; // Oracle
use App\Models\RefKecamatan; // Oracle
use App\Models\RefKelurahan; // Oracle
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PembatalanController extends Controller
{
    /**
     * Menampilkan form input pembatalan.
     */
    public function create()
    {
        return view('pembatalan.create');
    }

    /**
     * Menampilkan halaman laporan.
     */
    public function index(Request $request)
    {
        $query = Pembatalan::orderBy('created_at', 'desc');

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
        
        $laporan = $query->paginate(25);
        $flashResults = session('pembatalan_results', []);
        
        if ($request->ajax()) {
            return view('pembatalan.partials.laporan_table', ['laporan' => $laporan])->render();
        }

        return view('pembatalan.index', compact('laporan', 'flashResults'));
    }

    /**
     * Memvalidasi dan menampilkan data pratinjau.
     */
    public function preview(Request $request)
    {
        $request->validate([
            'input_type' => 'required|in:nop_manual,upload_excel',
            'nop_manual' => 'required_if:input_type,nop_manual|string|nullable',
            'excel_file' => 'required_if:input_type,upload_excel|file|mimes:xls,xlsx|max:24576',
            'thn_pajak_sppt' => 'required_if:input_type,nop_manual|integer|min:2000',
            'nomor_sk' => 'required|string|max:255',
            'tahun_sk' => 'required|integer|min:2000',
            'tgl_sk' => 'required|date',
            'berkas' => 'required|file|mimes:pdf|max:24576',
        ]);

        $nops = [];
        $tahunPajakPerNop = [];
        $inputType = $request->input('input_type');

        if ($inputType === 'nop_manual') {
            $nops = array_map('trim', explode(',', $request->input('nop_manual')));
            $tahunPajak = $request->input('thn_pajak_sppt');
            foreach($nops as $nop) {
                $tahunPajakPerNop[$nop] = $tahunPajak;
            }
        } elseif ($inputType === 'upload_excel') {
            $file = $request->file('excel_file');
            try {
                $spreadsheet = IOFactory::load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                for ($row = 2; $row <= $sheet->getHighestRow(); $row++) {
                    $nopExcel = str_replace(['.', '-', ' '], '', (string) $sheet->getCell('A' . $row)->getValue());
                    $tahunExcel = (string) $sheet->getCell('B' . $row)->getValue();
                    if (!empty($nopExcel) && ctype_digit($nopExcel) && strlen($nopExcel) == 18 && !empty($tahunExcel)) {
                        $nops[] = $nopExcel;
                        $tahunPajakPerNop[$nopExcel] = $tahunExcel;
                    }
                }
                $nops = array_unique($nops);
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['excel_file' => 'Gagal membaca file Excel: ' . $e->getMessage()]);
            }
        }
        
        $noSkLengkap = '100.3.3.2/' . $request->input('nomor_sk') . '/K/411.403/' . $request->input('tahun_sk');
        $keterangan = 'batal sk ' . $noSkLengkap;

        $berkasPath = $request->file('berkas')->store('temp_pembatalan', 'local');
        $request->session()->put('berkas_temp_path', $berkasPath);
        
        $dataToProcess = [];
        $tahunPembentukan = date('Y');

        foreach ($nops as $nop) {
            if (strlen($nop) !== 18) continue;
            
            $item = ['nop' => $nop, 'formatted_nop' => $this->formatNop($nop)];
            $thnPajak = $tahunPajakPerNop[$nop];
            $item['thn_pajak_sppt'] = $thnPajak;

            $dafnom = DafnomOp::on('oracle')
                ->whereRaw("kd_propinsi||kd_dati2||kd_kecamatan||kd_kelurahan||kd_blok||no_urut||kd_jns_op = ?", [$nop])
                ->where('thn_pembentukan', $tahunPembentukan)
                ->first();

            if (!$dafnom || $dafnom->kategori_op != 4) {
                $item['status'] = 'Gagal';
                $item['message'] = 'Data Daftar Nominatif tidak ditemukan/tidak valid. Buat Dafnom terlebih dahulu.';
            } else {
                $sppt = Sppt::on('oracle')
                    ->whereRaw("kd_propinsi||kd_dati2||kd_kecamatan||kd_kelurahan||kd_blok||no_urut||kd_jns_op = ?", [$nop])
                    ->where('thn_pajak_sppt', $thnPajak)
                    ->first();
                
                if ($sppt && $sppt->status_pembayaran_sppt == 1) {
                    $item['status'] = 'Lunas';
                    $item['message'] = 'SPPT sudah lunas/terbayar. Hanya Dafnom yang akan diupdate.';
                } elseif (!$sppt || $sppt->status_pembayaran_sppt != 0) {
                     $item['status'] = 'Gagal';
                     $item['message'] = 'SPPT tidak ditemukan atau status tidak memungkinkan untuk dibatalkan.';
                } else {
                    $item['status'] = 'Siap Diproses';
                    $item['message'] = 'Data valid dan siap untuk dibatalkan.';
                }

                // Ambil data untuk preview
                $item['data_preview'] = $this->getPreviewData($nop, $thnPajak);
            }
            $dataToProcess[] = $item;
        }

        $request->session()->put('pembatalan_data_preview', [
            'data' => $dataToProcess,
            'no_sk' => $noSkLengkap,
            'tgl_sk' => $request->input('tgl_sk'),
            'keterangan' => $keterangan,
        ]);

        return view('pembatalan.preview', ['preview' => session('pembatalan_data_preview')]);
    }

    /**
     * Menyimpan data pembatalan ke database.
     */
    public function store(Request $request)
    {
        $previewData = $request->session()->get('pembatalan_data_preview');
        $berkasTempPath = $request->session()->get('berkas_temp_path');

        if (!$previewData) {
            return redirect()->route('pembatalan.create')->withErrors('Sesi pratinjau tidak ditemukan. Silakan ulangi proses.');
        }

        $finalBerkasPath = null;
        if ($berkasTempPath && Storage::disk('local')->exists($berkasTempPath)) {
            $finalBerkasPath = Storage::disk('public')->putFile('berkas_pembatalan', new \Illuminate\Http\File(storage_path('app/' . $berkasTempPath)));
            Storage::disk('local')->delete($berkasTempPath);
        }

        $results = [];
        $user = Auth::user();
        $tahunPembentukan = date('Y');

        DB::connection('oracle')->beginTransaction();
        try {
            foreach ($previewData['data'] as $item) {
                if (in_array($item['status'], ['Siap Diproses', 'Lunas'])) {
                    $nop = $item['nop'];
                    
                    // Update DAFNOM_OP
                    DafnomOp::on('oracle')
                        ->whereRaw("kd_propinsi||kd_dati2||kd_kecamatan||kd_kelurahan||kd_blok||no_urut||kd_jns_op = ?", [$nop])
                        ->where('thn_pembentukan', $tahunPembentukan)
                        ->update([
                            'kategori_op' => 3,
                            'keterangan' => $previewData['keterangan'],
                            'tgl_pemutakhiran' => DB::raw('SYSDATE'),
                            'nip_pemutakhir' => $user->nip,
                        ]);

                    // Update SPPT jika statusnya 'Siap Diproses'
                    if ($item['status'] === 'Siap Diproses') {
                        Sppt::on('oracle')
                            ->whereRaw("kd_propinsi||kd_dati2||kd_kecamatan||kd_kelurahan||kd_blok||no_urut||kd_jns_op = ?", [$nop])
                            ->where('thn_pajak_sppt', $item['thn_pajak_sppt'])
                            ->where('status_pembayaran_sppt', 0)
                            ->update(['status_pembayaran_sppt' => 2]);
                    }
                    
                    $this->logPembatalan($item, $previewData, $user, $finalBerkasPath);
                    $results[] = $item;
                }
            }
            DB::connection('oracle')->commit();
        } catch (\Exception $e) {
            DB::connection('oracle')->rollBack();
            return redirect()->route('pembatalan.create')->withErrors('Terjadi kesalahan fatal saat memproses data di Oracle: ' . $e->getMessage());
        }

        $request->session()->forget(['pembatalan_data_preview', 'berkas_temp_path']);
        $request->session()->flash('pembatalan_results', $results);
        
        return redirect()->route('pembatalan.index')->with('success', 'Proses pembatalan SPPT selesai.');
    }

    // Helper Functions
    private function getPreviewData($nop, $tahun) {
        $sppt = Sppt::on('oracle')
            ->whereRaw("kd_propinsi||kd_dati2||kd_kecamatan||kd_kelurahan||kd_blok||no_urut||kd_jns_op = ?", [$nop])
            ->where('thn_pajak_sppt', $tahun)
            ->first();

        if(!$sppt) return [];

        $op = DatObjekPajak::on('oracle')
            ->where('kd_propinsi', $sppt->kd_propinsi)
            ->where('kd_dati2', $sppt->kd_dati2)
            ->where('kd_kecamatan', $sppt->kd_kecamatan)
            ->where('kd_kelurahan', $sppt->kd_kelurahan)
            ->where('kd_blok', $sppt->kd_blok)
            ->where('no_urut', $sppt->no_urut)
            ->where('kd_jns_op', $sppt->kd_jns_op)
            ->first();

        $kec = RefKecamatan::on('oracle')->where('kd_kecamatan', $sppt->kd_kecamatan)->first();
        $kel = RefKelurahan::on('oracle')->where('kd_kecamatan', $sppt->kd_kecamatan)->where('kd_kelurahan', $sppt->kd_kelurahan)->first();

        $alamatWp = trim(
            ($sppt->jln_wp_sppt ?? '') .
            ' RT. ' . ($sppt->rt_wp_sppt ?? '') .
            ' RW. ' . ($sppt->rw_wp_sppt ?? '') .
            ' Kel/Desa ' . ($sppt->kelurahan_wp_sppt ?? '') .
            ' Kab/Kota ' . ($sppt->kota_wp_sppt ?? '')
        );

        // PERUBAHAN DI SINI: Menambahkan 'Kab/Kota Nganjuk' secara paten
        $alamatOp = trim(
            ($op->jalan_op ?? '') .
            ' RT. ' . ($op->rt_op ?? '') .
            ' RW. ' . ($op->rw_op ?? '') .
            ' Kel/Desa ' . ($kel->nm_kelurahan ?? '') .
            ' Kec. ' . ($kec->nm_kecamatan ?? '') .
            ' Kab/Kota Nganjuk' // <-- Penambahan
        );

        return [
            'nm_wp_sppt' => $sppt->nm_wp_sppt,
            'alamat_wp' => $alamatWp,
            'alamat_op' => $alamatOp,
            'luas_bumi' => $sppt->luas_bumi_sppt,
            'luas_bangunan' => $sppt->luas_bng_sppt,
            'pbb_baku' => $sppt->pbb_yg_harus_dibayar_sppt,
        ];
    }
    
   private function logPembatalan($item, $previewData, $user, $berkasPath)
    {
        $nop = $item['nop'];
        Pembatalan::create([
            'kd_propinsi' => substr($nop, 0, 2),
            'kd_dati2' => substr($nop, 2, 2),
            'kd_kecamatan' => substr($nop, 4, 3),
            'kd_kelurahan' => substr($nop, 7, 3),
            'kd_blok' => substr($nop, 10, 3),
            'no_urut' => substr($nop, 13, 4),
            'kd_jns_op' => substr($nop, 17, 1),
            'thn_pajak_sppt' => $item['thn_pajak_sppt'],
            'nm_wp_sppt' => $item['data_preview']['nm_wp_sppt'] ?? null,
            'alamat_wp' => $item['data_preview']['alamat_wp'] ?? null,
            'letak_op' => $item['data_preview']['alamat_op'] ?? null,
            'pbb_yg_harus_dibayar_sppt' => $item['data_preview']['pbb_baku'] ?? 0,
            
            // PENAMBAHAN DATA BARU
            'luas_bumi_sppt' => $item['data_preview']['luas_bumi'] ?? 0,
            'luas_bng_sppt' => $item['data_preview']['luas_bangunan'] ?? 0,

            'no_sk' => $previewData['no_sk'],
            'tgl_sk' => $previewData['tgl_sk'],
            'keterangan_pembatalan' => $previewData['keterangan'],
            'berkas_path' => $berkasPath,
            'status_proses' => $item['status'],
            'pesan_proses' => $item['message'],
            'operator' => $user->name,
            'nip_operator' => $user->nip,
        ]);
    }

    private function formatNop(string $nopRaw): string
    {
        if (strlen($nopRaw) == 18) {
            return substr($nopRaw, 0, 2) . '.' . substr($nopRaw, 2, 2) . '.' . substr($nopRaw, 4, 3) . '.' . substr($nopRaw, 7, 3) . '.' . substr($nopRaw, 10, 3) . '.' . substr($nopRaw, 13, 4) . '.' . substr($nopRaw, 17, 1);
        }
        return $nopRaw;
    }

    public function edit(Pembatalan $pembatalan)
    {
        // Pecah no_sk untuk mendapatkan nomor dan tahun
        $parts = explode('/', $pembatalan->no_sk);
        $pembatalan->nomor_sk_raw = $parts[1] ?? '';
        $pembatalan->tahun_sk_raw = $parts[3] ?? '';

        return view('pembatalan.edit', compact('pembatalan'));
    }

    /**
     * Mengupdate data pembatalan (hanya log dan berkas).
     */
    public function update(Request $request, Pembatalan $pembatalan)
    {
        $request->validate([
            'nomor_sk' => 'required|string|max:255',
            'tahun_sk' => 'required|integer|min:2000',
            'tgl_sk' => 'required|date',
            'berkas' => 'nullable|file|mimes:pdf|max:24576',
        ]);

        // Rekonstruksi No SK dan Keterangan
        $noSkLengkap = '100.3.3.2/' . $request->input('nomor_sk') . '/K/411.403/' . $request->input('tahun_sk');
        $keterangan = 'batal sk ' . $noSkLengkap;

        // Handle upload berkas
        $berkasPath = $pembatalan->berkas_path;
        if ($request->hasFile('berkas')) {
            // Hapus file lama jika ada
            if ($berkasPath && Storage::disk('public')->exists($berkasPath)) {
                Storage::disk('public')->delete($berkasPath);
            }
            // Simpan file baru
            $berkasPath = $request->file('berkas')->store('berkas_pembatalan', 'public');
        }

        $pembatalan->update([
            'no_sk' => $noSkLengkap,
            'tgl_sk' => $request->input('tgl_sk'),
            'keterangan_pembatalan' => $keterangan,
            'berkas_path' => $berkasPath,
            'operator' => Auth::user()->name,
            'nip_operator' => Auth::user()->nip,
        ]);

        return redirect()->route('pembatalan.index')->with('success', 'Data log pembatalan berhasil diperbarui.');
    }

    /**
     * Menghapus data pembatalan dan mengembalikan data Oracle.
     */
    public function destroy(Pembatalan $pembatalan)
    {
        DB::connection('oracle')->beginTransaction();
        try {
            // Kembalikan data DAFNOM_OP
            DafnomOp::on('oracle')
                ->where('kd_propinsi', $pembatalan->kd_propinsi)
                ->where('kd_dati2', $pembatalan->kd_dati2)
                ->where('kd_kecamatan', $pembatalan->kd_kecamatan)
                ->where('kd_kelurahan', $pembatalan->kd_kelurahan)
                ->where('kd_blok', $pembatalan->kd_blok)
                ->where('no_urut', $pembatalan->no_urut)
                ->where('kd_jns_op', $pembatalan->kd_jns_op)
                ->where('thn_pembentukan', Carbon::parse($pembatalan->created_at)->year)
                ->update([
                    'kategori_op' => 4, // Kembali ke status semula
                    'keterangan' => null,
                    'tgl_pemutakhiran' => null,
                    'nip_pemutakhir' => null
                ]);

            // Kembalikan data SPPT jika statusnya dibatalkan (2)
            if ($pembatalan->status_proses == 'Siap Diproses') {
                 Sppt::on('oracle')
                    ->where('kd_propinsi', $pembatalan->kd_propinsi)
                    ->where('kd_dati2', $pembatalan->kd_dati2)
                    ->where('kd_kecamatan', $pembatalan->kd_kecamatan)
                    ->where('kd_kelurahan', $pembatalan->kd_kelurahan)
                    ->where('kd_blok', $pembatalan->kd_blok)
                    ->where('no_urut', $pembatalan->no_urut)
                    ->where('kd_jns_op', $pembatalan->kd_jns_op)
                    ->where('thn_pajak_sppt', $pembatalan->thn_pajak_sppt)
                    ->where('status_pembayaran_sppt', 2)
                    ->update(['status_pembayaran_sppt' => 0]); // Kembali ke belum bayar
            }

            DB::connection('oracle')->commit();

            // Hapus berkas dari storage
            if ($pembatalan->berkas_path && Storage::disk('public')->exists($pembatalan->berkas_path)) {
                Storage::disk('public')->delete($pembatalan->berkas_path);
            }

            // Hapus log dari MySQL
            $pembatalan->delete();

            return redirect()->route('pembatalan.index')->with('success', 'Data berhasil dihapus dan data Oracle telah dikembalikan.');

        } catch (\Exception $e) {
            DB::connection('oracle')->rollBack();
            return redirect()->route('pembatalan.index')->withErrors('Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Mencetak PDF untuk satu record.
     */
    public function cetakSinglePdf(Pembatalan $pembatalan)
{
    $dataLaporan = collect([$pembatalan]);

    // Tambahkan tanggal dan waktu ke nama file
    $timestamp = now()->format('Ymd_His');
    $fileName = 'pembatalan_sppt_' . str_replace('.', '', $pembatalan->formatted_nop) . '_' . $timestamp . '.pdf';

    $pdf = \PDF::loadView('pembatalan.laporan_pdf', compact('dataLaporan'))->setPaper('a4', 'landscape');
    return $pdf->download($fileName);
}
    public function showFilterCetakPdfForm()
    {
        $kecamatans = RefKecamatan::on('oracle')
            ->where('kd_propinsi', '35')
            ->where('kd_dati2', '18')
            ->orderBy('nm_kecamatan')
            ->get();

        return view('pembatalan.filter_cetak_pdf', compact('kecamatans'));
    }

    /**
     * Membuat laporan PDF berdasarkan filter tanggal.
     */
    public function cetakFilteredPdf(Request $request)
{
    $request->validate([
        'tahun_pajak' => 'nullable|integer|min:2000',
        'kd_kecamatan' => 'nullable|string',
        'no_sk' => 'nullable|string',
        'format' => 'required|in:pdf,excel',
    ]);

    $query = Pembatalan::query()->orderBy('created_at', 'asc');

    if ($request->filled('tahun_pajak')) {
        $query->where('thn_pajak_sppt', $request->input('tahun_pajak'));
    }
    if ($request->filled('kd_kecamatan')) {
        $query->where('kd_kecamatan', $request->input('kd_kecamatan'));
    }
    if ($request->filled('no_sk')) {
        $query->where('no_sk', 'LIKE', '%/' . $request->input('no_sk') . '/%');
    }

    $dataLaporan = $query->get();

    if ($dataLaporan->isEmpty()) {
        return redirect()->route('pembatalan.filter-cetak-pdf')->withErrors('Tidak ada data yang ditemukan untuk kriteria yang dipilih.');
    }

    $timestamp = now()->format('Ymd_His');
    $fileNameBase = 'laporan_pembatalan_' . $timestamp;

    if ($request->input('format') === 'pdf') {
        $pdf = \PDF::loadView('pembatalan.laporan_pdf', compact('dataLaporan'))->setPaper('a4', 'landscape');
        return $pdf->download($fileNameBase . '.pdf');
    }

    if ($request->input('format') === 'excel') {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'NOP', 'Tahun Pajak', 'Nama WP', 'Alamat WP', 'Letak OP',
            'Bumi (m²)', 'Bangunan (m²)', 'PBB Baku', 'No. SK', 'Tanggal SK',
            'Tgl Proses', 'Operator',
        ];
        $sheet->fromArray([$headers], null, 'A1');

        $rowNum = 2;
        foreach ($dataLaporan as $pembatalan) {
            $rowData = [
                $pembatalan->formatted_nop,
                $pembatalan->thn_pajak_sppt,
                $pembatalan->nm_wp_sppt,
                $pembatalan->alamat_wp,
                $pembatalan->letak_op,
                (float)($pembatalan->luas_bumi_sppt ?? 0),
                (float)($pembatalan->luas_bng_sppt ?? 0),
                (float)($pembatalan->pbb_yg_harus_dibayar_sppt ?? 0),
                $pembatalan->no_sk,
                Carbon::parse($pembatalan->tgl_sk)->format('d-m-Y'),
                $pembatalan->created_at->format('d-m-Y H:i:s'),
                $pembatalan->operator,
            ];
            $sheet->fromArray([$rowData], null, 'A' . $rowNum);
            $rowNum++;
        }

        $writer = new Xlsx($spreadsheet);
        return response()->stream(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileNameBase . '.xlsx"',
        ]);
    }
}
}