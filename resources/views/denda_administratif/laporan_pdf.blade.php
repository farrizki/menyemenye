<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penghapusan Denda Administratif</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            margin: 20px;
            font-size: 9px; /* Ukuran font dasar */
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 0;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 8px; /* Ukuran font lebih kecil untuk tabel PDF */
            table-layout: fixed; /* Layout tabel fixed */
        }
        th, td {
            border: 1px solid black;
            padding: 4px; /* Kurangi padding */
            text-align: left;
            word-wrap: break-word; /* Teks panjang bisa pecah baris */
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        /* Atur lebar kolom secara proporsional (total 15 kolom) */
        th:nth-child(1), td:nth-child(1) { width: 9%; } /* NOP */
        th:nth-child(2), td:nth-child(2) { width: 4%; }  /* Tahun Pajak */
        th:nth-child(3), td:nth-child(3) { width: 7%; }  /* Nama WP */
        th:nth-child(4), td:nth-child(4) { width: 10%; } /* Alamat WP */
        th:nth-child(5), td:nth-child(5) { width: 10%; } /* Letak OP */
        th:nth-child(6), td:nth-child(6) { width: 5%; }  /* Pokok */
        th:nth-child(7), td:nth-child(7) { width: 5%; }  /* Denda */
        th:nth-child(8), td:nth-child(8) { width: 5.5%; }  /* Jumlah Pajak */
        th:nth-child(9), td:nth-child(9) { width: 5.5%; } /* Sanksi Administratif */
        th:nth-child(10), td:nth-child(10) { width: 5%; } /* Yg Harus Dibayar */
        th:nth-child(11), td:nth-child(11) { width: 8%; } /* No SK */
        th:nth-child(12), td:nth-child(12) { width: 5%; } /* Tgl SK */
        th:nth-child(13), td:nth-child(13) { width: 6%; } /* Tgl JTT Baru */
        th:nth-child(14), td:nth-child(14) { width: 5%; } /* Tgl Proses */
        /* Total width 100% */

        .footer {
            text-align: right;
            margin-top: 20px;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Penghapusan Denda Administratif</h1>
        <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d-m-Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>NOP</th>
                <th>Tahun Pajak</th>
                <th>Nama WP</th>
                <th>Alamat WP</th>
                <th>Letak OP</th>
                <th>Pokok</th>
                <th>Denda</th>
                <th>Jumlah Pajak</th>
                <th>Sanksi Administratif</th>
                <th>Yang Harus Dibayar</th>
                <th>No SK</th>
                <th>Tgl SK</th>
                <th>Tgl Jatuh Tempo Baru</th>
                <th>Tgl Proses</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($dataLaporan as $data)
                <tr>
                    <td>{{ $data->formatted_nop }}</td>
                    <td>{{ $data->thn_pajak_sppt }}</td>
                    <td>{{ $data->nm_wp_sppt ?? '-' }}</td>
                    <td>{{ $data->alamat_wp ?? '-' }}</td>
                    <td>{{ $data->letak_op ?? '-' }}</td>
                    <td>{{ number_format($data->pokok ?? 0, 2, ',', '.') }}</td>
                    <td>{{ number_format($data->denda ?? 0, 2, ',', '.') }}</td>
                    <td>{{ number_format($data->jumlah_pajak ?? 0, 2, ',', '.') }}</td>
                    <td>{{ number_format($data->sanksi_administratif ?? 0, 2, ',', '.') }}</td>
                    <td>{{ number_format($data->yang_harus_dibayar ?? 0, 2, ',', '.') }}</td>
                    <td>{{ $data->no_sk ?? '-' }}</td>
                    <td>{{ $data->tgl_sk ? \Carbon\Carbon::parse($data->tgl_sk)->format('d-m-Y') : '-' }}</td>
                    <td>{{ $data->tgl_jatuh_tempo_baru ? \Carbon\Carbon::parse($data->tgl_jatuh_tempo_baru)->format('d-m-Y') : '-' }}</td>
                    <td>{{ $data->created_at->format('d-m-Y H:i:s') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="14" style="text-align: center;">Tidak ada data denda administratif yang tercatat.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dibuat oleh: {{ Auth::user()->name ?? 'Administrator' }}</p>
    </div>
</body>
</html>