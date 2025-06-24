<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Filter Laporan Penggabungan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold mb-4">Pilih Kriteria Laporan</h3>
                    <form method="GET" action="{{ route('penggabungan.cetak-pdf-filtered') }}" target="_blank">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="tahun_pajak" value="Tahun Pajak (Opsional)" />
                                <x-text-input id="tahun_pajak" class="block mt-1 w-full" type="number" name="tahun_pajak" :value="date('Y')" />
                            </div>
                            <div>
                                <x-input-label for="kd_kecamatan" value="Kecamatan (Opsional)" />
                                <select name="kd_kecamatan" id="kd_kecamatan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">-- Semua Kecamatan --</option>
                                    @foreach ($kecamatans as $kecamatan)
                                        <option value="{{ $kecamatan->kd_kecamatan }}">{{ $kecamatan->nm_kecamatan }}</option>
                                    @endforeach
                                </select>
                            </div>
                             <div class="md:col-span-2">
                                <x-input-label for="keterangan" value="Keterangan (Opsional)" />
                                <x-text-input id="keterangan" class="block mt-1 w-full" type="text" name="keterangan" placeholder="Contoh: GAB KE NOP..."/>
                            </div>
                        </div>

                        <div class="mt-6">
                            <x-input-label for="format" value="Format Laporan" />
                             <select name="format" id="format" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="pdf">PDF</option>
                                <option value="excel">Excel</option>
                            </select>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('Cetak Laporan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
