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

            <x-table :headers="$headers" :results="$sales" :sortColumn="$sortBy" :sortDirection="$sortDirection">
                @forelse ($sales as $sale)
                    @php
                        $totalDibayar = 0;
                        foreach ($sale->payments as $payment) {
                            $totalDibayar += $payment->total_harga;
                        }
                    @endphp

                    <tr>
                        <td>{{ $loop->iteration + ($sales->currentPage() - 1) * $sales->perPage() }}</td>
                        <td>{{ $sale->no_invoice }}</td>
                        <td>
                            {{ date('Y-m-d', strtotime($sale->tanggal_transaksi)) }}
                        </td>
                        <td>{{ $sale->customer->nama_pelanggan }}</td>
                        <td>
                            @if ($sale->status == 'completed')
                                <span class="badge text-light bg-success">Selesai</span>
                            @elseif ($sale->status == 'partial')
                                <span class="badge text-light bg-warning">Sebagian</span>
                            @elseif ($sale->status == 'pending')
                                <span class="badge text-light bg-danger">Pending</span>
                            @endif
                        </td>
                        <td>{{ number_format($sale->total) }}</td>
                        <td>{{ number_format($totalDibayar) }}</td>
                        <td>{{ number_format($sale->total - $totalDibayar) }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-info dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="material-symbols-outlined">
                                        more_vert
                                    </span>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#"
                                        wire:click="detailPenjualan({{ $sale->id }})">
                                        Detail Penjualan
                                    </a>
                                    <a class="dropdown-item" href="/penjualan/edit/{{ $sale->id }}">
                                        Edit
                                    </a>

                                    <a class="dropdown-item" href="#"
                                        wire:click="lihatPembayaran({{ $sale->id }})">
                                        Lihat Pembayaran
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    @if ($sale->total - $sale->payments->sum('total_harga') > 0)
                                        <a class="dropdown-item" href="#"
                                            wire:click="tambahPembayaran({{ $sale->id }})">
                                            Tambahkan Pembayaran
                                        </a>
                                    @endif
                                    <a class="dropdown-item" href="/penjualan/retur/{{ $sale->id }}">
                                        Retur Penjualan
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="#"
                                        wire:click="$dispatch('confirm-delete', {id: {{ $sale->id }}})">
                                        Hapus
                                    </a>
                                </div>
                            </div>
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
