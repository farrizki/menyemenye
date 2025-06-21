<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Penghapusan Denda Administratif') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold mb-4">Form Penghapusan Denda Administratif</h3>

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('denda_administratif.preview') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Pilihan Input NOP --}}
                        <div class="mb-4">
                            <x-input-label for="input_type" :value="__('Cara Input NOP')" />
                            <select id="input_type" name="input_type" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" onchange="toggleNopInput()">
                                <option value="nop_manual" {{ old('input_type') == 'nop_manual' ? 'selected' : '' }}>Input NOP Manual</option>
                                <option value="upload_excel" {{ old('input_type') == 'upload_excel' ? 'selected' : '' }}>Upload File Excel</option>
                                <option value="satu_desa" {{ old('input_type') == 'satu_desa' ? 'selected' : '' }}>Satu Desa</option>
                            </select>
                            <x-input-error :messages="$errors->get('input_type')" class="mt-2" />
                        </div>

                        {{-- Input NOP Manual (Conditional) --}}
                        <div class="mb-4" id="nop_manual_section" style="display: {{ old('input_type', 'nop_manual') == 'nop_manual' ? 'block' : 'none' }}">
                            <x-input-label for="nop_manual" :value="__('NOP (Pisahkan dengan koma)')" />
                            <x-text-input id="nop_manual" class="block mt-1 w-full" type="text" name="nop_manual" :value="old('nop_manual')" placeholder="Contoh: 351814001000802040,351814001101004020" />
                            <x-input-error :messages="$errors->get('nop_manual')" class="mt-2" />
                        </div>

                        {{-- Upload File Excel (Conditional) --}}
                        <div class="mb-4" id="upload_excel_section" style="display: {{ old('input_type') == 'upload_excel' ? 'block' : 'none' }}">
                            <x-input-label for="excel_file" :value="__('Upload File Excel (NOP & Tahun Pajak di kolom A & B)')" />
                            <input id="excel_file" class="block mt-1 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" type="file" name="excel_file" accept=".xls,.xlsx" />
                            <x-input-error :messages="$errors->get('excel_file')" class="mt-2" />
                        </div>

                        {{-- Satu Desa (Conditional) --}}
                        <div class="mb-4" id="satu_desa_section" style="display: {{ old('input_type') == 'satu_desa' ? 'block' : 'none' }}">
                            <x-input-label for="kd_kecamatan_desa" :value="__('Kecamatan')" />
                            <select id="kd_kecamatan_desa" name="kd_kecamatan_desa" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" onchange="getKelurahanByKecamatan()">
                                <option value="">Pilih Kecamatan</option>
                                @foreach($kecamatans as $kecamatan)
                                    <option value="{{ $kecamatan->kd_kecamatan }}" {{ old('kd_kecamatan_desa') == $kecamatan->kd_kecamatan ? 'selected' : '' }}>
                                        {{ $kecamatan->nm_kecamatan }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('kd_kecamatan_desa')" class="mt-2" />

                            <div class="mt-4">
                                <x-input-label for="kd_kelurahan_desa" :value="__('Kelurahan/Desa')" />
                                <select id="kd_kelurahan_desa" name="kd_kelurahan_desa" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Pilih Kelurahan/Desa</option>
                                    {{-- Opsi kelurahan akan diisi via AJAX --}}
                                </select>
                                <x-input-error :messages="$errors->get('kd_kelurahan_desa')" class="mt-2" />
                            </div>
                        </div>

                        {{-- Input Tahun Pajak (Opsional, tergantung input_type) --}}
                        <div class="mb-4" id="thn_pajak_input_section" style="display: {{ old('input_type', 'nop_manual') == 'upload_excel' ? 'none' : 'block' }}">
                            <x-input-label for="thn_pajak_input" :value="__('Tahun Pajak (Pisahkan dengan koma jika lebih dari 1 tahun)')" />
                            <x-text-input id="thn_pajak_input" class="block mt-1 w-full" type="text" name="thn_pajak_input" :value="old('thn_pajak_input', \Carbon\Carbon::now()->year)" placeholder="Contoh: 2023,2024,2025" />
                            <x-input-error :messages="$errors->get('thn_pajak_input')" class="mt-2" />
                        </div>

                        {{-- Tanggal Jatuh Tempo Baru --}}
                        <div class="mb-4">
                            <x-input-label for="tgl_jatuh_tempo_baru" :value="__('Tanggal Jatuh Tempo Baru')" />
                            <x-text-input id="tgl_jatuh_tempo_baru" class="block mt-1 w-full" type="date" name="tgl_jatuh_tempo_baru" :value="old('tgl_jatuh_tempo_baru')" required />
                            <x-input-error :messages="$errors->get('tgl_jatuh_tempo_baru')" class="mt-2" />
                        </div>

                        {{-- No SK, Tahun SK, Tanggal SK, Upload Berkas --}}
                        <div class="mb-4">
                            <x-input-label for="nomor_sk" :value="__('Nomor SK')" />
                            <x-text-input id="nomor_sk" class="block mt-1 w-full" type="text" name="nomor_sk" :value="old('nomor_sk')" required />
                            <x-input-error :messages="$errors->get('nomor_sk')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="tahun_sk" :value="__('Tahun SK')" />
                            <x-text-input id="tahun_sk" class="block mt-1 w-full" type="number" name="tahun_sk" :value="old('tahun_sk', \Carbon\Carbon::now()->year)" required min="2000" max="2100" />
                            <x-input-error :messages="$errors->get('tahun_sk')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="tgl_sk" :value="__('Tanggal SK')" />
                            <x-text-input id="tgl_sk" class="block mt-1 w-full" type="date" name="tgl_sk" :value="old('tgl_sk')" required />
                            <x-input-error :messages="$errors->get('tgl_sk')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="berkas" :value="__('Upload Berkas (PDF, Max 24MB)')" />
                            <input id="berkas" class="block mt-1 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" type="file" name="berkas" accept="application/pdf" required />
                            <x-input-error :messages="$errors->get('berkas')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('Proses Penghapusan Denda') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            // Fungsi untuk menampilkan/menyembunyikan input NOP berdasarkan pilihan
            function toggleNopInput() {
                const inputType = document.getElementById('input_type').value;
                document.getElementById('nop_manual_section').style.display = 'none';
                document.getElementById('upload_excel_section').style.display = 'none';
                document.getElementById('satu_desa_section').style.display = 'none';
                document.getElementById('thn_pajak_input_section').style.display = 'block'; // Default muncul

                // Atur atribut 'required'
                document.getElementById('nop_manual').required = false;
                document.getElementById('excel_file').required = false;
                document.getElementById('kd_kecamatan_desa').required = false;
                document.getElementById('kd_kelurahan_desa').required = false;
                document.getElementById('thn_pajak_input').required = false; // Akan disesuaikan

                if (inputType === 'nop_manual') {
                    document.getElementById('nop_manual_section').style.display = 'block';
                    document.getElementById('nop_manual').required = true;
                    document.getElementById('thn_pajak_input').required = true;
                } else if (inputType === 'upload_excel') {
                    document.getElementById('upload_excel_section').style.display = 'block';
                    document.getElementById('excel_file').required = true;
                    document.getElementById('thn_pajak_input_section').style.display = 'none'; // Sembunyikan jika upload excel
                } else if (inputType === 'satu_desa') {
                    document.getElementById('satu_desa_section').style.display = 'block';
                    document.getElementById('kd_kecamatan_desa').required = true;
                    // Kelurahan akan diset required setelah diisi via AJAX
                    document.getElementById('thn_pajak_input').required = true;
                }
            }

            // Fungsi untuk mengisi dropdown kelurahan via AJAX
            function getKelurahanByKecamatan() {
                const kdKecamatan = document.getElementById('kd_kecamatan_desa').value;
                const kelurahanDropdown = document.getElementById('kd_kelurahan_desa');

                kelurahanDropdown.innerHTML = '<option value="">Memuat...</option>'; // Pesan loading

                if (kdKecamatan) {
                    fetch("{{ route('denda_administratif.get-kelurahan-by-kecamatan') }}?kd_kecamatan=" + kdKecamatan)
                        .then(response => response.json())
                        .then(data => {
                            kelurahanDropdown.innerHTML = '<option value="">Pilih Kelurahan/Desa</option>';
                            data.forEach(kel => {
                                const option = document.createElement('option');
                                option.value = kel.kd_kelurahan; // Nama kolom di DB Oracle biasanya huruf kecil
                                option.textContent = kel.nm_kelurahan; // Nama kolom di DB Oracle biasanya huruf kecil
                                // Set selected jika old() value cocok
                                if ("{{ old('kd_kelurahan_desa') }}" == kel.kd_kelurahan) {
                                    option.selected = true;
                                }
                                kelurahanDropdown.appendChild(option);
                            });
                            // Set required untuk kelurahan jika kecamatan sudah dipilih
                            kelurahanDropdown.required = true;
                        })
                        .catch(error => {
                            console.error('Error fetching kelurahan:', error);
                            kelurahanDropdown.innerHTML = '<option value="">Gagal memuat Kelurahan</option>';
                        });
                } else {
                    kelurahanDropdown.innerHTML = '<option value="">Pilih Kelurahan/Desa</option>';
                    kelurahanDropdown.required = false; // Hapus required jika kecamatan tidak dipilih
                }
            }

            // Panggil saat halaman dimuat untuk set state awal
            document.addEventListener('DOMContentLoaded', function() {
                toggleNopInput(); // Set display awal berdasarkan old()
                // Jika input_type adalah 'satu_desa' dan ada old('kd_kecamatan_desa'), panggil getKelurahanByKecamatan
                if (document.getElementById('input_type').value === 'satu_desa' && document.getElementById('kd_kecamatan_desa').value) {
                    getKelurahanByKecamatan();
                }
            });
        </script>
    @endpush
</x-app-layout>