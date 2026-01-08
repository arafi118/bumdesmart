<div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-between mb-3">
                <div class="col-md-3">
                    <input type="search" wire:model.live.debounce.300ms="search" class="form-control"
                        placeholder="🔍 Cari satuan...">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary w-100" wire:click="create">
                        <i class="fas fa-plus"></i> Tambah Satuan
                    </button>
                </div>
            </div>

            <x-table :headers="$headers" :results="$units" :sortColumn="$sortBy" :sortDirection="$sortDirection">
                @forelse ($units as $unit)
                    <tr>
                        <td>
                            {{ $loop->iteration + ($units->currentPage() - 1) * $units->perPage() }}
                        </td>
                        <td>{{ $unit->nama_satuan }}</td>
                        <td>{{ $unit->inisial_satuan }}</td>
                        <td>{{ $unit->deskripsi }}</td>
                        <td>
                            {{ $unit->desimal > 0 ? 'Ya' : 'Tidak' }}
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary" wire:click="edit({{ $unit->id }})">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-danger"
                                wire:click="$dispatch('confirm-delete', {id: {{ $unit->id }}})">
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

    <div class="modal fade" id="satuanModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ $titleModal }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Satuan</label>
                                <input type="text" class="form-control" wire:model="namaSatuan"
                                    placeholder="Nama Satuan" />
                                @error('namaSatuan')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Inisial</label>
                                <input type="text" class="form-control" wire:model="inisialSatuan"
                                    placeholder="Inisial" />
                                @error('inisialSatuan')
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

                        <div class="d-flex justify-content-end">
                            <label class="form-check form-switch form-switch-3">
                                <input class="form-check-input" type="checkbox" wire:model.boolean="ijinkanDesimal" />
                                <span class="form-check-label">Ijinkan Desimal</span>
                            </label>
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
