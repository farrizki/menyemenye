<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\LogDafnom;
use App\Models\DafnomOp;
use Throwable;

class ProcessDafnom implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $logId;
    public $timeout = 3600; // 1 jam

    /**
     * Terima hanya ID dari log untuk stabilitas.
     */
    public function __construct(int $logId)
    {
        $this->logId = $logId;
    }

    /**
     * Jalankan job.
     */
    public function handle()
    {
        $log = LogDafnom::findOrFail($this->logId);
        $log->update(['status' => 'processing', 'message' => 'Memulai proses...']);

        try {
            $tahun = $log->tahun_pembentukan;
            $metode = $log->metode;
            
            $oracleConnection = DB::connection('oracle');
            $oracleConnection->beginTransaction();

            if ($metode === 'ulang') {
                Log::info("Log ID {$log->id}: Metode 'Ulang', menghapus data lama...");
                $deleteQuery = $oracleConnection->table('DAFNOM_OP')->where('THN_PEMBENTUKAN', $tahun);
                if ($log->kd_kecamatan) $deleteQuery->where('KD_KECAMATAN', $log->kd_kecamatan);
                if ($log->kd_kelurahan) $deleteQuery->where('KD_KELURAHAN', $log->kd_kelurahan);
                $deletedRows = $deleteQuery->delete();
                Log::info("Log ID {$log->id}: Selesai menghapus {$deletedRows} data.");
            }

            $baseQuery = $oracleConnection->table('SPPT')
                ->select(
                    'SPPT.KD_PROPINSI', 'SPPT.KD_DATI2', 'SPPT.KD_KECAMATAN', 'SPPT.KD_KELURAHAN', 
                    'SPPT.KD_BLOK', 'SPPT.NO_URUT', 'SPPT.KD_JNS_OP',
                    'DAT_OBJEK_PAJAK.JALAN_OP', 'DAT_OBJEK_PAJAK.BLOK_KAV_NO_OP', 
                    'DAT_OBJEK_PAJAK.RW_OP', 'DAT_OBJEK_PAJAK.RT_OP',
                    'DAT_OBJEK_PAJAK.JNS_TRANSAKSI_OP as JNS_BUMI',
                    'DAT_OP_BANGUNAN.KD_JPB',
                    'DAT_OBJEK_PAJAK.KD_STATUS_WP',
                    DB::raw("'4' as KATEGORI_OP"),
                    DB::raw("'' as KETERANGAN"),
                    DB::raw("'" . ($log->no_formulir ?? '') . "' as NO_FORMULIR"),
                    DB::raw("SYSDATE as TGL_PEMBENTUKAN"),
                    DB::raw("'" . $log->user_nip . "' as NIP_PEMBENTUK"),
                    DB::raw("SYSDATE as TGL_PEMUTAKHIRAN"),
                    DB::raw("'" . $log->user_nip . "' as NIP_PEMUTAKHIR"),
                    DB::raw($tahun . " as THN_PEMBENTUKAN")
                )
                ->leftJoin('DAT_OBJEK_PAJAK', function ($join) {
                    $join->on('SPPT.KD_PROPINSI', '=', 'DAT_OBJEK_PAJAK.KD_PROPINSI')
                        ->on('SPPT.KD_DATI2', '=', 'DAT_OBJEK_PAJAK.KD_DATI2')
                        ->on('SPPT.KD_KECAMATAN', '=', 'DAT_OBJEK_PAJAK.KD_KECAMATAN')
                        ->on('SPPT.KD_KELURAHAN', '=', 'DAT_OBJEK_PAJAK.KD_KELURAHAN')
                        ->on('SPPT.KD_BLOK', '=', 'DAT_OBJEK_PAJAK.KD_BLOK')
                        ->on('SPPT.NO_URUT', '=', 'DAT_OBJEK_PAJAK.NO_URUT')
                        ->on('SPPT.KD_JNS_OP', '=', 'DAT_OBJEK_PAJAK.KD_JNS_OP');
                })
                ->leftJoin('DAT_OP_BANGUNAN', function ($join) {
                    $join->on('SPPT.KD_PROPINSI', '=', 'DAT_OP_BANGUNAN.KD_PROPINSI')
                        ->on('SPPT.KD_DATI2', '=', 'DAT_OP_BANGUNAN.KD_DATI2')
                        ->on('SPPT.KD_KECAMATAN', '=', 'DAT_OP_BANGUNAN.KD_KECAMATAN')
                        ->on('SPPT.KD_KELURAHAN', '=', 'DAT_OP_BANGUNAN.KD_KELURAHAN')
                        ->on('SPPT.KD_BLOK', '=', 'DAT_OP_BANGUNAN.KD_BLOK')
                        ->on('SPPT.NO_URUT', '=', 'DAT_OP_BANGUNAN.NO_URUT')
                        ->on('SPPT.KD_JNS_OP', '=', 'DAT_OP_BANGUNAN.KD_JNS_OP')
                        ->where('DAT_OP_BANGUNAN.NO_BNG', '=', '1');
                })
                ->where('SPPT.THN_PAJAK_SPPT', $tahun);
            
            // <<< TAMBAHAN: Filter berdasarkan status pembayaran SPPT >>>
            $baseQuery->whereIn('SPPT.STATUS_PEMBAYARAN_SPPT', [0, 1]);

            if ($log->kd_kecamatan) $baseQuery->where('SPPT.KD_KECAMATAN', $log->kd_kecamatan);
            if ($log->kd_kelurahan) $baseQuery->where('SPPT.KD_KELURAHAN', $log->kd_kelurahan);

            if ($metode === 'susulan') {
                $baseQuery->whereNotExists(function ($query) use ($tahun) {
                    $query->select(DB::raw(1))->from('DAFNOM_OP')
                        ->where('THN_PEMBENTUKAN', $tahun)
                        ->whereRaw('DAFNOM_OP.KD_PROPINSI = SPPT.KD_PROPINSI')
                        ->whereRaw('DAFNOM_OP.KD_DATI2 = SPPT.KD_DATI2')
                        ->whereRaw('DAFNOM_OP.KD_KECAMATAN = SPPT.KD_KECAMATAN')
                        ->whereRaw('DAFNOM_OP.KD_KELURAHAN = SPPT.KD_KELURAHAN')
                        ->whereRaw('DAFNOM_OP.KD_BLOK = SPPT.KD_BLOK')
                        ->whereRaw('DAFNOM_OP.NO_URUT = SPPT.NO_URUT')
                        ->whereRaw('DAFNOM_OP.KD_JNS_OP = SPPT.KD_JNS_OP');
                });
            }
            
            $inserted = $oracleConnection->table('DAFNOM_OP')->insertUsing([
                'KD_PROPINSI', 'KD_DATI2', 'KD_KECAMATAN', 'KD_KELURAHAN', 'KD_BLOK', 'NO_URUT', 'KD_JNS_OP',
                'JALAN_OP', 'BLOK_KAV_NO_OP', 'RW_OP', 'RT_OP', 'JNS_BUMI', 'KD_JPB', 'KD_STATUS_WP',
                'KATEGORI_OP', 'KETERANGAN', 'NO_FORMULIR', 'TGL_PEMBENTUKAN', 'NIP_PEMBENTUK',
                'TGL_PEMUTAKHIRAN', 'NIP_PEMUTAKHIR', 'THN_PEMBENTUKAN'
            ], $baseQuery);

            $oracleConnection->commit();
            
            $log->update(['status' => 'success', 'message' => "Proses berhasil. {$inserted} data telah dimasukkan."]);
            Log::info("Log ID {$log->id}: Sukses, {$inserted} data dimasukkan.");

        } catch (Throwable $e) {
            $oracleConnection->rollBack();
            Log::error("Log ID {$log->id}: Gagal. Error: {$e->getMessage()}");
            $log->update(['status' => 'failed', 'message' => Str::limit($e->getMessage(), 1000)]);
            throw $e;
        }
    }
}
