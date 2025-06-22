<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Pembatalan SPPT') }}
        </h2>
    </x-slot>

    <div class="p-6 bg-white border-b border-gray-200">
        @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Riwayat Pembatalan</h3>
             <a href="#" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500">
                Cetak Laporan
             </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">NOP</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tahun</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama WP</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Baku</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No SK</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl Proses</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Operator</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Berkas</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($laporanPembatalan as $item)
                        <tr>
                            <td class="px-4 py-4 whitespace-nowrap text-sm">{{ $item->kd_propinsi.'.'.$item->kd_dati2.'.'.$item->kd_kecamatan.'.'.$item->kd_kelurahan.'.'.$item->kd_blok.'.'.$item->no_urut.'.'.$item->kd_jns_op }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm">{{ $item->thn_pajak_sppt }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm">{{ $item->nm_wp_sppt }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-right">{{ number_format($item->pbb_terhutang_sppt, 0, ',', '.') }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm">{{ $item->no_sk_pembatalan }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm">{{ $item->created_at->format('d-m-Y H:i') }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm">{{ $item->operator }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-center">
                                @if($item->berkas_path)
                                    <a href="{{ Storage::url($item->berkas_path) }}" target="_blank" title="Lihat Berkas">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500 hover:text-blue-700 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </a>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-center">
                                <a href="{{ route('pembatalan.edit', $item->id) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                                <a href="{{ route('pembatalan.cetakSinglePdf', $item->id) }}" class="text-blue-600 hover:text-blue-900 ml-2">Cetak</a>
                                <form action="{{ route('pembatalan.destroy', $item->id) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('Anda yakin ingin menghapus data pembatalan ini? Tindakan ini akan mencoba mengembalikan data SPPT di Oracle.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-6 py-4 text-center text-gray-500">Tidak ada data laporan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">{{ $laporanPembatalan->links() }}</div>
    </div>
</x-app-layout>
