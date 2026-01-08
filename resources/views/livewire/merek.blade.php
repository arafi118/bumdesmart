<div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-between mb-3">
                <div class="col-md-3">
                    <input type="search" wire:model.live.debounce.300ms="search" class="form-control"
                        placeholder="🔍 Cari merek...">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary w-100" wire:click="create">
                        <i class="fas fa-plus"></i> Tambah Merek
                    </button>
                </div>
            </div>

            <x-table :headers="$headers" :results="$brands" :sortColumn="$sortBy" :sortDirection="$sortDirection">
                @forelse ($brands as $brand)
                    <tr>
                        <td>
                            {{ $loop->iteration + ($brands->currentPage() - 1) * $brands->perPage() }}
                        </td>
                        <td>{{ $brand->nama_brand }}</td>
                        <td>{{ $brand->deskripsi }}</td>
                        <td>
                            <button class="btn btn-sm btn-primary" wire:click="edit({{ $brand->id }})">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-danger"
                                wire:click="$dispatch('confirm-delete', {id: {{ $brand->id }}})">
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

    <div class="modal fade" id="merekModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ $titleModal }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Nama Merek</label>
                                <input type="text" class="form-control" wire:model="namaMerek"
                                    placeholder="Nama Merek" />
                                @error('namaMerek')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-control" rows="3" wire:model="deskripsi" placeholder="Deskripsi"></textarea>
                                @error('deskripsi')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary ms-auto" data-bs-dismiss="modal" wire:click="store">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
