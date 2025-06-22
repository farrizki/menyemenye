<!DOCTYPE html>
<html>
<head>
    <style>
    body {
        font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        margin: 20px;
        font-size: 9px;
    }
    .header { text-align: center; margin-bottom: 20px; }
    .header h1 { margin: 0; font-size: 18px; }
    .header p { margin: 0; font-size: 11px; }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        font-size: 7px;
        table-layout: fixed;
    }
    th, td { border: 1px solid black; padding: 3px; text-align: left; word-wrap: break-word; }
    th { background-color: #f2f2f2; font-weight: bold; }
    .footer { text-align: right; margin-top: 20px; font-size: 10px; }
</style>
</head>
<body>
    <div class="header">
        <h1>Laporan Pengurangan SPPT</h1>
        <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d-m-Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 9%;">NOP</th>
                <th style="width: 4%;">Tahun</th>
                <th style="width: 7%;">Nama WP</th>
                <th style="width: 10%;">Alamat WP</th>
                <th style="width: 10%;">Letak OP</th>
                <th style="width: 4%;">Bumi</th>
                <th style="width: 4%;">Bangunan</th>
                <th style="width: 5%;">Baku</th>
                <th style="width: 7.5%;">Jenis Pengurangan</th>
                <th style="width: 5%;">Pengurangan (%)</th>
                <th style="width: 5.5%;">Jml Pengurangan</th>
                <th style="width: 6%;">Ketetapan</th>
                <th style="width: 7%;">Nomor SK</th>
                <th style="width: 5%;">Tanggal SK</th>
                <th style="width: 6%;">Tanggal Proses</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($dataLaporan as $data)
                <tr>
                    <td>{{ $data->formatted_nop ?? ($data->kd_propinsi . '.' . $data->kd_dati2 . '.' . $data->kd_kecamatan . '.' . $data->kd_kelurahan . '.' . $data->kd_blok . '-' . $data->no_urut . '.' . $data->kd_jns_op) }}</td>
                    <td>{{ $data->thn_pajak_sppt }}</td>
                    <td>{{ $data->nm_wp_sppt ?? '-' }}</td>
                    <td>{{ $data->alamat_wp ?? '-' }}</td>
                    <td>{{ $data->letak_op ?? '-' }}</td>
                    <td>{{ number_format($data->luas_bumi_sppt ?? 0, 0, ',', '.') }}</td>
                    <td>{{ number_format($data->luas_bng_sppt ?? 0, 0, ',', '.') }}</td>
                    <td>{{ number_format($data->pbb_terhutang_sppt_lama ?? 0, 0, ',', '.') }}</td>
                    <td>{{ $data->jenis_pengurangan ?? '-' }}</td>
                    <td>{{ number_format($data->persentase ?? 0, 2, ',', '.') }}%</td>
                    <td>{{ number_format($data->jumlah_pengurangan_baru ?? 0, 0, ',', '.') }}</td>
                    <td>{{ number_format($data->ketetapan_baru ?? 0, 0, ',', '.') }}</td>
                    <td>{{ $data->no_sk_pengurangan ?? '-' }}</td>
                    <td>{{ $data->tgl_sk_pengurangan ? \Carbon\Carbon::parse($data->tgl_sk_pengurangan)->format('d-m-Y') : '-' }}</td>
                    <td>{{ $data->created_at->format('d-m-Y H:i:s') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="15" style="text-align: center;">Tidak ada data pengurangan yang tercatat.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dibuat oleh: {{ Auth::user()->name ?? 'Administrator' }}</p>
    </div>
</body>
</html>