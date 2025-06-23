<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pembatalan SPPT') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold mb-4">Form Pembatalan SPPT</h3>

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('pembatalan.preview') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Grup Input NOP --}}
                        <div class="border p-4 rounded-md mb-6">
                            <h4 class="font-semibold text-lg mb-4 border-b pb-2">1. Pilih Objek Pajak</h4>
                            <div class="mb-4">
                                <x-input-label for="input_type" :value="__('Cara Input NOP')" />
                                <select id="input_type" name="input_type" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" onchange="toggleInput()">
                                    <option value="nop_manual" @if(old('input_type', 'nop_manual') == 'nop_manual') selected @endif>Input NOP Manual</option>
                                    <option value="upload_excel" @if(old('input_type') == 'upload_excel') selected @endif>Upload File Excel</option>
                                </select>
                            </div>

                            <div id="manual_input_section" class="mb-4">
                                <x-input-label for="nop_manual" :value="__('NOP (Pisahkan dengan koma)')" />
                                <x-text-input id="nop_manual" class="block mt-1 w-full" type="text" name="nop_manual" :value="old('nop_manual')" placeholder="Contoh: 351801000100100010,351801000100100020" />
                                
                                <div class="mt-4">
                                    <x-input-label for="thn_pajak_sppt" :value="__('Tahun Pajak SPPT')" />
                                    <x-text-input id="thn_pajak_sppt" class="block mt-1 w-full" type="number" name="thn_pajak_sppt" :value="old('thn_pajak_sppt', date('Y'))" />
                                </div>
                            </div>

                            <div id="excel_input_section" class="mb-4" style="display:none;">
                                <x-input-label for="excel_file" :value="__('Upload File Excel (Kolom A: NOP, Kolom B: Tahun Pajak)')" />
                                <input id="excel_file" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" type="file" name="excel_file" accept=".xls,.xlsx" />
                                <p class="text-sm text-gray-500 mt-1">Pastikan baris pertama adalah header dan data dimulai dari baris kedua.</p>
                            </div>
                        </div>

                        {{-- Grup Informasi SK & Berkas --}}
                        <div class="border p-4 rounded-md mb-6">
                            <h4 class="font-semibold text-lg mb-4 border-b pb-2">2. Informasi SK & Berkas</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="nomor_sk" :value="__('Nomor SK')" />
                                    <x-text-input id="nomor_sk" class="block mt-1 w-full" type="text" name="nomor_sk" :value="old('nomor_sk')" required />
                                </div>
                                <div>
                                    <x-input-label for="tahun_sk" :value="__('Tahun SK')" />
                                    <x-text-input id="tahun_sk" class="block mt-1 w-full" type="number" name="tahun_sk" :value="old('tahun_sk', date('Y'))" required />
                                </div>
                            </div>
                            <div class="mt-4">
                                <x-input-label for="tgl_sk" :value="__('Tanggal SK')" />
                                <x-text-input id="tgl_sk" class="block mt-1 w-full" type="date" name="tgl_sk" :value="old('tgl_sk')" required />
                            </div>

                            <div class="mt-4">
                                <x-input-label for="berkas" :value="__('Upload Berkas (PDF, Max 24MB)')" />
                                <input id="berkas" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" type="file" name="berkas" accept="application/pdf" required />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
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
            function toggleInput() {
                const inputType = document.getElementById('input_type').value;
                const manualSection = document.getElementById('manual_input_section');
                const excelSection = document.getElementById('excel_input_section');
                if (inputType === 'nop_manual') {
                    manualSection.style.display = 'block';
                    excelSection.style.display = 'none';
                } else {
                    manualSection.style.display = 'none';
                    excelSection.style.display = 'block';
                }
            }
            document.addEventListener('DOMContentLoaded', toggleInput);
        </script>
    @endpush
</x-app-layout>
