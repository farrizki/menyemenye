<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengurangan SPPT') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold mb-4">Form Pengurangan SPPT</h3>

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

                    <form action="{{ route('pengurangan.preview') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <x-input-label for="nop" :value="__('NOP (Nomor Objek Pajak - Pisahkan dengan koma)')" />
                            <x-text-input id="nop" class="block mt-1 w-full" type="text" name="nop" :value="old('nop')" required autofocus placeholder="Contoh: 351814001000802040,351814001101004020" />
                            <x-input-error :messages="$errors->get('nop')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="thn_pajak_sppt_input" :value="__('Tahun Pajak SPPT yang Akan Diupdate')" />
                            <x-text-input id="thn_pajak_sppt_input" class="block mt-1 w-full" type="number" name="thn_pajak_sppt_input" :value="old('thn_pajak_sppt_input', \Carbon\Carbon::now()->year)" required min="2000" max="2100" />
                            <x-input-error :messages="$errors->get('thn_pajak_sppt_input')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="jenis_pengurangan_dropdown" :value="__('Jenis Pengurangan')" />
                            <select id="jenis_pengurangan_dropdown" name="jenis_pengurangan_dropdown" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" onchange="updatePersentase()" required> {{-- PERBAIKAN: Tambah required --}}
                                <option value="" data-persentase="" {{ old('jenis_pengurangan_dropdown') == '' ? 'selected' : '' }}>Pilih Jenis Pengurangan</option>
                                <option value="Veteran" data-persentase="75" {{ old('jenis_pengurangan_dropdown') == 'Veteran' ? 'selected' : '' }}>1. Veteran</option>
                                <option value="Pertanian Terbatas" data-persentase="30" {{ old('jenis_pengurangan_dropdown') == 'Pertanian Terbatas' ? 'selected' : '' }}>2. Pertanian Terbatas</option>
                                <option value="LP2B" data-persentase="50" {{ old('jenis_pengurangan_dropdown') == 'LP2B' ? 'selected' : '' }}>3. LP2B</option>
                                <option value="Pensiunan" data-persentase="50" {{ old('jenis_pengurangan_dropdown') == 'Pensiunan' ? 'selected' : '' }}>4. Pensiunan</option>
                                <option value="Cagar Budaya" data-persentase="50" {{ old('jenis_pengurangan_dropdown') == 'Cagar Budaya' ? 'selected' : '' }}>5. Cagar Budaya</option>
                                <option value="Penghasilan Rendah/SKTM" data-persentase="25" {{ old('jenis_pengurangan_dropdown') == 'Penghasilan Rendah/SKTM' ? 'selected' : '' }}>6. Penghasilan Rendah/SKTM</option>
                                <option value="Lainnya" data-persentase="manual" {{ old('jenis_pengurangan_dropdown') == 'Lainnya' ? 'selected' : '' }}>7. Lainnya</option>
                            </select>
                            <x-input-error :messages="$errors->get('jenis_pengurangan_dropdown')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="persentase" :value="__('Persentase Pengurangan (%)')" />
                            <x-text-input id="persentase" class="block mt-1 w-full" type="number" name="persentase" :value="old('persentase')" required min="0" max="100" step="0.01" /> {{-- PERBAIKAN: Tambah required --}}
                            <x-input-error :messages="$errors->get('persentase')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="no_sk_pengurangan" :value="__('Nomor SK')" />
                            <x-text-input id="no_sk_pengurangan" class="block mt-1 w-full" type="text" name="nomor_sk" :value="old('nomor_sk')" required /> {{-- PERBAIKAN: Tambah required --}}
                            <x-input-error :messages="$errors->get('nomor_sk')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="tahun_sk" :value="__('Tahun SK')" />
                            <x-text-input id="tahun_sk" class="block mt-1 w-full" type="number" name="tahun_sk" :value="old('tahun_sk', \Carbon\Carbon::now()->year)" required min="2000" max="2100" /> {{-- PERBAIKAN: Tambah required --}}
                            <x-input-error :messages="$errors->get('tahun_sk')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="tgl_sk_pengurangan" :value="__('Tanggal SK Pengurangan')" />
                            <x-text-input id="tgl_sk_pengurangan" class="block mt-1 w-full" type="date" name="tgl_sk_pengurangan" :value="old('tgl_sk_pengurangan')" required /> {{-- PERBAIKAN: Tambah required --}}
                            <x-input-error :messages="$errors->get('tgl_sk_pengurangan')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="berkas" :value="__('Upload Berkas (PDF, Max 24MB)')" />
                            <input id="berkas" class="block mt-1 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" type="file" name="berkas" accept="application/pdf" required /> {{-- PERBAIKAN: Tambah required --}}
                            <x-input-error :messages="$errors->get('berkas')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('Proses Pengurangan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            function updatePersentase() {
                const dropdown = document.getElementById('jenis_pengurangan_dropdown');
                const persentaseInput = document.getElementById('persentase');
                const selectedOption = dropdown.options[dropdown.selectedIndex];
                const persentaseValue = selectedOption.getAttribute('data-persentase');

                if (persentaseValue === 'manual') {
                    persentaseInput.value = '';
                    persentaseInput.readOnly = false;
                    persentaseInput.focus();
                    persentaseInput.required = true; // PERBAIKAN: Pastikan required untuk input manual
                } else if (persentaseValue !== '') { // Jika ada pilihan standar
                    persentaseInput.value = persentaseValue;
                    persentaseInput.readOnly = true;
                    persentaseInput.required = true; // PERBAIKAN: Pastikan required untuk input otomatis
                } else { // Jika pilihan kosong ("Pilih Jenis Pengurangan")
                    persentaseInput.value = '';
                    persentaseInput.readOnly = true;
                    persentaseInput.required = false; // PERBAIKAN: Tidak required jika pilihan kosong
                }
            }
            document.addEventListener('DOMContentLoaded', function() {
                updatePersentase();
            });
        </script>
    @endpush
</x-app-layout>