<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pembatalan SPPT</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; font-size: 8px; }
        th, td { border: 1px solid black; padding: 5px; text-align: left; word-wrap: break-word; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Pembatalan SPPT</h1>
        <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d-m-Y H:i:s') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>NOP</th>
                <th>Tahun</th>
                <th>Nama WP</th>
                <th>Alamat WP</th>
                <th>Letak OP</th>
                <th>Bumi (m²)</th>
                <th>Bangunan (m²)</th>
                <th>PBB Baku</th>
                <th>No. SK</th>
                {{-- PENAMBAHAN KOLOM --}}
                <th>Tgl SK</th>
                <th>Tgl Proses</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dataLaporan as $data)
                <tr>
                    <td>{{ $data->formatted_nop }}</td>
                    <td>{{ $data->thn_pajak_sppt }}</td>
                    <td>{{ $data->nm_wp_sppt }}</td>
                    <td>{{ $data->alamat_wp }}</td>
                    <td>{{ $data->letak_op }}</td>
                    <td>{{ number_format($data->luas_bumi_sppt ?? 0, 0, ',', '.') }}</td>
                    <td>{{ number_format($data->luas_bng_sppt ?? 0, 0, ',', '.') }}</td>
                    <td>{{ number_format($data->pbb_yg_harus_dibayar_sppt ?? 0, 0, ',', '.') }}</td>
                    <td>{{ $data->no_sk }}</td>
                    {{-- PENAMBAHAN DATA --}}
                    <td>{{ $data->tgl_sk ? \Carbon\Carbon::parse($data->tgl_sk)->format('d-m-Y') : '-' }}</td>
                    <td>{{ $data->created_at->format('d-m-Y H:i:s') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>