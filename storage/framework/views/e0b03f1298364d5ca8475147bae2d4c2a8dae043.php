<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penggabungan SPPT</title>
    <style>
        body { font-family: 'sans-serif'; font-size: 10px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #000; padding: 5px; text-align: left; }
        .table th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>Laporan Penggabungan SPPT</h2>
    <table class="table">
        <thead>
            <tr>
                <th>NOP</th>
                <th>Tahun</th>
                <th>Nama WP</th>
                <th>Letak OP</th>
                <th class="text-right">Bumi (m²)</th>
                <th class="text-right">Bangunan (m²)</th>
                <th class="text-right">PBB Terhutang</th>
                <th>Keterangan</th>
                <th>Operator</th>
                <th>Tgl Proses</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $dataLaporan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($item->formatted_nop); ?></td>
                    <td><?php echo e($item->thn_pajak_sppt); ?></td>
                    <td><?php echo e($item->nm_wp_sppt); ?></td>
                    <td><?php echo e($item->letak_op); ?></td>
                    <td class="text-right"><?php echo e(number_format($item->luas_bumi_sppt, 0, ',', '.')); ?></td>
                    <td class="text-right"><?php echo e(number_format($item->luas_bng_sppt, 0, ',', '.')); ?></td>
                    <td class="text-right"><?php echo e(number_format($item->pbb_terhutang_sppt, 0, ',', '.')); ?></td>
                    <td><?php echo e($item->keterangan_penggabungan); ?></td>
                    <td><?php echo e($item->operator); ?></td>
                    <td><?php echo e($item->created_at->format('d-m-Y')); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="10" class="text-center">Tidak ada data ditemukan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH D:\xampp\htdocs\aplikasi-final-sppt\resources\views/penggabungan/laporan_pdf.blade.php ENDPATH**/ ?>