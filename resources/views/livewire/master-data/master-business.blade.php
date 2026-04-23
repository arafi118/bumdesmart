<div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-between mb-3">
                <div class="col-md-3">
                    <input type="search" wire:model.live.debounce.300ms="search" class="form-control"
                        placeholder="🔍 Cari business/owner...">
                </div>
                <div class="col-md-3">
                    @if (count($ownersList) > 0)
                        <button class="btn btn-primary w-100" wire:click="create">
                            <i class="fas fa-plus"></i> Tambah Business
                        </button>
                    @else
                        <button class="btn btn-secondary w-100" disabled title="Tambahkan owner terlebih dahulu">
                            <i class="fas fa-plus"></i> Tambah Business
                        </button>
                    @endif
                </div>
            </div>

            @if (count($ownersList) === 0)
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Belum ada owner terdaftar. <a href="/master/owner">Tambah owner terlebih dahulu</a> sebelum membuat
                    business.
                </div>
            @endif

            <x-table :headers="$headers" :results="$businesses" :sortColumn="$sortBy" :sortDirection="$sortDirection">
                @forelse ($businesses as $business)
                    <tr>
                        <td>{{ $loop->iteration + ($businesses->currentPage() - 1) * $businesses->perPage() }}</td>
                        @if (!$isContextual)
                            <td>{{ optional($business->owner)->nama_usaha }}</td>
                        @endif
                        <td>{{ $business->nama_usaha }}</td>
                        <td>{{ $business->email }}</td>
                        <td>{{ $business->no_telp }}</td>
                        <td>{{ $business->alamat }}</td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-info" wire:click="openImport({{ $business->id }})">
                                    <i class="fas fa-file-import"></i> Import
                                </button>
                                <button class="btn btn-sm btn-danger"
                                    wire:click="$dispatch('confirm-delete', {id: {{ $business->id }}})">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
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

    <!-- Modal Form -->
    <div class="modal fade" id="masterBusinessModal" tabindex="-1" role="dialog" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ $titleModal }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="store">
                        <div class="row">

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Owner / Principal <span class="text-danger">*</span></label>
                                @if ($isContextual)
                                    <div class="form-control bg-light">
                                        <strong>{{ $ownersList[$ownerId] ?? 'Owner Tidak Ditemukan' }}</strong>
                                    </div>
                                @else
                                    <select class="form-select" wire:model="ownerId">
                                        <option value="">-- Pilih Owner --</option>
                                        @foreach ($ownersList as $owId => $owName)
                                            <option value="{{ $owId }}">{{ $owName }}</option>
                                        @endforeach
                                    </select>
                                @endif
                                @error('ownerId')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Business (Toko/Usaha) <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" wire:model="businessName"
                                    placeholder="Nama Business" />
                                @error('businessName')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" wire:model="email" placeholder="Email" />
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. Telepon / HP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" wire:model="phone"
                                    placeholder="No HP/Telp" />
                                @error('phone')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                                <textarea class="form-control" wire:model="address" placeholder="Alamat lengkap" rows="3"></textarea>
                                @error('address')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <hr>
                            <h5>Default Admin/Owner User (Opsional)</h5>
                            <p class="text-muted small">Jika dikosongkan, sistem akan membuatkan username default dan
                                password "password".</p>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" wire:model="username"
                                    placeholder="Username admin" />
                                @error('username')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" wire:model="password"
                                    placeholder="Password admin" />
                                @error('password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary ms-auto" wire:click="store">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Import --}}
    <div wire:ignore.self class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Data Toko (Produk & Stok)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pilih File (Excel / CSV)</label>
                        <input type="file" class="form-control" wire:model="importFile" accept=".csv,.xlsx,.xls">
                        <div wire:loading wire:target="importFile" class="text-info small">Memproses file...</div>
                        @error('importFile')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle me-2"></i>
                        Pastikan file Anda mengikuti format yang benar. 
                        <a href="javascript:void(0)" wire:click="downloadTemplate" class="fw-bold text-decoration-underline">Unduh Template Disini</a>
                    </div>

                    @if ($importStep === 'processing')
                        <div class="text-center py-3">
                            <div class="spinner-border text-primary mb-2"></div>
                            <p>Sedang mengimport data... Mohon tunggu.</p>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" wire:click="processImport"
                        wire:loading.attr="disabled" {{ !$importFile ? 'disabled' : '' }}>
                        Mulai Import
                    </button>
                </div>
            </div>
        </div>
    </div>

    @script
        <script>
            window.addEventListener('show-modal', event => {
                var myModal = new bootstrap.Modal(document.getElementById(event.detail.modalId));
                myModal.show();
            });
            window.addEventListener('hide-modal', event => {
                var myModalEl = document.getElementById(event.detail.modalId);
                var modal = bootstrap.Modal.getInstance(myModalEl);
                if (modal) modal.hide();
            });
        </script>
    @endscript
</div>
