<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NOP</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama WP</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PBB Terhutang</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Operator</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Proses</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php $__empty_1 = true; $__currentLoopData = $laporan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo e($item->formatted_nop); ?></td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($item->thn_pajak_sppt); ?></td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($item->nm_wp_sppt); ?></td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-right">Rp <?php echo e(number_format($item->pbb_terhutang_sppt, 0, ',', '.')); ?></td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($item->keterangan_penggabungan); ?></td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            <?php if($item->status_proses == 'Gagal'): ?> bg-red-100 text-red-800 <?php endif; ?>
                            <?php if($item->status_proses == 'Lunas'): ?> bg-yellow-100 text-yellow-800 <?php endif; ?>
                            <?php if($item->status_proses == 'Siap Diproses'): ?> bg-blue-100 text-blue-800 <?php endif; ?>
                        "><?php echo e($item->status_proses); ?></span>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($item->operator); ?></td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($item->created_at->format('d-m-Y H:i')); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="8" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada data ditemukan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<div class="mt-4">
    <?php echo e($laporan->links()); ?>

</div><?php /**PATH D:\xampp\htdocs\aplikasi-final-sppt\resources\views/penggabungan/partials/laporan_table.blade.php ENDPATH**/ ?>