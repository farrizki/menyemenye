<?php

namespace App\Exports;

use App\Models\Pembatalan;
// PASTIKAN TIGA BARIS 'use' DI BAWAH INI ADA DAN TEPAT
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class PembatalanLaporanExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'NOP', 'Tahun Pajak', 'Nama WP', 'Alamat WP', 'Letak OP',
            'Bumi (m²)', 'Bangunan (m²)', 'PBB Baku', 'No. SK', 'Tanggal SK',
            'Tgl Proses', 'Operator',
        ];
    }

    public function map($pembatalan): array
    {
        return [
            $pembatalan->formatted_nop,
            $pembatalan->thn_pajak_sppt,
            $pembatalan->nm_wp_sppt,
            $pembatalan->alamat_wp,
            $pembatalan->letak_op,
            number_format($pembatalan->luas_bumi_sppt ?? 0, 0, ',', '.'),
            number_format($pembatalan->luas_bng_sppt ?? 0, 0, ',', '.'),
            $pembatalan->pbb_yg_harus_dibayar_sppt,
            $pembatalan->no_sk,
            Carbon::parse($pembatalan->tgl_sk)->format('d-m-Y'),
            $pembatalan->created_at->format('d-m-Y H:i:s'),
            $pembatalan->operator,
        ];
    }
}