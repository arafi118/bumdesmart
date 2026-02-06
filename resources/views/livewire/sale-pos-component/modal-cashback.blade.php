<div class="modal fade" id="globalCashbackModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cashback Global</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" x-data>
                <div class="mb-3">
                    <div class="form-selectgroup w-100">
                        <label class="form-selectgroup-item">
                            <input type="radio" value="nominal" class="form-selectgroup-input"
                                x-model="globalCashback.jenis">
                            <span class="form-selectgroup-label w-100">Rp (Nominal)</span>
                        </label>
                        <label class="form-selectgroup-item">
                            <input type="radio" value="persen" class="form-selectgroup-input"
                                x-model="globalCashback.jenis">
                            <span class="form-selectgroup-label w-100">% (Persen)</span>
                        </label>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jumlah</label>
                    <input type="number" class="form-control" x-model="globalCashback.jumlah" placeholder="0">
                </div>
                <div class="text-end">
                    <span class="text-info fw-bold" x-text="formatRupiah(calculateGlobalCashbackValue())"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary w-100" data-bs-dismiss="modal">Simpan</button>
            </div>
        </div>
    </div>
</div>
