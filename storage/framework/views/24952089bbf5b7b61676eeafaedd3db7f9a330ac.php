<?php if (isset($component)) { $__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da = $component; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AppLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?php echo e(__('Laporan Pembatalan SPPT')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="p-6 bg-white border-b border-gray-200">
        <?php if(session('success')): ?>
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="alert">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Riwayat Pembatalan</h3>
             <a href="#" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500">
                Cetak Laporan
             </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">NOP</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tahun</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama WP</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Baku</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No SK</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl Proses</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Operator</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Berkas</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $laporanPembatalan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-4 py-4 whitespace-nowrap text-sm"><?php echo e($item->kd_propinsi.'.'.$item->kd_dati2.'.'.$item->kd_kecamatan.'.'.$item->kd_kelurahan.'.'.$item->kd_blok.'.'.$item->no_urut.'.'.$item->kd_jns_op); ?></td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm"><?php echo e($item->thn_pajak_sppt); ?></td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm"><?php echo e($item->nm_wp_sppt); ?></td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-right"><?php echo e(number_format($item->pbb_terhutang_sppt, 0, ',', '.')); ?></td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm"><?php echo e($item->no_sk_pembatalan); ?></td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm"><?php echo e($item->created_at->format('d-m-Y H:i')); ?></td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm"><?php echo e($item->operator); ?></td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-center">
                                <?php if($item->berkas_path): ?>
                                    <a href="<?php echo e(Storage::url($item->berkas_path)); ?>" target="_blank" title="Lihat Berkas">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500 hover:text-blue-700 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-center">
                                <a href="<?php echo e(route('pembatalan.edit', $item->id)); ?>" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                                <a href="<?php echo e(route('pembatalan.cetakSinglePdf', $item->id)); ?>" class="text-blue-600 hover:text-blue-900 ml-2">Cetak</a>
                                <form action="<?php echo e(route('pembatalan.destroy', $item->id)); ?>" method="POST" class="inline-block ml-2" onsubmit="return confirm('Anda yakin ingin menghapus data pembatalan ini? Tindakan ini akan mencoba mengembalikan data SPPT di Oracle.');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="9" class="px-6 py-4 text-center text-gray-500">Tidak ada data laporan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="mt-4"><?php echo e($laporanPembatalan->links()); ?></div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da)): ?>
<?php $component = $__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da; ?>
<?php unset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da); ?>
<?php endif; ?>
<?php /**PATH D:\xampp\htdocs\aplikasi-final-sppt\resources\views/pembatalan/laporan.blade.php ENDPATH**/ ?>