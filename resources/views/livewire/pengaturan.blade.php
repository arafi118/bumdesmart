<div>
    <form wire:submit="updateSettings">
        <div class="row row-cards">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Bisnis / Toko</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label required">Nama Toko</label>
                            <input type="text" class="form-control @error('nama_toko') is-invalid @enderror"
                                wire:model="nama_toko">
                            @error('nama_toko')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Alamat Toko</label>
                            <textarea class="form-control @error('alamat_toko') is-invalid @enderror" wire:model="alamat_toko" rows="3"></textarea>
                            @error('alamat_toko')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" class="form-control @error('no_telp_toko') is-invalid @enderror"
                                wire:model="no_telp_toko">
                            @error('no_telp_toko')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control @error('email_toko') is-invalid @enderror"
                                wire:model="email_toko">
                            @error('email_toko')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <!-- Data Owner (Pusat) -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Pengelola Pusat (Owner)</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label required">Nama Perusahaan Pengelola</label>
                            <input type="text" class="form-control @error('nama_perusahaan') is-invalid @enderror"
                                wire:model="nama_perusahaan">
                            @error('nama_perusahaan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Logo</label>
                            <div class="d-flex align-items-center mb-2">
                                @if ($new_logo)
                                    <span class="avatar avatar-xl me-3"
                                        style="background-image: url({{ $new_logo->temporaryUrl() }})"></span>
                                @elseif ($logo)
                                    <span class="avatar avatar-xl me-3"
                                        style="background-image: url({{ asset('storage/' . $logo) }})"></span>
                                @else
                                    <span class="avatar avatar-xl me-3">No Logo</span>
                                @endif
                                <div>
                                    <input type="file" class="form-control @error('new_logo') is-invalid @enderror"
                                        wire:model="new_logo" accept="image/*">
                                    <small class="form-hint mt-1">Format: JPG, PNG. Maksimal 2MB.</small>
                                    @error('new_logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Domain Utama</label>
                            <!-- Note: User requested domain to be readonly -->
                            <input type="text" class="form-control disabled bg-light" wire:model="domain" readonly
                                disabled>
                            <small class="form-hint text-danger mt-1">
                                <span class="material-symbols-outlined"
                                    style="font-size: 14px; vertical-align: bottom;">info</span>
                                Domain utama tidak dapat diubah secara manual.
                            </small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Domain Alternatif</label>
                            <!-- Note: User requested domain alternatif to be readonly -->
                            <input type="text" class="form-control disabled bg-light" wire:model="domain_alternatif"
                                readonly disabled>
                            <small class="form-hint text-danger mt-1">
                                <span class="material-symbols-outlined"
                                    style="font-size: 14px; vertical-align: bottom;">info</span>
                                Domain alternatif tidak dapat diubah secara manual.
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 text-end">
                <button type="submit" class="btn btn-primary mt-2">
                    <span class="material-symbols-outlined me-2">save</span>
                    Simpan Pengaturan
                </button>
            </div>
        </div>
    </form>
</div>
