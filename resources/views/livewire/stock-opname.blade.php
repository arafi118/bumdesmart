<div>
    <div class="tab-content">
        <div class="tab-pane fade show active" id="daftarStock">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-between mb-3">
                        <div class="col-md-3">
                            <input type="search" wire:model.live.debounce.300ms="search" class="form-control"
                                placeholder="🔍 Cari Stock...">
                        </div>
                        <div class="col-md-3">
                        </div>
                    </div>
                    <x-table :headers="$headers" :results="$stock">
                        @forelse ($stock as $stk)
                            <tr>
                                <td>
                                    {{ $loop->iteration + ($stock->currentPage() - 1) * $stock->perPage() }}
                                </td>
                                <td>{{ $stk->product->nama_produk }}</td>
                                <td>{{ $stk->tanggal_perubahan_stok }}</td>
                                <td>{{ $stk->jenis_perubahan }}</td>
                                <td>{{ $stk->jumlah_perubahan }}</td>
                                <td>{{ $stk->reference_id }}</td>
                                <td>{{ $stk->reference_type }}</td>
                                <td>{{ $stk->catatan }}</td>
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
                                                wire:click="edit({{ $stk->id }})">
                                                Edit
                                            </a>
                                            <a class="dropdown-item" href="#"
                                                wire:click="detailStock({{ $stk->id }})">
                                                Detail Stock
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item text-danger" href="#"
                                                wire:click="$dispatch('confirm-delete', {id: {{ $stk->id }}})">
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
    </div>
</div>
