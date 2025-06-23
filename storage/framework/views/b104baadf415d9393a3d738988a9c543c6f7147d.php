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
        <p>Tanggal Cetak: <?php echo e(\Carbon\Carbon::now()->format('d-m-Y H:i:s')); ?></p>
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
                
                <th>Tgl SK</th>
                <th>Tgl Proses</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $dataLaporan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($data->formatted_nop); ?></td>
                    <td><?php echo e($data->thn_pajak_sppt); ?></td>
                    <td><?php echo e($data->nm_wp_sppt); ?></td>
                    <td><?php echo e($data->alamat_wp); ?></td>
                    <td><?php echo e($data->letak_op); ?></td>
                    <td><?php echo e(number_format($data->luas_bumi_sppt ?? 0, 0, ',', '.')); ?></td>
                    <td><?php echo e(number_format($data->luas_bng_sppt ?? 0, 0, ',', '.')); ?></td>
                    <td><?php echo e(number_format($data->pbb_yg_harus_dibayar_sppt ?? 0, 0, ',', '.')); ?></td>
                    <td><?php echo e($data->no_sk); ?></td>
                    
                    <td><?php echo e($data->tgl_sk ? \Carbon\Carbon::parse($data->tgl_sk)->format('d-m-Y') : '-'); ?></td>
                    <td><?php echo e($data->created_at->format('d-m-Y H:i:s')); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</body>
</html><?php /**PATH D:\xampp\htdocs\aplikasi-final-sppt\resources\views/pembatalan/laporan_pdf.blade.php ENDPATH**/ ?>