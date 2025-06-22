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
            <?php echo e(__('Laporan Penghapusan Denda Administratif')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold mb-4">Hasil Proses Terakhir</h3>

                    <?php if(session('success')): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline"><?php echo e(session('success')); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if(!empty($flashResults)): ?>
                        <div class="mb-4">
                            <h4 class="mb-2">Detail Proses Terakhir:</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NOP</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun Pajak</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pesan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $flashResults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr class="<?php echo e($result['status'] == 'Berhasil' ? 'bg-green-50' : ($result['status'] == 'Gagal' || $result['status'] == 'Error' ? 'bg-red-50' : 'bg-yellow-50')); ?>">
                                                <td><?php echo e($result['formatted_nop'] ?? $result['nop'] ?? '-'); ?></td>
                                                <td><?php echo e($result['thn_pajak_sppt'] ?? '-'); ?></td>
                                                <td><?php echo e($result['status']); ?></td>
                                                <td><?php echo e($result['message']); ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="flex justify-end mb-4">
                        
                        <a href="<?php echo e(route('denda_administratif.filter-pdf')); ?>" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150" target="_blank">
                            <?php echo e(__('Cetak Semua PDF (Filter)')); ?>

                        </a>
                    </div>

                    <h3 class="text-xl font-bold mb-4 mt-5">Riwayat Penghapusan Denda Administratif</h3>

                    <div class="mb-4 flex items-center space-x-2">
                        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.text-input','data' => ['type' => 'text','id' => 'searchInput','placeholder' => 'Cari NOP/Nama/Tahun/Alamat/No SK...','class' => 'w-full','value' => ''.e(request('search')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('text-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'text','id' => 'searchInput','placeholder' => 'Cari NOP/Nama/Tahun/Alamat/No SK...','class' => 'w-full','value' => ''.e(request('search')).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                        <a href="<?php echo e(route('denda_administratif.index')); ?>" id="resetButton" class="inline-flex items-center px-4 py-2 bg-gray-400 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-600 focus:outline-none focus:border-gray-600 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 <?php echo e(request('search') ? '' : 'hidden'); ?>">Reset</a>
                    </div>

                    <div id="laporanTableContainer">
                        <?php echo $__env->make('denda_administratif.partials.laporan_table', ['laporanDenda' => $laporanDenda], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            const searchInput = document.getElementById('searchInput');
            const resetButton = document.getElementById('resetButton');
            const laporanTableContainer = document.getElementById('laporanTableContainer');
            let searchTimeout;

            function fetchLaporan(searchQuery = '', page = 1) {
                clearTimeout(searchTimeout);

                searchTimeout = setTimeout(() => {
                    const url = new URL("<?php echo e(route('denda_administratif.index')); ?>");
                    url.searchParams.set('search', searchQuery);
                    url.searchParams.set('page', page);
                    url.searchParams.set('ajax', 'true');

                    fetch(url, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        laporanTableContainer.innerHTML = html;

                        if (searchQuery) {
                            resetButton.classList.remove('hidden');
                        } else {
                            resetButton.classList.add('hidden');
                        }

                        attachPaginationListeners();
                    })
                    .catch(error => console.error('Error fetching laporan:', error));
                }, 300);
            }

            searchInput.addEventListener('input', (event) => {
                fetchLaporan(event.target.value);
            });

            resetButton.addEventListener('click', (event) => {
                event.preventDefault();
                searchInput.value = '';
                fetchLaporan('');
            });

            function attachPaginationListeners() {
                laporanTableContainer.querySelectorAll('.pagination a').forEach(link => {
                    link.removeEventListener('click', handlePaginationClick);
                    link.addEventListener('click', handlePaginationClick);
                });
            }

            function handlePaginationClick(event) {
                event.preventDefault();
                const url = new URL(event.target.href);
                const page = url.searchParams.get('page');
                fetchLaporan(searchInput.value, page);
            }

            document.addEventListener('DOMContentLoaded', attachPaginationListeners);
        </script>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da)): ?>
<?php $component = $__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da; ?>
<?php unset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da); ?>
<?php endif; ?><?php /**PATH D:\xampp\htdocs\epbb-tes\resources\views/denda_administratif/index.blade.php ENDPATH**/ ?>