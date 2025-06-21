<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pengurangan SPPT</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            margin: 20px;
            font-size: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
        }
        .header p {
            margin: 0;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 9px;
            table-layout: fixed;
        }
        th, td {
            border: 1px solid black;
            padding: 5px;
            text-align: left;
            word-wrap: break-word;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        /* Atur lebar kolom secara proporsional */
        th:nth-child(1), td:nth-child(1) { width: 10%; } /* NOP */
        th:nth-child(2), td:nth-child(2) { width: 4%; }  /* Tahun Pajak */
        th:nth-child(3), td:nth-child(3) { width: 8%; }  /* Nama WP */
        th:nth-child(4), td:nth-child(4) { width: 12%; } /* Alamat WP */
        th:nth-child(5), td:nth-child(5) { width: 12%; } /* Letak OP */
        th:nth-child(6), td:nth-child(6) { width: 4%; }  /* Bumi */
        th:nth-child(7), td:nth-child(7) { width: 4%; }  /* Bangunan */
        th:nth-child(8), td:nth-child(8) { width: 6%; }  /* Baku */
        th:nth-child(9), td:nth-child(9) { width: 5%; }  /* Pengurangan (%) */
        th:nth-child(10), td:nth-child(10) { width: 6%; } /* Jumlah Pengurangan */
        th:nth-child(11), td:nth-child(11) { width: 7%; } /* Ketetapan */
        th:nth-child(12), td:nth-child(12) { width: 8%; } /* No SK */
        th:nth-child(13), td:nth-child(13) { width: 5%; } /* Tgl SK */
        th:nth-child(14), td:nth-child(14) { width: 9%; } /* Tgl Proses */

        .footer {
            text-align: right;
            margin-top: 20px;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Pengurangan SPPT</h1>
        <p>Tanggal Cetak: <?php echo e(\Carbon\Carbon::now()->format('d-m-Y H:i:s')); ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>NOP</th>
                <th>Tahun Pajak</th>
                <th>Nama WP</th>
                <th>Alamat WP</th>
                <th>Letak OP</th>
                <th>Bumi</th>
                <th>Bangunan</th>
                <th>Baku</th>
                <th>Pengurangan (%)</th>
                <th>Jumlah Pengurangan</th>
                <th>Ketetapan Yang Harus Dibayar</th>
                <th>No SK</th>
                <th>Tgl SK</th>
                <th>Tgl Proses</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $dataLaporan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        
                        <?php echo e($data->formatted_nop); ?>

                    </td>
                    <td><?php echo e($data->thn_pajak_sppt); ?></td>
                    <td><?php echo e($data->nm_wp_sppt ?? '-'); ?></td>
                    <td><?php echo e($data->alamat_wp ?? '-'); ?></td>
                    <td><?php echo e($data->letak_op ?? '-'); ?></td>
                    <td><?php echo e(number_format($data->luas_bumi_sppt ?? 0, 0, ',', '.')); ?></td>
                    <td><?php echo e(number_format($data->luas_bng_sppt ?? 0, 0, ',', '.')); ?></td>
                    <td><?php echo e(number_format($data->pbb_terhutang_sppt_lama ?? 0, 2, ',', '.')); ?></td>
                    <td><?php echo e(number_format($data->persentase ?? 0, 2, ',', '.')); ?>%</td>
                    <td><?php echo e(number_format($data->jumlah_pengurangan_baru ?? 0, 2, ',', '.')); ?></td>
                    <td><?php echo e(number_format($data->ketetapan_baru ?? 0, 2, ',', '.')); ?></td>
                    <td><?php echo e($data->no_sk_pengurangan ?? '-'); ?></td>
                    <td><?php echo e($data->tgl_sk_pengurangan ? \Carbon\Carbon::parse($data->tgl_sk_pengurangan)->format('d-m-Y') : '-'); ?></td>
                    <td><?php echo e($data->created_at->format('d-m-Y H:i:s')); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="14" style="text-align: center;">Tidak ada data pengurangan yang tercatat.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Dibuat oleh: <?php echo e(Auth::user()->name ?? 'Administrator'); ?></p>
    </div>
</body>
</html><?php /**PATH D:\xampp\htdocs\aplikasi-final-sppt\resources\views/pengurangan/laporan_pdf.blade.php ENDPATH**/ ?>