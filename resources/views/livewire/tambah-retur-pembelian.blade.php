<div wire:ignore x-data="returPembelianHandler()" x-init="initData(@js($purchase))">
    <div class="card mb-3">
        <div class="card-body">
            <ul class="list-group ">
                <li class="list-group-item border-0 p-2 ps-0 pt-0">
                    <strong>Tanggal :</strong>
                    <span>
                        {{ $purchase->tanggal_pembelian }}
                    </span>
                </li>
                <li class="list-group-item border-0 p-2 ps-0 pt-0">
                    <strong>No. Pembelian :</strong>
                    <span>{{ $purchase->no_pembelian }}</span>
                </li>
                <li class="list-group-item border-0 p-2 ps-0 pt-0">
                    <strong>Status :</strong>
                    <span>
                        @if ($purchase->status == 'completed')
                            <span class="badge text-light bg-success">Selesai</span>
                        @elseif ($purchase->status == 'partial')
                            <span class="badge text-light bg-warning">Sebagian</span>
                        @elseif ($purchase->status == 'pending')
                            <span class="badge text-light bg-danger">Pending</span>
                        @endif
                    </span>
                </li>
            </ul>

            <div class="row justify-content-between mt-3">
                <div class="col-md-3">
                    <div class="fw-bold">Supplier :</div>
                    <div>{{ $purchase->supplier->nama_supplier }}</div>
                    <div>{{ $purchase->supplier->no_hp }}</div>
                    <div>{{ $purchase->supplier->alamat }}</div>
                </div>
                <div class="col-md-3">
                    <div class="fw-bold">Usaha :</div>
                    <div>{{ $purchase->business->nama_usaha }}</div>
                    <div>{{ $purchase->business->alamat }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-vcenter table-striped">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="25%">Nama Produk</th>
                            <th width="15%">Harga Satuan</th>
                            <th width="10%">Jumlah Pembelian</th>
                            <th width="10%">Diskon</th>
                            <th width="10%">Cashback</th>
                            <th width="10%">Jumlah Retur</th>
                            <th width="15%">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(purchaseDetail, index) in Object.values(purchase.purchase_details)"
                            :key="index">
                            <tr>
                                <td x-text="index + 1"></td>
                                <td>
                                    <div x-text="purchaseDetail.product.nama_produk"></div>
                                    <div x-text="purchaseDetail.product.sku"></div>
                                </td>
                                <td>
                                    <div x-text="formatRupiah(purchaseDetail.harga_satuan)"></div>
                                </td>
                                <td>
                                    <div x-text="purchaseDetail.jumlah"></div>
                                </td>
                                <td>
                                    <span x-text="formatRupiah(purchaseDetail.jumlah_diskon)"></span>
                                    <span x-show="purchaseDetail.jenis_diskon === 'persen'">%</span>
                                </td>
                                <td>
                                    <span x-text="formatRupiah(purchaseDetail.jumlah_cashback)"></span>
                                    <span x-show="purchaseDetail.jenis_cashback === 'persen'">%</span>
                                </td>
                                <td>
                                    <input type="number" class="form-control" x-model="purchaseDetail.jumlah_retur"
                                        x-on:focus="$el.select()" :max="purchaseDetail.jumlah">
                                </td>
                                <td>
                                    <span
                                        x-text="formatRupiah(purchaseDetail.harga_satuan * purchaseDetail.jumlah_retur)"></span>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="Object.keys(purchase.purchase_details).length === 0">
                            <td colspan="8" class="text-center text-muted py-4">
                                <i>Belum ada produk yang dipilih</i>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@section('script')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('returPembelianHandler', () => ({
                purchase: {},
                initData(data) {
                    this.purchase = data;
                },

                // --- Helpers ---
                formatRupiah(num) {
                    return new Intl.NumberFormat('en-US').format(num || 0);
                },

                parseMoney(str) {
                    if (!str) return 0;
                    // Remove commas, keep dots and numbers
                    return parseFloat(String(str).replace(/,/g, '')) || 0;
                },

                parseFormatted(val) {
                    if (typeof val === 'number') return val;
                    // Remove commas for standard float parsing
                    return parseFloat(String(val).replace(/,/g, '')) || 0;
                },
            }))
        })
    </script>
@endsection
