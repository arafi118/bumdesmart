<div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-between mb-3">
                <div class="col-md-3">
                    <input type="search" wire:model.live.debounce.300ms="search" class="form-control"
                        placeholder="🔍 Cari no. pembelian, tanggal, supplier, atau status...">
                </div>
                <div class="col-md-3">
                    <a href="/pembelian/tambah" class="btn btn-primary w-100">
                        <i class="fas fa-plus"></i> Tambah Pembelian
                    </a>
                </div>
            </div>

            <x-table :headers="$headers" :results="$purchases" :sortColumn="$sortBy" :sortDirection="$sortDirection">
                @forelse ($purchases as $purchase)
                    @php
                        $totalDibayar = 0;
                        foreach ($purchase->payments as $payment) {
                            $totalDibayar += $payment->total_harga;
                        }
                    @endphp

                    <tr>
                        <td>{{ $loop->iteration + ($purchases->currentPage() - 1) * $purchases->perPage() }}</td>
                        <td>{{ $purchase->no_pembelian }}</td>
                        <td>{{ $purchase->tanggal_pembelian }}</td>
                        <td>{{ $purchase->supplier->nama_supplier }}</td>
                        <td>{{ $purchase->status }}</td>
                        <td>{{ number_format($purchase->total) }}</td>
                        <td>{{ number_format($totalDibayar) }}</td>
                        <td>{{ number_format($purchase->total - $totalDibayar) }}</td>
                        <td>
                            <a href="/pembelian/edit/{{ $purchase->id }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <button class="btn btn-sm btn-danger"
                                wire:click="$dispatch('confirm-delete', {id: {{ $purchase->id }}})">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers) }}" class="text-center text-muted">
                            <i class="fas fa-inbox fa-3x mb-2"></i>
                            <p>Tidak ada data yang ditemukan</p>
                        </td>
                    </tr>
                @endforelse
            </x-table>
        </div>
    </div>
</div>
