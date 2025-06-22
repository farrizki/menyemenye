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
            <?php echo e(__('Pratinjau Pengurangan SPPT')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold mb-4">Konfirmasi Pengurangan SPPT</h3>

                    <?php if(empty($dataToProcess) || collect($dataToProcess)->every(fn($item) => ($item['status_validasi'] ?? 'N/A') !== 'Siap Diproses')): ?>
                        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4" role="alert">
                            Tidak ada data SPPT yang valid untuk diproses. Silakan kembali dan periksa input Anda.
                        </div>
                        <div class="mt-4">
                            <a href="<?php echo e(route('pengurangan.create')); ?>" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <?php echo e(__('Kembali ke Formulir')); ?>

                            </a>
                        </div>
                    <?php else: ?>
                        <p class="mb-4 text-sm text-gray-600">Berikut adalah data SPPT yang akan diupdate untuk **Tahun Pajak <?php echo e($thnUpdateOracle); ?>**:</p>

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
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Baku</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengurangan (%)</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Pengurangan</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ketetapan Yang Harus Dibayar</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pesan</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php $__currentLoopData = $dataToProcess; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="<?php echo e(($data['status_validasi'] ?? 'N/A') == 'Siap Diproses' ? 'bg-blue-50' : (($data['status_validasi'] ?? 'N/A') == 'Gagal' || ($data['status_validasi'] ?? 'N/A') == 'Error' ? 'bg-red-50' : 'bg-yellow-50')); ?>">
                                            <td class="px-3 py-4 whitespace-nowrap text-sm"><?php echo e($data['formatted_nop'] ?? $data['nop'] ?? '-'); ?></td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm"><?php echo e($data['thn_pajak_sppt'] ?? '-'); ?></td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm"><?php echo e($data['nm_wp_sppt'] ?? '-'); ?></td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm"><?php echo e($data['alamat_wp'] ?? '-'); ?></td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm"><?php echo e($data['letak_op'] ?? '-'); ?></td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm text-right"><?php echo e(number_format($data['luas_bumi_sppt'] ?? 0, 0, ',', '.')); ?></td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm text-right"><?php echo e(number_format($data['luas_bng_sppt'] ?? 0, 0, ',', '.')); ?></td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm text-right">Rp <?php echo e(number_format($data['pbb_terhutang_sppt_lama'] ?? 0, 0, ',', '.')); ?></td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm text-right"><?php echo e(number_format($data['persentase'] ?? 0, 0, ',', '.')); ?>%</td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm text-right">Rp <?php echo e(number_format($data['jumlah_pengurangan_baru'] ?? 0, 0, ',', '.')); ?></td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm text-right font-bold">Rp <?php echo e(number_format($data['ketetapan_baru'] ?? 0, 0, ',', '.')); ?></td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm">
                                                 <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    <?php if($data['status_validasi'] == 'Gagal' || $data['status_validasi'] == 'Error'): ?> bg-red-100 text-red-800 <?php endif; ?>
                                                    <?php if($data['status_validasi'] == 'Tidak Diproses'): ?> bg-yellow-100 text-yellow-800 <?php endif; ?>
                                                    <?php if($data['status_validasi'] == 'Siap Diproses'): ?> bg-blue-100 text-blue-800 <?php endif; ?>
                                                ">
                                                    <?php echo e($data['status_validasi'] ?? 'N/A'); ?>

                                                </span>
                                            </td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo e($data['message'] ?? 'N/A'); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        <?php
                            $jumlahNopSiapDiproses = collect($dataToProcess)->where('status_validasi', 'Siap Diproses')->count();
                        ?>

                        <div class="mt-6 border-t pt-4">
                            <p class="mb-2 font-semibold text-gray-800">Jumlah NOP yang akan diproses: <strong><?php echo e($jumlahNopSiapDiproses); ?></strong></p>
                            <p>Nomor SK Pengurangan: <strong><?php echo e($noSkPengurangan ?? '-'); ?></strong></p>
                            <p class="mb-4">Tanggal SK Pengurangan: <strong><?php echo e($tglSkPengurangan ? \Carbon\Carbon::parse($tglSkPengurangan)->format('d-m-Y') : '-'); ?></strong></p>
                        </div>

                        <div class="flex justify-end mt-4">
                            <form action="<?php echo e(route('pengurangan.confirm')); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <a href="<?php echo e(route('pengurangan.create')); ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 mr-2">
                                    <?php echo e(__('Batal')); ?>

                                </a>

                                <?php if($jumlahNopSiapDiproses > 0): ?>
                                    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.primary-button','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('primary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                                        <?php echo e(__('Konfirmasi & Simpan')); ?>

                                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                                <?php else: ?>
                                    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.primary-button','data' => ['disabled' => true,'title' => 'Tidak ada data yang siap disimpan']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('primary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['disabled' => true,'title' => 'Tidak ada data yang siap disimpan']); ?>
                                        <?php echo e(__('Konfirmasi & Simpan')); ?>

                                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                                <?php endif; ?>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da)): ?>
<?php $component = $__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da; ?>
<?php unset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da); ?>
<?php endif; ?><?php /**PATH D:\xampp\htdocs\aplikasi-final-sppt\resources\views/pengurangan/preview.blade.php ENDPATH**/ ?>