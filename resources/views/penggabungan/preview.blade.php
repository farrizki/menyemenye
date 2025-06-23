<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pratinjau Penggabungan SPPT') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold mb-4">Konfirmasi Penggabungan SPPT</h3>

                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                             <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NOP</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama WP</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PBB Baku</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Pratinjau</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pesan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($preview['data'] as $item)
                                    <tr class="@if($item['status'] == 'Gagal') bg-red-50 @elseif($item['status'] == 'Lunas') bg-yellow-50 @else bg-blue-50 @endif">
                                        <td class="px-3 py-4 whitespace-nowrap text-sm">{{ $item['formatted_nop'] }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm">{{ $item['thn_pajak_sppt'] }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm">{{ $item['data_preview']['nm_wp_sppt'] ?? '-' }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-right font-semibold">Rp {{ number_format($item['data_preview']['pbb_baku'] ?? 0, 0, ',', '.') }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($item['status'] == 'Gagal') bg-red-100 text-red-800 @endif
                                                @if($item['status'] == 'Lunas') bg-yellow-100 text-yellow-800 @endif
                                                @if($item['status'] == 'Siap Diproses') bg-blue-100 text-blue-800 @endif
                                            ">{{ $item['status'] }}</span>
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-600">{{ $item['message'] }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center p-4">Tidak ada data untuk diproses.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-6 border-t pt-4">
                        <div class="p-4 border rounded-lg bg-gray-50">
                            <p class="font-semibold">Detail Proses:</p>
                            <ul class="list-disc list-inside ml-2 mt-1">
                                <li><strong>Keterangan:</strong> "{{ $preview['keterangan'] }}"</li>
                            </ul>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <a href="{{ route('penggabungan.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 mr-2">
                            Kembali
                        </a>
                        @if(collect($preview['data'])->contains(fn($i) => in_array($i['status'], ['Siap Diproses', 'Lunas'])))
                        <form action="{{ route('penggabungan.store') }}" method="POST">
                            @csrf
                            <x-primary-button>{{ __('Konfirmasi & Simpan Penggabungan') }}</x-primary-button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>