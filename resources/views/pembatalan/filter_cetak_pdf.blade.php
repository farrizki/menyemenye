<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Filter Cetak Laporan Pembatalan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold mb-4">Pilih Kriteria dan Format Cetak Laporan</h3>

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('pembatalan.cetak-pdf-filtered') }}" method="GET" target="_blank">
                        {{-- Filter Tahun Pajak --}}
                        <div class="mb-4">
                            <x-input-label for="tahun_pajak" :value="__('Tahun Pajak')" />
                            <x-text-input id="tahun_pajak" class="block mt-1 w-full" type="number" name="tahun_pajak" :value="old('tahun_pajak')" placeholder="Contoh: 2024" />
                        </div>

                        {{-- Filter Kecamatan --}}
                        <div class="mb-4">
                             <x-input-label for="kd_kecamatan" :value="__('Kecamatan')" />
                            <select id="kd_kecamatan" name="kd_kecamatan" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Semua Kecamatan</option>
                                @foreach($kecamatans as $kecamatan)
                                    <option value="{{ $kecamatan->kd_kecamatan }}">{{ $kecamatan->nm_kecamatan }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- Filter Nomor SK --}}
                        <div class="mb-4">
                            <x-input-label for="no_sk" :value="__('Nomor SK')" />
                            <x-text-input id="no_sk" class="block mt-1 w-full" type="text" name="no_sk" :value="old('no_sk')" placeholder="Contoh: 123" />
                        </div>

                        {{-- Pilihan Format Export --}}
                        <div class="mb-4">
                            <x-input-label :value="__('Pilih Format Export')" class="mb-2" />
                             <div class="flex items-center space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="format" value="pdf" class="rounded-full border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" checked>
                                    <span class="ml-2 text-sm text-gray-600">PDF</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="format" value="excel" class="rounded-full border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-600">Excel</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('pembatalan.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500">
                                Batal
                            </a>
                            <x-primary-button class="ml-4">
                                {{ __('Export') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>