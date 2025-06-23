<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Penggabungan SPPT') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('penggabungan.preview') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-6">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Kolom Kiri --}}
                                <div class="space-y-4">
                                    <div>
                                        <x-input-label for="nomor_pelayanan_pembatalan" value="Nomor Pelayanan Pembatalan" />
                                        <x-text-input id="nomor_pelayanan_pembatalan" class="block mt-1 w-full" type="text" name="nomor_pelayanan_pembatalan" :value="old('nomor_pelayanan_pembatalan')" required maxlength="11" />
                                        <p class="mt-1 text-sm text-gray-500">NOP yang akan digabung/dihapus.</p>
                                    </div>
                                    <div>
                                        <x-input-label for="nop_pembatalan" value="NOP (Hasil Pencarian)" />
                                        <x-text-input id="nop_pembatalan" class="block mt-1 w-full bg-gray-100" type="text" readonly />
                                    </div>
                                     <div>
                                        <x-input-label for="nama_wp_pembatalan" value="Nama WP (Hasil Pencarian)" />
                                        <x-text-input id="nama_wp_pembatalan" class="block mt-1 w-full bg-gray-100" type="text" readonly />
                                    </div>
                                </div>
                                {{-- Kolom Kanan --}}
                                <div class="space-y-4">
                                    <div>
                                        <x-input-label for="nomor_pelayanan_pembetulan" value="Nomor Pelayanan Pembetulan" />
                                        <x-text-input id="nomor_pelayanan_pembetulan" class="block mt-1 w-full" type="text" name="nomor_pelayanan_pembetulan" :value="old('nomor_pelayanan_pembetulan')" required maxlength="11" />
                                         <p class="mt-1 text-sm text-gray-500">NOP tujuan penggabungan.</p>
                                    </div>
                                    <div>
                                        <x-input-label for="nop_pembetulan" value="NOP (Hasil Pencarian)" />
                                        <x-text-input id="nop_pembetulan" class="block mt-1 w-full bg-gray-100" type="text" readonly />
                                    </div>
                                     <div>
                                        <x-input-label for="nama_wp_pembetulan" value="Nama WP (Hasil Pencarian)" />
                                        <x-text-input id="nama_wp_pembetulan" class="block mt-1 w-full bg-gray-100" type="text" readonly />
                                    </div>
                                </div>
                            </div>
                            
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="tahun_pajak" value="Tahun Pajak" />
                                    <x-text-input id="tahun_pajak" class="block mt-1 w-full bg-gray-100" type="text" name="tahun_pajak" readonly />
                                </div>
                                <div>
                                     <x-input-label for="keterangan" value="Keterangan" />
                                     <x-text-input id="keterangan" class="block mt-1 w-full bg-gray-100" type="text" name="keterangan" readonly />
                                </div>
                            </div>
                            
                             <div class="flex items-end space-x-4">
                                <div class="flex-grow">
                                    <label class="block font-medium text-sm text-gray-700">Bidang</label>
                                    <div class="mt-2 flex items-center space-x-4 rounded-lg p-2 bg-gray-100 border border-gray-200">
                                        <label class="flex items-center">
                                            <input type="radio" name="bidang" value="Pelayanan" class="form-radio" checked>
                                            <span class="ml-2 text-sm">Pelayanan</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="bidang" value="Pendataan" class="form-radio">
                                            <span class="ml-2 text-sm">Pendataan</span>
                                        </label>
                                    </div>
                                </div>
                                <x-secondary-button type="button" id="cari-data-btn">
                                    <span id="search-text">Cari Data</span>
                                    <span id="search-spinner" class="hidden animate-spin rounded-full h-4 w-4 border-b-2 border-gray-800"></span>
                                </x-secondary-button>
                            </div>
                            
                            <div>
                                <x-input-label for="berkas" value="Upload Berkas Pelayanan (PDF)" />
                                <input id="berkas" class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" type="file" name="berkas" required accept=".pdf">
                            </div>

                        </div>

                        <div class="flex items-center justify-end mt-8">
                            <x-primary-button id="proses-btn" class="opacity-50" disabled>
                                {{ __('Lanjut ke Pratinjau') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const noPembatalan = document.getElementById('nomor_pelayanan_pembatalan');
            const noPembetulan = document.getElementById('nomor_pelayanan_pembetulan');
            const tahunPajakField = document.getElementById('tahun_pajak');
            const keteranganField = document.getElementById('keterangan');
            const bidangRadios = document.querySelectorAll('input[name="bidang"]');
            const cariBtn = document.getElementById('cari-data-btn');
            const prosesBtn = document.getElementById('proses-btn');
            const searchText = document.getElementById('search-text');
            const searchSpinner = document.getElementById('search-spinner');

            // Elemen field baru
            const nopPembatalanField = document.getElementById('nop_pembatalan');
            const namaWpPembatalanField = document.getElementById('nama_wp_pembatalan');
            const nopPembetulanField = document.getElementById('nop_pembetulan');
            const namaWpPembetulanField = document.getElementById('nama_wp_pembetulan');

            let keteranganNop = '';

            function generateKeterangan() {
                if (!keteranganNop) return;
                let bidangText = '';
                const selectedBidang = document.querySelector('input[name="bidang"]:checked').value;
                if (selectedBidang === 'Pendataan') { bidangText = ' (PENDATAAN)'; }
                keteranganField.value = `GAB KE NOP ${keteranganNop}${bidangText}`;
            }

            function resetFormState() {
                tahunPajakField.value = '';
                keteranganField.value = '';
                nopPembatalanField.value = '';
                namaWpPembatalanField.value = '';
                nopPembetulanField.value = '';
                namaWpPembetulanField.value = '';
                prosesBtn.disabled = true;
                prosesBtn.classList.add('opacity-50');
            }

            bidangRadios.forEach(radio => radio.addEventListener('change', generateKeterangan));

            cariBtn.addEventListener('click', function() {
                const noBatal = noPembatalan.value;
                const noBetul = noPembetulan.value;

                if (noBatal.length !== 11 || noBetul.length !== 11) {
                    alert('Pastikan kedua Nomor Pelayanan diisi dengan 11 digit.');
                    return;
                }
                
                searchText.classList.add('hidden');
                searchSpinner.classList.remove('hidden');
                cariBtn.disabled = true;
                resetFormState();

                fetch("{{ route('penggabungan.fetch-data') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ no_pelayanan_pembatalan: noBatal, no_pelayanan_pembetulan: noBetul, })
                })
                .then(response => response.json().then(data => ({ ok: response.ok, data })))
                .then(({ ok, data }) => {
                    if (!ok) { throw new Error(data.error || 'Terjadi kesalahan tidak diketahui.'); }
                    
                    // Mengisi semua field
                    tahunPajakField.value = data.tahun_pajak;
                    keteranganNop = data.keterangan_nop;
                    nopPembatalanField.value = data.nop_pembatalan;
                    namaWpPembatalanField.value = data.nama_wp_pembatalan;
                    nopPembetulanField.value = data.nop_pembetulan;
                    namaWpPembetulanField.value = data.nama_wp_pembetulan;
                    
                    generateKeterangan();
                    prosesBtn.disabled = false;
                    prosesBtn.classList.remove('opacity-50');
                })
                .catch(error => {
                    alert('Error: ' + error.message);
                    resetFormState();
                })
                .finally(() => {
                    searchText.classList.remove('hidden');
                    searchSpinner.classList.add('hidden');
                    cariBtn.disabled = false;
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
