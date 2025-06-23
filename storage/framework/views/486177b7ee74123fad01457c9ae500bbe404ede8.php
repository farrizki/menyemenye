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
            <?php echo e(__('Pratinjau Pembatalan SPPT')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold mb-4">Konfirmasi Pembatalan SPPT</h3>

                    
                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                             <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NOP</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama WP</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat WP</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Letak OP</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bumi (m²)</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bangunan (m²)</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PBB Baku</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Pratinjau</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pesan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php $__empty_1 = true; $__currentLoopData = $preview['data']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php
                                        $rowClass = '';
                                        if ($item['status'] == 'Gagal') $rowClass = 'bg-red-50';
                                        if ($item['status'] == 'Lunas') $rowClass = 'bg-yellow-50';
                                        if ($item['status'] == 'Siap Diproses') $rowClass = 'bg-blue-50';
                                    ?>
                                    <tr class="<?php echo e($rowClass); ?>">
                                        <td class="px-3 py-4 whitespace-nowrap text-sm"><?php echo e($item['formatted_nop']); ?></td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm"><?php echo e($item['thn_pajak_sppt']); ?></td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm"><?php echo e($item['data_preview']['nm_wp_sppt'] ?? '-'); ?></td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm"><?php echo e($item['data_preview']['alamat_wp'] ?? '-'); ?></td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm"><?php echo e($item['data_preview']['alamat_op'] ?? '-'); ?></td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-right"><?php echo e(number_format($item['data_preview']['luas_bumi'] ?? 0, 0, ',', '.')); ?></td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-right"><?php echo e(number_format($item['data_preview']['luas_bangunan'] ?? 0, 0, ',', '.')); ?></td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-right font-semibold">Rp <?php echo e(number_format($item['data_preview']['pbb_baku'] ?? 0, 0, ',', '.')); ?></td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?php if($item['status'] == 'Gagal'): ?> bg-red-100 text-red-800 <?php endif; ?>
                                                <?php if($item['status'] == 'Lunas'): ?> bg-yellow-100 text-yellow-800 <?php endif; ?>
                                                <?php if($item['status'] == 'Siap Diproses'): ?> bg-blue-100 text-blue-800 <?php endif; ?>
                                            ">
                                                <?php echo e($item['status']); ?>

                                            </span>
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo e($item['message']); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr><td colspan="10" class="text-center p-4">Tidak ada data untuk diproses.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-6 border-t pt-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2 text-sm">
                            <div class="p-4 border rounded-lg bg-gray-50">
                                <p class="font-semibold">Detail SK Pembatalan:</p>
                                <ul class="list-disc list-inside ml-2 mt-1">
                                    <li><strong>Nomor SK:</strong> <?php echo e($preview['no_sk']); ?></li>
                                    <li><strong>Tanggal SK:</strong> <?php echo e(\Carbon\Carbon::parse($preview['tgl_sk'])->format('d-m-Y')); ?></li>
                                    <li><strong>Keterangan:</strong> "<?php echo e($preview['keterangan']); ?>"</li>
                                </ul>
                            </div>
                             <div class="p-4 border rounded-lg bg-blue-50 border-blue-300">
                                <?php
                                    $collection = collect($preview['data']);
                                    $siapCount = $collection->where('status', 'Siap Diproses')->count();
                                    $lunasCount = $collection->where('status', 'Lunas')->count();
                                    $gagalCount = $collection->where('status', 'Gagal')->count();
                                    $totalCount = $siapCount + $lunasCount + $gagalCount;
                                ?>
                                <p class="font-semibold">Ringkasan Proses:</p>
                                <ul class="list-disc list-inside ml-2 mt-1">
                                    <li>Total NOP yang diajukan: <strong><?php echo e($totalCount); ?></strong></li>
                                    <li>Siap Diproses: <strong><?php echo e($siapCount); ?></strong></li>
                                    <li>Sudah Lunas (Hanya Dafnom): <strong><?php echo e($lunasCount); ?></strong></li>
                                    <li>Gagal: <strong><?php echo e($gagalCount); ?></strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <a href="<?php echo e(route('pembatalan.create')); ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 mr-2">
                            Kembali
                        </a>
                        <?php if(collect($preview['data'])->contains(fn($i) => in_array($i['status'], ['Siap Diproses', 'Lunas']))): ?>
                        <form action="<?php echo e(route('pembatalan.store')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.primary-button','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('primary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                                <?php echo e(__('Konfirmasi & Simpan Pembatalan')); ?>

                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da)): ?>
<?php $component = $__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da; ?>
<?php unset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da); ?>
<?php endif; ?>
<?php /**PATH D:\xampp\htdocs\aplikasi-final-sppt\resources\views/pembatalan/preview.blade.php ENDPATH**/ ?>