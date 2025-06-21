<?php

namespace App\Exports;

use App\Models\Pengurangan; // Model yang akan diekspor
use Maatwebsite\Excel\Concerns\FromCollection; // Trait untuk mengekspor dari koleksi
use Maatwebsite\Excel\Concerns\WithHeadings;   // Trait untuk menambahkan header
use Maatwebsite\Excel\Concerns\WithMapping;    // Trait untuk memetakan data
use Carbon\Carbon; // Untuk format tanggal

class PenguranganLaporanExport implements FromCollection, WithHeadings, WithMapping
{
    protected $dataLaporan;

    public function __construct($dataLaporan)
    {
        $this->dataLaporan = $dataLaporan;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->dataLaporan;
    }

    /**
     * PERBAIKAN: Menambahkan baris header di Excel.
     */
    public function headings(): array
    {
        return [
            'NOP',
            'Tahun Pajak',
            'Nama WP',
            'Alamat WP',
            'Letak OP',
            'Bumi',
            'Bangunan',
            'Baku',
            'Pengurangan (%)',
            'Jumlah Pengurangan',
            'Ketetapan Yang Harus Dibayar',
            'Jenis Pengurangan', // Kolom baru
            'No SK',
            'Tgl SK',
            'Tgl Proses',
            'Operator',
        ];
    }

    /**
     * PERBAIKAN: Memetakan data dari setiap item koleksi ke baris Excel.
     * Ini menentukan urutan dan format data di Excel.
     */
    public function map($data): array
    {
        // Ambil NOP mentah lalu format
        $rawNop = $data->kd_propinsi . $data->kd_dati2 . $data->kd_kecamatan . $data->kd_kelurahan . $data->kd_blok . $data->no_urut . $data->kd_jns_op;
        $formattedNop = $this->formatNop($rawNop); // Panggil helper formatNop

        return [
            $formattedNop,
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
            $data->tgl_sk_pengurangan ? Carbon::parse($data->tgl_sk_pengurangan)->format('d-m-Y') : '-',
            $data->created_at->format('d-m-Y H:i:s'),
            $data->operator ?? '-',
        ];
    }

    // PERBAIKAN: Tambahkan method formatNop di sini juga karena diimplementasikan di model
    private function formatNop(string $nopRaw): string
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