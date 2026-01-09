<div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-between mb-3">
                <div class="col-md-3">
                    <input type="search" wire:model.live.debounce.300ms="search" class="form-control"
                        placeholder="🔍 Cari rak...">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary w-100" wire:click="create">
                        <i class="fas fa-plus"></i> Tambah Rak
                    </button>
                </div>
            </div>

            <x-table :headers="$headers" :results="$shelves" :sortColumn="$sortBy" :sortDirection="$sortDirection">
                @forelse ($shelves as $shelf)
                    <tr>
                        <td>
                            {{ $loop->iteration + ($shelves->currentPage() - 1) * $shelves->perPage() }}
                        </td>
                        <td>{{ $shelf->kode_rak }}</td>
                        <td>{{ $shelf->nama_rak }}</td>
                        <td>{{ $shelf->lokasi }}</td>
                        <td>{{ $shelf->kapasitas }}</td>
                        <td>{{ $shelf->aktif ? 'Ya' : 'Tidak' }}</td>
                        <td>
                            <button class="btn btn-sm btn-primary" wire:click="edit({{ $shelf->id }})">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-danger"
                                wire:click="$dispatch('confirm-delete', {id: {{ $shelf->id }}})">
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

    <div class="modal fade" id="rakModal" tabindex="-1" role="dialog" aria-hidden="true">
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
                                <label class="form-label">Kode Rak</label>
                                <input type="text" class="form-control" wire:model="kodeRak"
                                    placeholder="Kode Rak" />
                                @error('kodeRak')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Rak</label>
                                <input type="text" class="form-control" wire:model="namaRak"
                                    placeholder="Nama Rak" />
                                @error('namaRak')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Lokasi</label>
                                <textarea class="form-control" wire:model="lokasi" placeholder="Lokasi"></textarea>
                                @error('lokasi')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kapasitas Maksimal</label>
                                <input type="number" class="form-control" wire:model="kapasitasMaksimal"
                                    placeholder="Kapasitas Maksimal" />
                                @error('kapasitasMaksimal')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Aktif</label>
                                <div class="form-selectgroup">
                                    <label class="form-selectgroup-item">
                                        <input type="radio" value="1" class="form-selectgroup-input"
                                            wire:model="aktif" {{ $aktif != 0 ? 'checked' : '' }} />
                                        <span class="form-selectgroup-label">Ya</span>
                                    </label>
                                    <label class="form-selectgroup-item">
                                        <input type="radio" value="0" class="form-selectgroup-input"
                                            wire:model="aktif" {{ $aktif == 0 ? 'checked' : '' }} />
                                        <span class="form-selectgroup-label">Tidak</span>
                                    </label>
                                </div>
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
