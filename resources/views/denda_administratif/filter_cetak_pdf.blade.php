<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Filter Laporan Penghapusan Denda Administratif untuk Cetak') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold mb-4">Pilih Kriteria dan Format Cetak Laporan</h3>

                    <form action="{{ route('denda_administratif.cetak-pdf-filtered') }}" method="GET" id="filterExportForm" target="_blank"> {{-- PERBAIKAN: Tambah ID form dan target --}}
                        <div class="mb-4">
                            <x-input-label for="filter_tahun_pajak" :value="__('Tahun Pajak')" />
                            <x-text-input id="filter_tahun_pajak" class="block mt-1 w-full" type="number" name="tahun_pajak" value="{{ request('tahun_pajak') }}" min="2000" max="2100" placeholder="Contoh: 2024" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="filter_kecamatan" :value="__('Kecamatan')" />
                            <select id="filter_kecamatan" name="kd_kecamatan" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Semua Kecamatan</option>
                                @foreach($kecamatans as $kecamatan)
                                    <option value="{{ $kecamatan->kd_kecamatan }}" {{ request('kd_kecamatan') == $kecamatan->kd_kecamatan ? 'selected' : '' }}>
                                        {{ $kecamatan->nm_kecamatan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="filter_no_sk" :value="__('Nomor SK')" />
                            <x-text-input id="filter_no_sk" class="block mt-1 w-full" type="text" name="no_sk" value="{{ request('no_sk') }}" placeholder="Contoh: 123" />
                        </div>

                        {{-- PERBAIKAN: Pilihan Format Export --}}
                        <div class="mb-4">
                            <x-input-label :value="__('Pilih Format Export')" class="mb-2" />
                            <div class="flex items-center space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="format" value="pdf" class="rounded-full border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('format', 'pdf') == 'pdf' ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-600">PDF</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="format" value="xlsx" class="rounded-full border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('format') == 'xlsx' ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-600">Excel</span>
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('format')" class="mt-2" />
                        </div>
                        {{-- Akhir Pilihan Format Export --}}

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('denda_administratif.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-400 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-600 focus:outline-none focus:border-gray-600 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mr-2">
                                {{ __('Batal') }}
                            </a>
                            <x-primary-button type="submit">
                                {{ __('Export') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>