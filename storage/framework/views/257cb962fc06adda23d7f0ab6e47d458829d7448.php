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
            <?php echo e(__('Pratinjau Penghapusan Denda Administratif')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold mb-4">Konfirmasi Penghapusan Denda Administratif</h3>

                    <p class="mb-4">Berikut adalah status NOP yang akan diproses untuk denda:</p>

                    <div class="overflow-x-auto mb-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NOP</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun Pajak</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama WP</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat WP</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Letak OP</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pokok</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Denda</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Pajak</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sanksi Administratif</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Yang Harus Dibayar</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Pratinjau</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pesan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
    <?php
        $displayCount = 0;
    ?>

    <?php $__currentLoopData = $dataToProcess; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $shouldDisplay = false; // Default: jangan tampilkan
            $currentStatus = $data['status_validasi'] ?? 'N/A';

            if (isset($inputType) && $inputType === 'satu_desa') {
                // Jika mode "satu_desa", hanya tampilkan yang siap diproses
                if ($currentStatus === 'Siap Diproses') {
                    $shouldDisplay = true;
                }
            } else {
                // Untuk mode manual atau excel, tampilkan semua kecuali yang statusnya 'Tidak Diproses'
                if ($currentStatus === 'Siap Diproses' || $currentStatus === 'Gagal' || $currentStatus === 'Error') {
                    $shouldDisplay = true;
                }
            }
        ?>

        <?php if($shouldDisplay): ?>
            <?php $displayCount++; ?>
            <tr class="<?php echo e($currentStatus == 'Siap Diproses' ? 'bg-blue-50' : 'bg-red-50'); ?>">
                <td class="px-6 py-4 whitespace-nowrap text-sm"><?php echo e($data['formatted_nop'] ?? $data['nop'] ?? '-'); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm"><?php echo e($data['thn_pajak_sppt'] ?? '-'); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm"><?php echo e($data['nm_wp_sppt'] ?? '-'); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm"><?php echo e($data['alamat_wp'] ?? '-'); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm"><?php echo e($data['letak_op'] ?? '-'); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-right"><?php echo e(number_format($data['pokok'] ?? 0, 2, ',', '.')); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-right"><?php echo e(number_format($data['denda'] ?? 0, 2, ',', '.')); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-right"><?php echo e(number_format($data['jumlah_pajak'] ?? 0, 2, ',', '.')); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-right"><?php echo e(number_format($data['sanksi_administratif'] ?? 0, 2, ',', '.')); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-right"><?php echo e(number_format($data['yang_harus_dibayar'] ?? 0, 2, ',', '.')); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm"><?php echo e($data['status_validasi'] ?? 'N/A'); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm"><?php echo e($data['message'] ?? 'N/A'); ?></td>
            </tr>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php if($displayCount === 0): ?>
        <tr>
            <td colspan="12" class="text-center py-4 px-6 text-gray-500">
                Tidak ada data yang siap diproses untuk kriteria yang dipilih.
            </td>
        </tr>
    <?php endif; ?>
</tbody>
                        </table>
                        
<?php
    $jumlahNopSiapDiproses = collect($dataToProcess)
                                ->where('status_validasi', 'Siap Diproses')
                                ->count();
?>


<div class="mt-4 border-t pt-4">
    <p class="mb-2 font-semibold text-gray-800">Jumlah NOP yang akan diproses: <strong><?php echo e($jumlahNopSiapDiproses); ?></strong></p>
    <p class="mb-2">Tanggal Jatuh Tempo Baru akan diubah menjadi: <strong><?php echo e($tglJatuhTempoBaru ? \Carbon\Carbon::parse($tglJatuhTempoBaru)->format('d-m-Y') : '-'); ?></strong></p>
    <p class="mb-2">Nomor SK: <strong><?php echo e($noSkLengkap ?? '-'); ?></strong></p>
    <p class="mb-4">Tanggal SK: <strong><?php echo e($tglSk ? \Carbon\Carbon::parse($tglSk)->format('d-m-Y') : '-'); ?></strong></p>
</div>


<div class="flex justify-end mt-4">
    <a href="<?php echo e(route('denda_administratif.create')); ?>" class="inline-flex items-center px-4 py-2 bg-gray-400 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-600 focus:outline-none focus:border-gray-600 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mr-2">
        <?php echo e(__('Batalkan')); ?>

    </a>
    
    <?php if(collect($dataToProcess)->contains('status_validasi', 'Siap Diproses')): ?>
        <form action="<?php echo e(route('denda_administratif.confirm')); ?>" method="POST">
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
                <?php echo e(__('Konfirmasi & Simpan')); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
        </form>
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
<?php endif; ?><?php /**PATH D:\xampp\htdocs\epbb-tes\resources\views/denda_administratif/preview.blade.php ENDPATH**/ ?>