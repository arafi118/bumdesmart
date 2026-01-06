<div class="modal fade" id="roleModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-3 modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ $titleModal }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Nama Role</label>
                        <input type="text" class="form-control" wire:model="namaRole" placeholder="Nama Role" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" rows="3" wire:model="deskripsi" placeholder="Deskripsi"></textarea>
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
<!-- END MODAL -->
