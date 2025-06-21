


<div class="mb-4 text-sm text-gray-600">
    Menampilkan <?php echo e($laporanPengurangan->firstItem()); ?> - <?php echo e($laporanPengurangan->lastItem()); ?> dari total <?php echo e($laporanPengurangan->total()); ?> data.
</div>


<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NOP</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun Pajak</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama WP</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat WP</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Letak OP</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bumi</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bangunan</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Baku</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengurangan (%)</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Pengurangan</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ketetapan Yang Harus Dibayar</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Pengurangan</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No SK</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl SK</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Proses</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Operator</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Berkas</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $laporanPengurangan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($data->formatted_nop); ?></td>
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
                    <td><?php echo e($data->jenis_pengurangan ?? '-'); ?></td>
                    <td><?php echo e($data->no_sk_pengurangan ?? '-'); ?></td>
                    <td><?php echo e($data->tgl_sk_pengurangan ? \Carbon\Carbon::parse($data->tgl_sk_pengurangan)->format('d-m-Y') : '-'); ?></td>
                    <td><?php echo e($data->created_at->format('d-m-Y H:i:s')); ?></td>
                    <td><?php echo e($data->operator ?? '-'); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                        <?php if($data->berkas_path): ?>
                            <a href="<?php echo e(Storage::url($data->berkas_path)); ?>" target="_blank" class="text-blue-600 hover:text-blue-900 inline-block" title="Lihat Berkas">
                                <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-2">
                            <a href="<?php echo e(route('pengurangan.cetak-single-pdf', $data->id)); ?>" target="_blank" class="text-blue-600 hover:text-blue-900" title="Cetak PDF">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2z"></path></svg>
                            </a>
                            <a href="<?php echo e(route('pengurangan.edit', $data->id)); ?>" class="text-green-600 hover:text-green-900" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            <form action="<?php echo e(route('pengurangan.destroy', $data->id)); ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini? Tindakan ini akan mengembalikan data SPPT di Oracle ke nilai semula.');" class="inline-block">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="16" class="text-center">Tidak ada data pengurangan yang tercatat.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    
    <div class="mt-4 flex justify-between items-center">
        <div class="text-sm text-gray-600">
            Menampilkan <?php echo e($laporanPengurangan->firstItem()); ?> - <?php echo e($laporanPengurangan->lastItem()); ?> dari total <?php echo e($laporanPengurangan->total()); ?> data.
        </div>
        <div>
            <?php echo e($laporanPengurangan->appends(request()->query())->links()); ?>

        </div>
    </div>
</div><?php /**PATH D:\xampp\htdocs\aplikasi-final-sppt\resources\views/pengurangan/partials/laporan_table.blade.php ENDPATH**/ ?>