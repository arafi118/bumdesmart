<div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-between mb-3">
                <div class="col-md-3">
                    <input type="search" wire:model.live.debounce.300ms="search" class="form-control"
                        placeholder="🔍 Cari role atau deskripsi...">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary w-100" wire:click="create">
                        <i class="fas fa-plus"></i> Tambah Role
                    </button>
                </div>
            </div>

            <x-table :headers="$headers" :results="$roles" :sortColumn="$sortBy" :sortDirection="$sortDirection">
                @forelse ($roles as $role)
                    <tr>
                        <td>{{ $loop->iteration + ($roles->currentPage() - 1) * $roles->perPage() }}</td>
                        <td>{{ $role->nama_role }}</td>
                        <td>{{ $role->deskripsi }}</td>
                        <td>
                            <button class="btn btn-sm btn-primary" wire:click="edit({{ $role->id }})">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-danger"
                                wire:click="$dispatch('confirm-delete', {id: {{ $role->id }}})">
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

    @include('livewire.role-component.modal-form')
</div>
