{{-- Informasi Jumlah Data --}}
<div class="mb-4 text-sm text-gray-600">
    Menampilkan {{ $laporanPengurangan->firstItem() }} - {{ $laporanPengurangan->lastItem() }} dari total {{ $laporanPengurangan->total() }} data.
</div>

{{-- Tabel Laporan --}}
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
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Baku</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Pengurangan</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengurangan (%)</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Pengurangan</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ketetapan Yang Harus Dibayar</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No SK</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl SK</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Proses</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Operator</th>
                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Berkas</th>
                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($laporanPengurangan as $data)
                <tr>
                    <td class="px-3 py-4 whitespace-nowrap text-sm">{{ $data->formatted_nop }}</td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm">{{ $data->thn_pajak_sppt }}</td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm">{{ $data->nm_wp_sppt ?? '-' }}</td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm">{{ $data->alamat_wp ?? '-' }}</td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm">{{ $data->letak_op ?? '-' }}</td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm text-right">{{ number_format($data->luas_bumi_sppt ?? 0, 0, ',', '.') }}</td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm text-right">{{ number_format($data->luas_bng_sppt ?? 0, 0, ',', '.') }}</td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm text-right">Rp {{ number_format($data->pbb_terhutang_sppt_lama ?? 0, 0, ',', '.') }}</td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm">{{ $data->jenis_pengurangan ?? '-' }}</td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm text-center">{{ number_format($data->persentase, 0) }}%</td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm text-right">Rp {{ number_format($data->jumlah_pengurangan_baru ?? 0, 0, ',', '.') }}</td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm text-right font-semibold">Rp {{ number_format($data->ketetapan_baru ?? 0, 0, ',', '.') }}</td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm">{{ $data->no_sk_pengurangan ?? '-' }}</td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm">{{ $data->tgl_sk_pengurangan ? \Carbon\Carbon::parse($data->tgl_sk_pengurangan)->format('d-m-Y') : '-' }}</td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm">{{ $data->created_at->format('d-m-Y H:i') }}</td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm">{{ $data->operator ?? '-' }}</td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm text-center">
                         @if($data->berkas_path)
                            <a href="{{ Storage::url($data->berkas_path) }}" target="_blank" class="text-gray-500 hover:text-blue-600" title="Lihat Berkas">
                                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                        @endif
                    </td>
                    <td class="px-3 py-4 whitespace-nowrap text-sm">
                        <div class="flex items-center justify-center space-x-3">
                            <a href="{{ route('pengurangan.cetak-single-pdf', $data->id) }}" target="_blank" class="text-gray-500 hover:text-blue-600" title="Cetak PDF">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2z"></path></svg>
                            </a>
                            <a href="{{ route('pengurangan.edit', $data->id) }}" class="text-gray-500 hover:text-green-600" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            <form action="{{ route('pengurangan.destroy', $data->id) }}" method="POST" onsubmit="return confirm('Anda yakin? Tindakan ini akan mengembalikan data SPPT di Oracle ke nilai semula.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-gray-500 hover:text-red-600" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="18" class="text-center p-4">Tidak ada data pengurangan yang tercatat.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Paginasi --}}
<div class="mt-4 flex justify-between items-center">
    <div>
        {{-- Kosong, karena info jumlah data sudah di atas --}}
    </div>
    <div>
        {{ $laporanPengurangan->appends(request()->query())->links() }}
    </div>
</div>