<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Penghapusan Denda Administratif') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold mb-4">Edit Data Denda Administratif: {{ $denda->formatted_nop }} (Tahun {{ $denda->thn_pajak_sppt }})</h3>

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('denda_administratif.update', $denda->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH') {{-- Menggunakan method PATCH untuk update --}}

                        {{-- Field yang tidak bisa diedit --}}
                        <div class="mb-4">
                            <x-input-label for="nop_display" :value="__('NOP')" />
                            <x-text-input id="nop_display" class="block mt-1 w-full bg-gray-100" type="text" value="{{ $denda->formatted_nop }}" readonly />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="thn_pajak_sppt_display" :value="__('Tahun Pajak SPPT')" />
                            <x-text-input id="thn_pajak_sppt_display" class="block mt-1 w-full bg-gray-100" type="text" value="{{ $denda->thn_pajak_sppt }}" readonly />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="pokok_display" :value="__('Pokok')" />
                            <x-text-input id="pokok_display" class="block mt-1 w-full bg-gray-100" type="text" value="{{ number_format($denda->pokok ?? 0, 2, ',', '.') }}" readonly />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="denda_display" :value="__('Denda')" />
                            <x-text-input id="denda_display" class="block mt-1 w-full bg-gray-100" type="text" value="{{ number_format($denda->denda ?? 0, 2, ',', '.') }}" readonly />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="jumlah_pajak_display" :value="__('Jumlah Pajak (Pokok + Denda)')" />
                            <x-text-input id="jumlah_pajak_display" class="block mt-1 w-full bg-gray-100" type="text" value="{{ number_format($denda->jumlah_pajak ?? 0, 2, ',', '.') }}" readonly />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="sanksi_administratif_display" :value="__('Sanksi Administratif')" />
                            <x-text-input id="sanksi_administratif_display" class="block mt-1 w-full bg-gray-100" type="text" value="{{ number_format($denda->sanksi_administratif ?? 0, 2, ',', '.') }}" readonly />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="yang_harus_dibayar_display" :value="__('Yang Harus Dibayar')" />
                            <x-text-input id="yang_harus_dibayar_display" class="block mt-1 w-full bg-gray-100" type="text" value="{{ number_format($denda->yang_harus_dibayar ?? 0, 2, ',', '.') }}" readonly />
                        </div>

                        {{-- Field yang bisa diedit --}}
                        <div class="mb-4">
                            <x-input-label for="tgl_jatuh_tempo_baru" :value="__('Tanggal Jatuh Tempo Baru')" />
                            <x-text-input id="tgl_jatuh_tempo_baru" class="block mt-1 w-full" type="date" name="tgl_jatuh_tempo_baru" value="{{ old('tgl_jatuh_tempo_baru', $denda->tgl_jatuh_tempo_baru ? \Carbon\Carbon::parse($denda->tgl_jatuh_tempo_baru)->format('Y-m-d') : '') }}" required />
                            <x-input-error :messages="$errors->get('tgl_jatuh_tempo_baru')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="nomor_sk" :value="__('Nomor SK')" />
                            <x-text-input id="nomor_sk" class="block mt-1 w-full" type="text" name="nomor_sk" value="{{ old('nomor_sk', $denda->nomor_sk_raw ?? '') }}" required />
                            <x-input-error :messages="$errors->get('nomor_sk')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="tahun_sk" :value="__('Tahun SK')" />
                            <x-text-input id="tahun_sk" class="block mt-1 w-full" type="number" name="tahun_sk" value="{{ old('tahun_sk', $denda->tahun_sk_raw ?? '') }}" required min="2000" max="2100" />
                            <x-input-error :messages="$errors->get('tahun_sk')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="tgl_sk" :value="__('Tanggal SK')" />
                            <x-text-input id="tgl_sk" class="block mt-1 w-full" type="date" name="tgl_sk" value="{{ old('tgl_sk', $denda->tgl_sk ? \Carbon\Carbon::parse($denda->tgl_sk)->format('Y-m-d') : '') }}" required />
                            <x-input-error :messages="$errors->get('tgl_sk')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="berkas" :value="__('Upload Berkas Baru (PDF, Max 24MB)')" />
                            <input id="berkas" class="block mt-1 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" type="file" name="berkas" accept="application/pdf" />
                            <x-input-error :messages="$errors->get('berkas')" class="mt-2" />

                            @if($denda->berkas_path)
                                <div class="mt-2">
                                    <p class="text-sm text-gray-600">Berkas saat ini: 
                                        <a href="{{ Storage::url($denda->berkas_path) }}" target="_blank" class="text-blue-600 hover:underline">{{ basename($denda->berkas_path) }}</a>
                                        <input type="checkbox" name="remove_berkas" id="remove_berkas" class="ml-4 rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500">
                                        <label for="remove_berkas" class="ms-1 text-sm text-red-600">Hapus Berkas</label>
                                    </p>
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('denda_administratif.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-400 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-600 focus:outline-none focus:border-gray-600 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mr-2">
                                {{ __('Batal') }}
                            </a>
                            <x-primary-button>
                                {{ __('Simpan Perubahan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>