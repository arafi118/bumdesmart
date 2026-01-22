<div class="modal fade" id="tambahPembayaranModal" tabindex="-1" role="dialog" aria-hidden="true" x-data="tambahPembayaran"
    x-on:shown-bs-modal.dot="initModal" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Pembayaran</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Detail Tagihan Info -->
                    <div class="col-12 mb-3">
                        <div class="alert alert-info d-flex justify-content-between align-items-center">
                            <span>Sisa Tagihan: <strong x-text="formatRupiah(sisaTagihan)"></strong></span>
                            <span>Total Pembelian: <strong x-text="formatRupiah(totalTagihan)"></strong></span>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nomor Pembayaran</label>
                        <input type="text" class="form-control" wire:model="nomorPembayaran"
                            placeholder="Nomor Pembayaran" />
                        <small class="text-muted">Otomatis diisi jika kosong</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Pembayaran</label>
                        <input type="text" class="form-control" id="tanggalPembayaran" wire:model="tanggalPembayaran"
                            placeholder="Tanggal Pembayaran" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Sudah Dibayar</label>
                        <input type="text" class="form-control" x-model="formattedSudahDibayar" readonly />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jumlah Pembayaran</label>
                        <input type="text" class="form-control" x-model="formattedJumlahPembayaran"
                            x-on:input="updateJumlah" placeholder="0" />
                        <div class="d-flex justify-content-between mt-2">
                            <small>Kembalian</small>
                            <small class="fw-bold text-success" x-text="formatRupiah(kembalian)"></small>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" rows="3" wire:model="keterangan" placeholder="Keterangan"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary ms-auto" wire:click="simpanPembayaran">
                    Simpan Pembayaran
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('tambahPembayaran', () => ({
                sisaTagihan: 0,
                totalTagihan: 0,
                sudahDibayar: 0,
                jumlahPembayaran: 0,
                formattedJumlahPembayaran: '',
                formattedSudahDibayar: '',
                kembalian: 0,
                picker: null,

                initModal() {
                    // Sync initial data from Livewire
                    this.sisaTagihan = @this.sisaTagihan;
                    this.sudahDibayar = @this.sudahDibayar;
                    this.totalTagihan = this.sisaTagihan + this.sudahDibayar;

                    this.formattedSudahDibayar = this.formatRupiah(this.sudahDibayar);
                    this.formattedJumlahPembayaran = '';
                    this.jumlahPembayaran = 0;
                    this.kembalian = 0;

                    this.initDatePicker();
                },

                initDatePicker() {
                    if (this.picker) {
                        this.picker.destroy();
                    }
                    this.picker = new Litepicker({
                        element: document.getElementById('tanggalPembayaran'),
                        format: 'YYYY-MM-DD',
                        autoRefresh: true,
                        setup: (picker) => {
                            picker.on('selected', (date) => {
                                @this.set('tanggalPembayaran', date.format(
                                    'YYYY-MM-DD'));
                            });
                        },
                    });
                },

                updateJumlah(e) {
                    let value = e.target.value.replace(/[^0-9]/g, '');
                    this.jumlahPembayaran = parseInt(value) || 0;
                    this.formattedJumlahPembayaran = this.formatNumber(this.jumlahPembayaran);

                    // Logic Kembalian
                    this.kembalian = Math.max(0, this.jumlahPembayaran - this.sisaTagihan);

                    // Sync to Livewire
                    @this.set('jumlahPembayaran', this.jumlahPembayaran);
                },

                formatNumber(number) {
                    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                },

                formatRupiah(number) {
                    return new Intl.NumberFormat('en-US').format(number || 0);
                }
            }))
        })
    </script>
</div>
