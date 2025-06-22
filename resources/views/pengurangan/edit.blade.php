<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Pengurangan SPPT') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold mb-4">Edit Data Pengurangan SPPT</h3>

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- PERUBAHAN UTAMA: Form action diubah ke route 'pengurangan.preview' --}}
                    <form action="{{ route('pengurangan.preview') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        {{-- Method PATCH tidak lagi digunakan --}}

                        {{-- Input tersembunyi untuk menandai ini proses UPDATE dan membawa ID --}}
                        <input type="hidden" name="pengurangan_id" value="{{ $pengurangan->id }}">
                        
                        {{-- Input tersembunyi untuk NOP, karena preview() membutuhkannya --}}
                        <input type="hidden" name="nop" value="{{ $pengurangan->kd_propinsi . $pengurangan->kd_dati2 . $pengurangan->kd_kecamatan . $pengurangan->kd_kelurahan . $pengurangan->kd_blok . $pengurangan->no_urut . $pengurangan->kd_jns_op }}">

                        {{-- Input tersembunyi untuk Tahun Pajak, karena preview() juga membutuhkannya --}}
                        <input type="hidden" name="thn_pajak_sppt_input" value="{{ $pengurangan->thn_pajak_sppt }}">


                        {{-- Data Objek Pajak (Read-only) --}}
                        <div class="border p-4 rounded-md mb-6">
                            <h4 class="font-semibold text-lg mb-4 border-b pb-2">Data Objek Pajak</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <x-input-label for="nop_display" :value="__('NOP')" />
                                    <x-text-input id="nop_display" class="block mt-1 w-full bg-gray-100" type="text" value="{{ $pengurangan->formatted_nop }}" readonly />
                                </div>
                                <div>
                                    <x-input-label for="thn_pajak_sppt_display" :value="__('Tahun Pajak SPPT')" />
                                    <x-text-input id="thn_pajak_sppt_display" class="block mt-1 w-full bg-gray-100" type="text" value="{{ $pengurangan->thn_pajak_sppt }}" readonly />
                                </div>
                                <div>
                                    <x-input-label for="baku_lama_display" :value="__('PBB Awal')" />
                                    <x-text-input id="baku_lama_display" class="block mt-1 w-full bg-gray-100" type="text" value="{{ number_format($pengurangan->pbb_terhutang_sppt_lama ?? 0, 0, ',', '.') }}" readonly />
                                </div>
                            </div>
                        </div>

                        {{-- Detail Pengurangan --}}
                        <div class="border p-4 rounded-md mb-6">
                             <h4 class="font-semibold text-lg mb-4 border-b pb-2">Detail Pengurangan</h4>
                             <div class="mb-4">
                                <x-input-label for="jenis_pengurangan_dropdown" :value="__('Jenis Pengurangan')" />
                                <select id="jenis_pengurangan_dropdown" name="jenis_pengurangan_dropdown" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" onchange="updatePersentase()" required>
                                    <option value="Veteran" data-persentase="75" {{ old('jenis_pengurangan_dropdown', $pengurangan->jenis_pengurangan) == 'Veteran' ? 'selected' : '' }}>1. Veteran</option>
                                    <option value="Pertanian Terbatas" data-persentase="30" {{ old('jenis_pengurangan_dropdown', $pengurangan->jenis_pengurangan) == 'Pertanian Terbatas' ? 'selected' : '' }}>2. Pertanian Terbatas</option>
                                    <option value="LP2B" data-persentase="50" {{ old('jenis_pengurangan_dropdown', $pengurangan->jenis_pengurangan) == 'LP2B' ? 'selected' : '' }}>3. LP2B</option>
                                    <option value="Pensiunan" data-persentase="50" {{ old('jenis_pengurangan_dropdown', $pengurangan->jenis_pengurangan) == 'Pensiunan' ? 'selected' : '' }}>4. Pensiunan</option>
                                    <option value="Cagar Budaya" data-persentase="50" {{ old('jenis_pengurangan_dropdown', $pengurangan->jenis_pengurangan) == 'Cagar Budaya' ? 'selected' : '' }}>5. Cagar Budaya</option>
                                    <option value="Penghasilan Rendah/SKTM" data-persentase="25" {{ old('jenis_pengurangan_dropdown', $pengurangan->jenis_pengurangan) == 'Penghasilan Rendah/SKTM' ? 'selected' : '' }}>6. Penghasilan Rendah/SKTM</option>
                                    <option value="Lainnya" data-persentase="manual" {{ old('jenis_pengurangan_dropdown', $pengurangan->jenis_pengurangan) == 'Lainnya' ? 'selected' : '' }}>7. Lainnya</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <x-input-label for="persentase" :value="__('Persentase Pengurangan (%)')" />
                                <x-text-input id="persentase" class="block mt-1 w-full" type="number" name="persentase" :value="old('persentase', $pengurangan->persentase)" required min="0" max="100" step="0.01" />
                            </div>
                        </div>

                        {{-- Informasi SK & Berkas --}}
                        <div class="border p-4 rounded-md mb-6">
                            <h4 class="font-semibold text-lg mb-4 border-b pb-2">Informasi SK & Berkas</h4>
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="nomor_sk" :value="__('Nomor SK')" />
                                    <x-text-input id="nomor_sk" class="block mt-1 w-full" type="text" name="nomor_sk" value="{{ old('nomor_sk', explode('/', $pengurangan->no_sk_pengurangan ?? '//')[1] ?? '') }}" required/>
                                </div>
                                <div>
                                    <x-input-label for="tahun_sk" :value="__('Tahun SK')" />
                                    <x-text-input id="tahun_sk" class="block mt-1 w-full" type="number" name="tahun_sk" value="{{ old('tahun_sk', explode('/', $pengurangan->no_sk_pengurangan ?? '////')[5] ?? '') }}" required min="2000" max="2100" />
                                </div>
                            </div>
                            <div class="mt-4">
                                <x-input-label for="tgl_sk_pengurangan" :value="__('Tanggal SK Pengurangan')" />
                                <x-text-input id="tgl_sk_pengurangan" class="block mt-1 w-full" type="date" name="tgl_sk_pengurangan" value="{{ old('tgl_sk_pengurangan', $pengurangan->tgl_sk_pengurangan ? \Carbon\Carbon::parse($pengurangan->tgl_sk_pengurangan)->format('Y-m-d') : '') }}" required />
                            </div>
                            <div class="mt-4">
                                <x-input-label for="berkas" :value="__('Ganti Berkas (PDF, Opsional)')" />
                                <input id="berkas" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" type="file" name="berkas" accept="application/pdf" />
                                @if($pengurangan->berkas_path)
                                <div class="mt-2" id="berkas_info_section">
                                    <p class="text-sm text-gray-600">Berkas saat ini: 
                                        <a href="{{ Storage::url($pengurangan->berkas_path) }}" target="_blank" class="text-blue-600 hover:underline">{{ basename($pengurangan->berkas_path) }}</a>
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('laporan.pengurangan') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700">
                                {{ __('Batal') }}
                            </a>
                            <x-primary-button class="ml-4">
                                {{ __('Lanjutkan ke Pratinjau') }}
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
                } else {
                    persentaseInput.value = persentaseValue;
                    persentaseInput.readOnly = true;
                }
            }
            document.addEventListener('DOMContentLoaded', function() {
                updatePersentase();
            });
        </script>
    @endpush
</x-app-layout>
