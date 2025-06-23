<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Pembatalan SPPT') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">Oops!</strong>
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('pembatalan.preview.update', $pembatalan->id) }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- NOP (Read-only) -->
                            <div>
                                <x-input-label for="nop" value="NOP" />
                                <x-text-input id="nop" class="block mt-1 w-full bg-gray-100" type="text" :value="$pembatalan->formatted_nop" disabled />
                            </div>

                            <!-- Tahun Pajak (Read-only) -->
                            <div>
                                <x-input-label for="thn_pajak_sppt" value="Tahun Pajak" />
                                <x-text-input id="thn_pajak_sppt" class="block mt-1 w-full bg-gray-100" type="text" :value="$pembatalan->thn_pajak_sppt" disabled />
                            </div>

                            <!-- Nama WP (Read-only) -->
                            <div class="md:col-span-2">
                                <x-input-label for="nm_wp_sppt" value="Nama Wajib Pajak" />
                                <x-text-input id="nm_wp_sppt" class="block mt-1 w-full bg-gray-100" type="text" :value="$pembatalan->nm_wp_sppt" disabled />
                            </div>

                            <!-- Nomor SK -->
                            <div>
                                <x-input-label for="nomor_sk" value="Nomor SK" />
                                <x-text-input id="nomor_sk" class="block mt-1 w-full" type="text" name="nomor_sk" :value="old('nomor_sk', $pembatalan->nomor_sk_raw)" required autofocus />
                            </div>

                            <!-- Tahun SK -->
                            <div>
                                <x-input-label for="tahun_sk" value="Tahun SK" />
                                <x-text-input id="tahun_sk" class="block mt-1 w-full" type="number" name="tahun_sk" :value="old('tahun_sk', $pembatalan->tahun_sk_raw)" required />
                            </div>

                            <!-- Tanggal SK -->
                            <div>
                                <x-input-label for="tgl_sk" value="Tanggal SK" />
                                <x-text-input id="tgl_sk" class="block mt-1 w-full" type="date" name="tgl_sk" :value="old('tgl_sk', $pembatalan->tgl_sk->format('Y-m-d'))" required />
                            </div>

                            <!-- Berkas -->
                            <div>
                                <x-input-label for="berkas" value="Upload Berkas Baru (PDF, Opsional)" />
                                <input id="berkas" class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" type="file" name="berkas" accept=".pdf">
                                <p class="mt-1 text-sm text-gray-500">Kosongkan jika tidak ingin mengubah berkas.</p>
                                @if($pembatalan->berkas_path)
                                <p class="mt-2 text-sm text-green-600">
                                    Berkas saat ini: <a href="{{ Storage::url($pembatalan->berkas_path) }}" target="_blank" class="font-medium text-blue-600 hover:underline">Lihat Berkas</a>
                                </p>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('pembatalan.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 mr-2">
                                Batal
                            </a>
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
