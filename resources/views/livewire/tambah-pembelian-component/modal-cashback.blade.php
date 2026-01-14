<div class="modal fade" id="cashbackModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Cashback {{ $product['nama_produk'] ?? '' }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Jenis Cashback</label>
                        <div class="form-selectgroup w-100">
                            <label class="form-selectgroup-item flex-grow-1">
                                <input type="radio" value="nominal" class="form-selectgroup-input"
                                    wire:model="product.cashback.jenis"
                                    {{ $product['cashback']['jenis'] == 'nominal' ? 'checked' : '' }} />
                                <span class="form-selectgroup-label">Nominal</span>
                            </label>
                            <label class="form-selectgroup-item flex-grow-1">
                                <input type="radio" value="persen" class="form-selectgroup-input"
                                    wire:model="product.cashback.jenis"
                                    {{ $product['cashback']['jenis'] == 'persen' ? 'checked' : '' }} />
                                <span class="form-selectgroup-label">Persen</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label>Jumlah Cashback</label>
                        <input wire:model="product.cashback.jumlah" type="text" x-mask:dynamic="$money($input)"
                            class="form-control" placeholder="Jumlah Cashback">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary ms-auto" wire:click="setCashbackProduct">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>
