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
            <?php echo e(__('Pembentukan Daftar Nominatif')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold mb-4">Form Pembentukan Daftar Nominatif</h3>
            
            <?php if(session('success')): ?>
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Berhasil!</strong>
                    <span class="block sm:inline"><?php echo e(session('success')); ?></span>
                </div>
            <?php endif; ?>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="<?php echo e(route('dafnom.store')); ?>">
                        <?php echo csrf_field(); ?>

                        <div class="mt-4">
                            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'jenis_wilayah','value' => __('Wilayah')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'jenis_wilayah','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Wilayah'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                            <select id="jenis_wilayah" name="jenis_wilayah" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="semua">Semua Kecamatan</option>
                                <option value="kecamatan">Per Kecamatan</option>
                                <option value="kelurahan">Per Kelurahan/Desa</option>
                            </select>
                        </div>

                        <div id="pilihan_kecamatan" class="mt-4" style="display: none;">
                            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'kd_kecamatan','value' => __('Pilih Kecamatan')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'kd_kecamatan','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Pilih Kecamatan'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                            <select id="kd_kecamatan" name="kd_kecamatan" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">-- Pilih Kecamatan --</option>
                                <?php $__currentLoopData = $kecamatans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kecamatan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($kecamatan->kd_kecamatan); ?>"><?php echo e($kecamatan->nm_kecamatan); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div id="pilihan_kelurahan" class="mt-4" style="display: none;">
                            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'kd_kelurahan','value' => __('Pilih Kelurahan/Desa')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'kd_kelurahan','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Pilih Kelurahan/Desa'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                            <select id="kd_kelurahan" name="kd_kelurahan" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">-- Pilih Kelurahan/Desa --</option>
                            </select>
                        </div>

                        <div class="mt-4">
                            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'tahun_pembentukan','value' => __('Tahun Pembentukan')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'tahun_pembentukan','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Tahun Pembentukan'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.text-input','data' => ['id' => 'tahun_pembentukan','class' => 'block mt-1 w-full bg-gray-100','type' => 'text','name' => 'tahun_pembentukan','value' => ''.e(date('Y')).'','readonly' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('text-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'tahun_pembentukan','class' => 'block mt-1 w-full bg-gray-100','type' => 'text','name' => 'tahun_pembentukan','value' => ''.e(date('Y')).'','readonly' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                        </div>

                        <div class="mt-4">
                            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['for' => 'no_formulir','value' => __('Nomor Formulir (Opsional)')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'no_formulir','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Nomor Formulir (Opsional)'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.text-input','data' => ['id' => 'no_formulir','class' => 'block mt-1 w-full','type' => 'text','name' => 'no_formulir']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('text-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'no_formulir','class' => 'block mt-1 w-full','type' => 'text','name' => 'no_formulir']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                        </div>

                        <div class="mt-4">
                            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-label','data' => ['value' => __('Metode Pembentukan')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('input-label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Metode Pembentukan'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                            <div class="mt-2 space-y-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="metode" value="ulang" class="rounded-full border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" checked>
                                    <span class="ml-2 text-sm text-gray-600">Buat Semua / Ulang</span>
                                </label>
                                <label class="inline-flex items-center ml-6">
                                    <input type="radio" name="metode" value="susulan" class="rounded-full border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-600">Susulan</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-end mt-6">
                            <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.primary-button','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('primary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                                <?php echo e(__('Proses Pembentukan')); ?>

                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                        </div>
                    </form>
                    
                    
                    <div id="progress-terminal" class="mt-6 p-4 bg-gray-900 text-white font-mono rounded-lg shadow-lg" style="display: none;">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm">Memonitor Proses...</span>
                            <div id="status-indicator" class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></div>
                        </div>
                        <div id="progress-message" class="text-sm whitespace-pre-wrap">Menunggu proses dimulai...</div>
                        <div class="w-full bg-gray-700 rounded-full h-2.5 mt-3">
                            <div id="progress-bar" class="bg-green-500 h-2.5 rounded-full" style="width: 0%"></div>
                        </div>
                        <div id="progress-text" class="text-right text-xs mt-1">0 / 0</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // BAGIAN 1: LOGIKA UNTUK DROPDOWN DINAMIS
            const jenisWilayah = document.getElementById('jenis_wilayah');
            const pilihanKecamatan = document.getElementById('pilihan_kecamatan');
            const pilihanKelurahan = document.getElementById('pilihan_kelurahan');
            const selectKecamatan = document.getElementById('kd_kecamatan');
            const selectKelurahan = document.getElementById('kd_kelurahan');

            jenisWilayah.addEventListener('change', function () {
                pilihanKecamatan.style.display = 'none';
                pilihanKelurahan.style.display = 'none';
                selectKecamatan.required = false;
                selectKelurahan.required = false;

                if (this.value === 'kecamatan') {
                    pilihanKecamatan.style.display = 'block';
                    selectKecamatan.required = true;
                } else if (this.value === 'kelurahan') {
                    pilihanKecamatan.style.display = 'block';
                    pilihanKelurahan.style.display = 'block';
                    selectKecamatan.required = true;
                    selectKelurahan.required = true;
                }
            });

            selectKecamatan.addEventListener('change', function () {
                if (jenisWilayah.value === 'kelurahan' && this.value) {
                    const url = `<?php echo e(route('dafnom.getKelurahan', '')); ?>/${this.value}`;
                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            selectKelurahan.innerHTML = '<option value="">-- Pilih Kelurahan/Desa --</option>';
                            data.forEach(kelurahan => {
                                selectKelurahan.innerHTML += `<option value="${kelurahan.kd_kelurahan}">${kelurahan.nm_kelurahan}</option>`;
                            });
                        });
                }
            });

            // BAGIAN 2: LOGIKA UNTUK TERMINAL PROGRES
            const logIdToMonitor = <?php echo json_encode(session('monitoring_log_id'), 15, 512) ?>;
            
            if (logIdToMonitor) {
                const terminal = document.getElementById('progress-terminal');
                const messageEl = document.getElementById('progress-message');
                const progressBar = document.getElementById('progress-bar');
                const progressText = document.getElementById('progress-text');
                const statusIndicator = document.getElementById('status-indicator');
                
                terminal.style.display = 'block';

                let intervalId = setInterval(function () {
                    fetch(`<?php echo e(route('dafnom.log.status', '')); ?>/${logIdToMonitor}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            let percentage = 0;
                            if (data.progress_total > 0) {
                                percentage = (data.progress_current / data.progress_total) * 100;
                            }
                            
                            progressBar.style.width = percentage.toFixed(2) + '%';
                            progressText.textContent = `${data.progress_current} / ${data.progress_total}`;
                            
                            if (data.status === 'processing') {
                                messageEl.textContent = `Status: Memproses...\n${data.wilayah_text}`;
                            } else if (data.status === 'success') {
                                messageEl.textContent = `Status: Berhasil!\n${data.message || 'Proses telah selesai.'}`;
                                statusIndicator.classList.remove('bg-yellow-400', 'animate-pulse');
                                statusIndicator.classList.add('bg-green-500');
                                clearInterval(intervalId);
                            } else if (data.status === 'failed') {
                                messageEl.textContent = `Status: Gagal!\nPesan: ${data.message}`;
                                statusIndicator.classList.remove('bg-yellow-400', 'animate-pulse');
                                statusIndicator.classList.add('bg-red-500');
                                progressBar.classList.remove('bg-green-500');
                                progressBar.classList.add('bg-red-500');
                                clearInterval(intervalId);
                            }
                        })
                        .catch(error => {
                            messageEl.textContent = 'Gagal memuat status. Coba refresh halaman.';
                            statusIndicator.classList.remove('bg-yellow-400', 'animate-pulse');
                            statusIndicator.classList.add('bg-red-500');
                            clearInterval(intervalId);
                        });
                }, 3000); // Bertanya setiap 3 detik
            }
        });
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da)): ?>
<?php $component = $__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da; ?>
<?php unset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da); ?>
<?php endif; ?><?php /**PATH D:\xampp\htdocs\aplikasi-final-sppt\resources\views/dafnom/create.blade.php ENDPATH**/ ?>