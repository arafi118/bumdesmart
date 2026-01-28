<div class="modal fade" id="editStockModal" tabindex="-1"
     data-bs-backdrop="static"
     data-bs-keyboard="false"
     wire:ignore.self>

    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Edit Stock Opname</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="text"
                               id="tanggal_edit"
                               class="form-control"
                               autocomplete="off"
                               wire:ignore>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Stok Sistem</label>
                        <input type="number"
                               class="form-control"
                               value="{{ $stok_sistem }}"
                               disabled>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Stok Fisik</label>
                        <input type="number"
                               class="form-control"
                               wire:model.live="stok_fisik">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Selisih</label>
                        <input type="number"
                               class="form-control"
                               value="{{ $selisih }}"
                               disabled>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Alasan</label>
                        <input type="text"
                               class="form-control"
                               wire:model.defer="alasan">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control"
                                  rows="3"
                                  wire:model.defer="catatan"></textarea>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="button"
                        class="btn btn-primary"
                        wire:click="update">
                    Simpan Perubahan
                </button>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('livewire:initialized', () => {
    let picker = null
    const modalEl = document.getElementById('editStockModal')
    const inputTanggal = document.getElementById('tanggal_edit')

    modalEl.addEventListener('shown.bs.modal', () => {
        if (picker) picker.destroy()
        picker = new Litepicker({
            element: inputTanggal,
            format: 'YYYY-MM-DD',
            autoApply: true,
            singleMode: true,
            setup: (p) => {
                p.on('selected', (date) => {
                    @this.set('tanggal_perubahan_stok', date.format('YYYY-MM-DD'))
                })
            }
        })
        inputTanggal.value = @this.get('tanggal_perubahan_stok') ?? ''
    })

    modalEl.addEventListener('hidden.bs.modal', () => {
        if (picker) picker.destroy()
        picker = null
    })
})
</script>
