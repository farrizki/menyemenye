<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Penghapusan Denda') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold mb-4">Edit Data Penghapusan Denda</h3>

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
                        
                        <input type="hidden" name="denda_id" value="{{ $denda->id }}">
                        <input type="hidden" name="input_type" value="nop_manual">
                        <input type="hidden" name="nop_manual" value="{{ $denda->kd_propinsi . $denda->kd_dati2 . $denda->kd_kecamatan . $denda->kd_kelurahan . $denda->kd_blok . $denda->no_urut . $denda->kd_jns_op }}">
                        <input type="hidden" name="thn_pajak_input" value="{{ $denda->thn_pajak_sppt }}">

                        {{-- Data Objek Pajak (Read-only) --}}
                        <div class="border p-4 rounded-md mb-6">
                            <h4 class="font-semibold text-lg mb-4 border-b pb-2">Data Objek Pajak</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="nop_display" :value="__('NOP')" />
                                    {{-- PERBAIKAN: Menggunakan $denda->formatted_nop dari controller --}}
                                    <x-text-input id="nop_display" class="block mt-1 w-full bg-gray-100" type="text" :value="$denda->formatted_nop" readonly />
                                </div>
                                <div>
                                    <x-input-label for="thn_pajak_sppt_display" :value="__('Tahun Pajak SPPT')" />
                                    {{-- PERBAIKAN: Menggunakan $denda->thn_pajak_sppt --}}
                                    <x-text-input id="thn_pajak_sppt_display" class="block mt-1 w-full bg-gray-100" type="text" :value="$denda->thn_pajak_sppt" readonly />
                                </div>
                            </div>
                             <div class="mt-4">
                                <x-input-label for="nama_wp_display" :value="__('Nama Wajib Pajak')" />
                                {{-- PERBAIKAN: Menggunakan $denda->nm_wp_sppt --}}
                                <x-text-input id="nama_wp_display" class="block mt-1 w-full bg-gray-100" type="text" :value="$denda->nm_wp_sppt" readonly />
                            </div>
                        </div>

                        {{-- Informasi SK & Berkas --}}
                        <div class="border p-4 rounded-md mb-6">
                            <h4 class="font-semibold text-lg mb-4 border-b pb-2">Informasi SK & Jatuh Tempo Baru</h4>
                             <div class="mb-4">
                                <x-input-label for="tgl_jatuh_tempo_baru" :value="__('Tanggal Jatuh Tempo Baru')" />
                                <x-text-input id="tgl_jatuh_tempo_baru" class="block mt-1 w-full" type="date" name="tgl_jatuh_tempo_baru" :value="old('tgl_jatuh_tempo_baru', $denda->tgl_jatuh_tempo_baru ? \Carbon\Carbon::parse($denda->tgl_jatuh_tempo_baru)->format('Y-m-d') : '')" required />
                            </div>
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="nomor_sk" :value="__('Nomor SK')" />
                                    <x-text-input id="nomor_sk" class="block mt-1 w-full" type="text" name="nomor_sk" :value="old('nomor_sk', $denda->nomor_sk_raw ?? '')" required/>
                                </div>
                                <div>
                                    <x-input-label for="tahun_sk" :value="__('Tahun SK')" />
                                    <x-text-input id="tahun_sk" class="block mt-1 w-full" type="number" name="tahun_sk" :value="old('tahun_sk', $denda->tahun_sk_raw ?? '')" required min="2000" max="2100" />
                                </div>
                            </div>
                            <div class="mt-4">
                                <x-input-label for="tgl_sk" :value="__('Tanggal SK')" />
                                <x-text-input id="tgl_sk" class="block mt-1 w-full" type="date" name="tgl_sk" :value="old('tgl_sk', $denda->tgl_sk ? \Carbon\Carbon::parse($denda->tgl_sk)->format('Y-m-d') : '')" required />
                            </div>
                            <div class="mt-4">
                                <x-input-label for="berkas" :value="__('Ganti Berkas (PDF, Opsional)')" />
                                <input id="berkas" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" type="file" name="berkas" accept="application/pdf" />
                                @if($denda->berkas_path)
                                <div class="mt-2">
                                    <p class="text-sm text-gray-600">Berkas saat ini: 
                                        <a href="{{ Storage::url($denda->berkas_path) }}" target="_blank" class="text-blue-600 hover:underline">{{ basename($denda->berkas_path) }}</a>
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('denda_administratif.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700">
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
</x-app-layout>
