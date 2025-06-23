<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if(isset($isEdit) && $isEdit)
                {{ __('Pratinjau Perubahan Pembatalan') }}
            @else
                {{ __('Pratinjau Pembatalan SPPT') }}
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold mb-4">
                        @if(isset($isEdit) && $isEdit)
                            Konfirmasi Perubahan Data
                        @else
                            Konfirmasi Pembatalan SPPT
                        @endif
                    </h3>

                    {{-- Variabel data diambil tergantung dari proses (create atau edit) --}}
                    @php
                        $dataSource = isset($isEdit) ? $editPreview['data'] : $preview['data'];
                    @endphp

                    {{-- Tabel data yang akan diproses --}}
                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                             <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NOP</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama WP</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat WP</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Letak OP</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bumi (m²)</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bangunan (m²)</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PBB Baku</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Pratinjau</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pesan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($dataSource as $item)
                                    @php
                                        $rowClass = '';
                                        if ($item['status'] == 'Gagal') $rowClass = 'bg-red-50';
                                        if ($item['status'] == 'Lunas') $rowClass = 'bg-yellow-50';
                                        if ($item['status'] == 'Siap Diproses') $rowClass = 'bg-blue-50';
                                        if ($item['status'] == 'Akan Diperbarui') $rowClass = 'bg-green-50'; // Warna untuk edit
                                    @endphp
                                    <tr class="{{ $rowClass }}">
                                        <td class="px-3 py-4 whitespace-nowrap text-sm">{{ $item['formatted_nop'] }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm">{{ $item['thn_pajak_sppt'] }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm">{{ $item['data_preview']['nm_wp_sppt'] ?? '-' }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm">{{ $item['data_preview']['alamat_wp'] ?? '-' }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm">{{ $item['data_preview']['alamat_op'] ?? '-' }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-right">{{ number_format($item['data_preview']['luas_bumi'] ?? 0, 0, ',', '.') }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-right">{{ number_format($item['data_preview']['luas_bangunan'] ?? 0, 0, ',', '.') }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-right font-semibold">Rp {{ number_format($item['data_preview']['pbb_baku'] ?? 0, 0, ',', '.') }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($item['status'] == 'Gagal') bg-red-100 text-red-800 @endif
                                                @if($item['status'] == 'Lunas') bg-yellow-100 text-yellow-800 @endif
                                                @if($item['status'] == 'Siap Diproses') bg-blue-100 text-blue-800 @endif
                                                @if($item['status'] == 'Akan Diperbarui') bg-green-100 text-green-800 @endif
                                            ">
                                                {{ $item['status'] }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-600">{{ $item['message'] }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="10" class="text-center p-4">Tidak ada data untuk diproses.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-6 border-t pt-4">
                        {{-- Bagian detail SK (lama vs baru) hanya untuk EDIT --}}
                        @if(isset($isEdit) && $isEdit)
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2 text-sm">
                            <div class="p-4 border rounded-lg bg-gray-50">
                                <p class="font-semibold">Detail SK Lama:</p>
                                <ul class="list-disc list-inside ml-2 mt-1">
                                    <li><strong>Nomor SK:</strong> {{ $existingData->no_sk }}</li>
                                    <li><strong>Tanggal SK:</strong> {{ $existingData->tgl_sk->format('d-m-Y') }}</li>
                                </ul>
                            </div>
                             <div class="p-4 border rounded-lg bg-green-50 border-green-300">
                                <p class="font-semibold">Detail Perubahan:</p>
                                <ul class="list-disc list-inside ml-2 mt-1">
                                    <li><strong>Nomor SK Baru:</strong> {{ $newSkDetails['no_sk'] }}</li>
                                    <li><strong>Tanggal SK Baru:</strong> {{ \Carbon\Carbon::parse($newSkDetails['tgl_sk'])->format('d-m-Y') }}</li>
                                    <li><strong>Berkas Baru:</strong> {{ session()->has('berkas_temp_path_update') ? 'File baru telah diunggah' : 'Tidak ada perubahan berkas' }}</li>
                                </ul>
                            </div>
                        </div>
                        @else
                        {{-- Bagian detail SK dan ringkasan untuk CREATE --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2 text-sm">
                            <div class="p-4 border rounded-lg bg-gray-50">
                                <p class="font-semibold">Detail SK Pembatalan:</p>
                                <ul class="list-disc list-inside ml-2 mt-1">
                                    <li><strong>Nomor SK:</strong> {{ $preview['no_sk'] }}</li>
                                    <li><strong>Tanggal SK:</strong> {{ \Carbon\Carbon::parse($preview['tgl_sk'])->format('d-m-Y') }}</li>
                                    <li><strong>Keterangan:</strong> "{{ $preview['keterangan'] }}"</li>
                                </ul>
                            </div>
                               <div class="p-4 border rounded-lg bg-blue-50 border-blue-300">
                                @php
                                    $collection = collect($preview['data']);
                                    $siapCount = $collection->where('status', 'Siap Diproses')->count();
                                    $lunasCount = $collection->where('status', 'Lunas')->count();
                                    $gagalCount = $collection->where('status', 'Gagal')->count();
                                    $totalCount = $siapCount + $lunasCount + $gagalCount;
                                @endphp
                                <p class="font-semibold">Ringkasan Proses:</p>
                                <ul class="list-disc list-inside ml-2 mt-1">
                                    <li>Total NOP yang diajukan: <strong>{{ $totalCount }}</strong></li>
                                    <li>Siap Diproses: <strong>{{ $siapCount }}</strong></li>
                                    <li>Sudah Lunas (Hanya Dafnom): <strong>{{ $lunasCount }}</strong></li>
                                    <li>Gagal: <strong>{{ $gagalCount }}</strong></li>
                                </ul>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="flex justify-end mt-6">
                        <a href="javascript:history.back()" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 mr-2">
                            Kembali
                        </a>
                        
                        {{-- Logika untuk tombol Simpan --}}
                        @if(isset($isEdit) && $isEdit)
                            <form action="{{ route('pembatalan.update', $existingData->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="no_sk" value="{{ $newSkDetails['no_sk'] }}">
                                <input type="hidden" name="tgl_sk" value="{{ $newSkDetails['tgl_sk'] }}">
                                <input type="hidden" name="keterangan_pembatalan" value="{{ $newSkDetails['keterangan'] }}">
                                <x-primary-button>{{ __('Konfirmasi & Simpan Perubahan') }}</x-primary-button>
                            </form>
                        @else
                            @if(collect($preview['data'])->contains(fn($i) => in_array($i['status'], ['Siap Diproses', 'Lunas'])))
                            <form action="{{ route('pembatalan.store') }}" method="POST">
                                @csrf
                                <x-primary-button>{{ __('Konfirmasi & Simpan Pembatalan') }}</x-primary-button>
                            </form>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
