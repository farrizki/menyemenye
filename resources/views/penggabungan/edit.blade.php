<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Penggabungan SPPT') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('penggabungan.preview.update', $penggabungan->id) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-6">
                            
                            {{-- Data yang tidak bisa diubah --}}
                             <div class="p-4 border rounded-lg bg-gray-100">
                                <h4 class="font-semibold text-lg mb-2">Data Objek Pajak (Tidak dapat diubah)</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 text-sm">
                                    <p><strong>NOP:</strong> {{ $penggabungan->formatted_nop }}</p>
                                    <p><strong>Tahun Pajak:</strong> {{ $penggabungan->thn_pajak_sppt }}</p>
                                    <p class="md:col-span-2"><strong>Nama WP:</strong> {{ $penggabungan->nm_wp_sppt }}</p>
                                </div>
                            </div>
                            
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="nomor_pelayanan_pembatalan" value="Nomor Pelayanan Pembatalan" />
                                    <x-text-input id="nomor_pelayanan_pembatalan" class="block mt-1 w-full" type="text" name="nomor_pelayanan_pembatalan" :value="old('nomor_pelayanan_pembatalan', $penggabungan->nomor_pelayanan_pembatalan)" required maxlength="11" />
                                </div>
                                <div>
                                    <x-input-label for="nomor_pelayanan_pembetulan" value="Nomor Pelayanan Pembetulan" />
                                    <x-text-input id="nomor_pelayanan_pembetulan" class="block mt-1 w-full" type="text" name="nomor_pelayanan_pembetulan" :value="old('nomor_pelayanan_pembetulan', $penggabungan->nomor_pelayanan_pembetulan)" required maxlength="11" />
                                </div>
                            </div>
                             <div class="grid grid-cols-1">
                                <div>
                                     <x-input-label for="keterangan" value="Keterangan" />
                                     <x-text-input id="keterangan" class="block mt-1 w-full" type="text" name="keterangan" :value="old('keterangan', $penggabungan->keterangan_penggabungan)" required />
                                </div>
                            </div>
                             <div class="flex items-end space-x-4">
                                <div class="flex-grow">
                                    <label class="block font-medium text-sm text-gray-700">Bidang</label>
                                    <div class="mt-2 flex items-center space-x-4">
                                        <label class="flex items-center">
                                            <input type="radio" name="bidang" value="Pelayanan" class="form-radio" {{ (old('bidang', $penggabungan->bidang) == 'Pelayanan') ? 'checked' : '' }}>
                                            <span class="ml-2 text-sm">Pelayanan</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="bidang" value="Pendataan" class="form-radio" {{ (old('bidang', $penggabungan->bidang) == 'Pendataan') ? 'checked' : '' }}>
                                            <span class="ml-2 text-sm">Pendataan</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <x-input-label for="berkas" value="Upload Berkas Baru (Opsional)" />
                                <input id="berkas" class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" type="file" name="berkas" accept=".pdf">
                                <p class="mt-1 text-sm text-gray-500">Kosongkan jika tidak ingin mengubah berkas.</p>
                            </div>

                        </div>

                        <div class="flex items-center justify-end mt-8">
                             <a href="{{ route('penggabungan.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <x-primary-button>
                                {{ __('Lanjut ke Pratinjau') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
