<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pratinjau Pembatalan SPPT') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold mb-4">Konfirmasi Pembatalan SPPT</h3>

                    {{-- Tabel data yang akan diproses --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                             <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-2 py-3 text-left text-xs font-medium uppercase tracking-wider">NOP</th>
                                    <th class="px-2 py-3 text-left text-xs font-medium uppercase tracking-wider">Tahun</th>
                                    <th class="px-2 py-3 text-left text-xs font-medium uppercase tracking-wider">Nama WP</th>
                                    <th class="px-2 py-3 text-left text-xs font-medium uppercase tracking-wider">Alamat WP</th>
                                    <th class="px-2 py-3 text-left text-xs font-medium uppercase tracking-wider">Letak OP</th>
                                    <th class="px-2 py-3 text-left text-xs font-medium uppercase tracking-wider">Bumi (m²)</th>
                                    <th class="px-2 py-3 text-left text-xs font-medium uppercase tracking-wider">Bangunan (m²)</th>
                                    <th class="px-2 py-3 text-left text-xs font-medium uppercase tracking-wider">PBB Baku</th>
                                    <th class="px-2 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
                                    <th class="px-2 py-3 text-left text-xs font-medium uppercase tracking-wider">Pesan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($preview['data'] as $item)
                                    @php
                                        $rowClass = '';
                                        if ($item['status'] == 'Gagal') $rowClass = 'bg-red-50';
                                        if ($item['status'] == 'Lunas') $rowClass = 'bg-yellow-50';
                                        if ($item['status'] == 'Siap Diproses') $rowClass = 'bg-green-50';
                                    @endphp
                                    <tr class="{{ $rowClass }}">
                                        <td class="px-2 py-4 whitespace-nowrap text-sm">{{ $item['formatted_nop'] }}</td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm">{{ $item['thn_pajak_sppt'] }}</td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm">{{ $item['data_preview']['nm_wp_sppt'] ?? '-' }}</td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm">{{ $item['data_preview']['alamat_wp'] ?? '-' }}</td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm">{{ $item['data_preview']['alamat_op'] ?? '-' }}</td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm">{{ number_format($item['data_preview']['luas_bumi'] ?? 0, 0, ',', '.') }}</td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm">{{ number_format($item['data_preview']['luas_bangunan'] ?? 0, 0, ',', '.') }}</td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm">Rp {{ number_format($item['data_preview']['pbb_baku'] ?? 0, 0, ',', '.') }}</td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm font-semibold">{{ $item['status'] }}</td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm">{{ $item['message'] }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="10" class="text-center p-4">Tidak ada data untuk diproses.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- PERUBAHAN: Blok SK dan Ringkasan diatur posisinya --}}
                    <div class="mt-6 space-y-4">
                        {{-- Blok Detail SK --}}
                        <div class="p-4 border rounded-lg bg-gray-50 text-sm">
                            <p class="font-semibold">Detail SK Pembatalan:</p>
                            <ul class="list-disc list-inside ml-2">
                                <li><strong>Nomor SK:</strong> {{ $preview['no_sk'] }}</li>
                                <li><strong>Tanggal SK:</strong> {{ \Carbon\Carbon::parse($preview['tgl_sk'])->format('d-m-Y') }}</li>
                                <li><strong>Keterangan:</strong> "{{ $preview['keterangan'] }}"</li>
                            </ul>
                        </div>
                        
                        {{-- Blok Ringkasan Proses --}}
                        <div class="p-4 border rounded-lg bg-blue-50 border-blue-300 text-sm">
                            @php
                                $collection = collect($preview['data']);
                                $siapCount = $collection->where('status', 'Siap Diproses')->count();
                                $lunasCount = $collection->where('status', 'Lunas')->count();
                                $gagalCount = $collection->where('status', 'Gagal')->count();
                                $totalCount = $collection->count();
                            @endphp
                            <p class="font-semibold">Ringkasan Proses:</p>
                            <ul class="list-disc list-inside ml-2">
                                <li>Total NOP yang diajukan: <strong>{{ $totalCount }}</strong></li>
                                <li>Siap Diproses: <strong>{{ $siapCount }}</strong></li>
                                <li>Sudah Lunas (Hanya update Dafnom): <strong>{{ $lunasCount }}</strong></li>
                                <li>Gagal: <strong>{{ $gagalCount }}</strong></li>
                            </ul>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <a href="{{ route('pembatalan.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-400 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 mr-2">
                            Kembali
                        </a>
                        @if(collect($preview['data'])->contains(fn($i) => in_array($i['status'], ['Siap Diproses', 'Lunas'])))
                        <form action="{{ route('pembatalan.store') }}" method="POST">
                            @csrf
                            <x-primary-button>
                                {{ __('Konfirmasi & Simpan Pembatalan') }}
                            </x-primary-button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>