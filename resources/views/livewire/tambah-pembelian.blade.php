<div wire:ignore x-data="pembelianHandler()" x-on:update-products.window="products = $event.detail.products; calculateTotal()"
    x-on:add-product-client.window="addProduct($event.detail.product)">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Nomor Pembelian</label>
                    <input type="text" class="form-control" wire:model="nomorPembelian"
                        placeholder="Nomor Pembelian" />
                    @error('nomorPembelian')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tanggal Pembelian</label>
                    <input type="text" class="form-control litepicker" id="tanggalPembelian"
                        wire:model="tanggalPembelian" placeholder="Tanggal Pembelian" value="{{ $tanggalPembelian }}" />
                    @error('tanggalPembelian')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="col-md-4 mb-3" wire:ignore>
                    <label class="form-label">Supplier</label>
                    <select class="form-select" id="supplier" wire:model="supplier">
                        <option value=""></option>
                    </select>
                    @error('supplier')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <hr>

            <div class="mb-3" wire:ignore>
                <select class="form-select" id="searchProduct" wire:model="searchProduct">
                    <option value=""></option>
                </select>
            </div>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th width="3%">No</th>
                        <th width="22%">Nama Produk</th>
                        <th width="15%">Harga Satuan</th>
                        <th width="10%">Jumlah Beli</th>
                        <th width="15%">Diskon</th>
                        <th width="15%">Cashback</th>
                        <th width="15%">Subtotal</th>
                        <th width="5%">
                            <span class="material-symbols-outlined">
                                delete
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody x-data>
                    <template x-for="(product, index) in Object.values(products)" :key="product.id">
                        <tr>
                            <td x-text="index + 1"></td>
                            <td>
                                <span x-text="product.nama_produk"></span> - <span x-text="product.sku"></span>
                            </td>
                            <td>
                                <input type="text" class="form-control" x-model="product.harga_beli"
                                    x-on:input="updateRow(product.id)" x-mask:dynamic="$money($input)">
                            </td>
                            <td>
                                <input type="number" class="form-control" x-model="product.jumlah_beli"
                                    x-on:input="updateRow(product.id)">
                            </td>
                            <td>
                                <input type="text" class="form-control" x-on:click="openDiscountModal(product.id)"
                                    x-model="product.diskon.nominal" readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control" x-on:click="openCashbackModal(product.id)"
                                    x-model="product.cashback.nominal" readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control" x-model="product.subtotal" readonly>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger"
                                    x-on:click="removeProduct(product.id)">
                                    <span class="material-symbols-outlined">
                                        delete
                                    </span>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2">Total</th>
                        <th x-text="totalProducts.harga_beli"></th>
                        <th x-text="totalProducts.jumlah_beli"></th>
                        <th x-text="totalProducts.diskon"></th>
                        <th x-text="totalProducts.cashback"></th>
                        <th x-text="totalProducts.subtotal"></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>

            <hr>

            <div class="row">
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Jenis Diskon</label>
                            <div class="form-selectgroup w-100">
                                <label class="form-selectgroup-item flex-grow-1">
                                    <input type="radio" value="nominal" class="form-selectgroup-input"
                                        x-model="diskon.jenis" wire:model.defer="diskon.jenis" />
                                    <span class="form-selectgroup-label">Nominal</span>
                                </label>
                                <label class="form-selectgroup-item flex-grow-1">
                                    <input type="radio" value="persen" class="form-selectgroup-input"
                                        x-model="diskon.jenis" wire:model.defer="diskon.jenis" />
                                    <span class="form-selectgroup-label">Persen</span>
                                </label>
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">Jumlah Diskon</label>
                            <input type="text" class="form-control" x-mask:dynamic="$money($input)"
                                wire:model.defer="diskon.jumlah" x-model="diskon.jumlah"
                                placeholder="Jumlah Diskon" />
                            @error('diskon.jumlah')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Jenis Cashback</label>
                            <div class="form-selectgroup w-100">
                                <label class="form-selectgroup-item flex-grow-1">
                                    <input type="radio" value="nominal" class="form-selectgroup-input"
                                        x-model="cashback.jenis" wire:model.defer="cashback.jenis" />
                                    <span class="form-selectgroup-label">Nominal</span>
                                </label>
                                <label class="form-selectgroup-item flex-grow-1">
                                    <input type="radio" value="persen" class="form-selectgroup-input"
                                        x-model="cashback.jenis" wire:model.defer="cashback.jenis" />
                                    <span class="form-selectgroup-label">Persen</span>
                                </label>
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">Jumlah Cashback</label>
                            <input type="text" class="form-control" x-mask:dynamic="$money($input)"
                                wire:model.defer="cashback.jumlah" x-model="cashback.jumlah"
                                placeholder="Jumlah Cashback" />
                            @error('cashback.jumlah')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="row">
                        <div class="col-12 mb-3" wire:ignore>
                            <label class="form-label">Jenis Pajak</label>
                            <select class="form-select tom-select" id="jenisPajak" wire:model.defer="jenisPajak"
                                x-model="jenisPajak">
                                <option value="tidak ada" selected>Tidak Ada</option>
                                <option value="PPN">PPN</option>
                            </select>
                            @error('jenisPajak')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">Total</label>
                            <input type="text" class="form-control" readonly wire:model="total"
                                x-model="totalProducts.subtotal" placeholder="Total" />
                        </div>
                    </div>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea class="form-control" wire:model.blur="catatan" rows="6" placeholder="Catatan"></textarea>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <ol class="list-group list-group-numbered">
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <span class="fw-bold">Jumlah</span>
                                    </div>
                                    <span x-text="`${summary.items}(${summary.itemCount})`"></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <span class="fw-bold">Total</span>
                                    </div>
                                    <span x-text="`Rp. ${summary.subtotal}`"></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <span class="fw-bold text-danger">Pajak (+)</span>
                                    </div>
                                    <span x-text="`Rp. ${summary.orderTax}`"></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <span class="fw-bold text-primary">Diskon (-)</span>
                                    </div>
                                    <span x-text="`Rp. ${summary.orderDiscount}`"></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <span class="fw-bold">Cashback</span>
                                    </div>
                                    <span x-text="`Rp. ${summary.orderCashback}`"></span>
                                </li>
                            </ol>

                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold fs-3">Total Pembelian</span>
                                <span class="fw-bold fs-3" x-text="`Rp. ${summary.grandTotal}`"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3 flex-grow-1">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex flex-column justify-content-between h-100">
                                <div class="flex-grow-1">
                                    <div class="row">
                                        <div class="col-12 mb-3" wire:ignore>
                                            <label class="form-label">Jenis Pembayaran</label>
                                            <select class="form-select tom-select" id="jenisPembayaran"
                                                x-model="jenisPembayaran" wire:model.blur="jenisPembayaran">
                                                <option value="cash">Cash</option>
                                                <option value="transfer">Transfer</option>
                                            </select>
                                            @error('jenisPembayaran')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-12 mb-3" x-show="jenisPembayaran === 'transfer'" x-transition>
                                            <label class="form-label">No. Rekening</label>
                                            <input type="text" class="form-control" wire:model.blur="noRekening"
                                                placeholder="No. Rekening" />
                                            @error('noRekening')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-md-8 mb-3">
                                            <label class="form-label">Jumlah Bayar</label>
                                            <input type="text" class="form-control"
                                                x-mask:dynamic="$money($input)" x-model="bayar"
                                                x-on:keyup="calculateKembalian" placeholder="Jumlah Bayar" />
                                            @error('jumlahBayar')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Kembalian</label>
                                            <input type="text" class="form-control" x-model="kembali"
                                                placeholder="Kembalian" readonly />
                                            @error('kembalian')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <label class="form-check form-switch form-switch-3">
                                        <input class="form-check-input" type="checkbox" x-model="preOrder"
                                            wire:model.defer="preOrder">
                                        <span class="form-check-label">Pre Order</span>
                                    </label>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-primary" x-on:click="saveAll">
                                        Simpan
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('livewire.tambah-pembelian-component.modal-diskon')
    @include('livewire.tambah-pembelian-component.modal-cashback')
</div>

@section('script')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('pembelianHandler', () => ({
                products: @json((object) $products),
                diskon: @json($diskon),
                cashback: @json($cashback),
                totalProducts: @json($totalProducts),
                jenisPajak: @json($jenisPajak ?? 'tidak ada'),

                // Payment State
                jenisPembayaran: @json($jenisPembayaran ?? 'cash'),
                preOrder: false,
                bayar: 0,
                kembali: 0,

                // Summary State for the new card
                summary: {
                    items: 0,
                    itemCount: 0,
                    subtotal: 0,
                    orderTax: 0,
                    orderDiscount: 0,
                    orderCashback: 0,
                    grandTotal: 0
                },

                activeModalProductIndex: null,
                modalProduct: {
                    diskon: {
                        jenis: 'nominal',
                        jumlah: 0,
                        nominal: 0
                    },
                    cashback: {
                        jenis: 'nominal',
                        jumlah: 0,
                        nominal: 0
                    }
                },

                init() {
                    this.$watch('products', () => {
                        this.calculateTotal();
                    }, {
                        deep: true
                    });
                    this.$watch('diskon', () => {
                        this.calculateTotal();
                    }, {
                        deep: true
                    });
                    this.$watch('cashback', () => {
                        this.calculateTotal();
                    }, {
                        deep: true
                    });
                    this.$watch('jenisPajak', () => {
                        this.calculateTotal();
                    });
                },

                addProduct(product) {
                    let id = product.id;
                    if (this.products[id]) {
                        this.products[id].jumlah_beli++;
                        this.updateRow(id);
                    } else {
                        this.products[id] = {
                            id: product.id,
                            sku: product.sku,
                            nama_produk: product.nama_produk,
                            harga_beli: this.formatNumber(product.harga_beli),
                            jumlah_beli: 1,
                            diskon: {
                                jenis: 'nominal',
                                jumlah: 0,
                                nominal: 0
                            },
                            cashback: {
                                jenis: 'nominal',
                                jumlah: 0,
                                nominal: 0
                            },
                            subtotal: this.formatNumber(product.harga_beli)
                        };
                        this.calculateTotal();
                    }
                },

                removeProduct(id) {
                    delete this.products[id];
                    this.calculateTotal();
                },

                formatNumber(num) {
                    return new Intl.NumberFormat('en-US').format(num);
                },

                parseNumber(str) {
                    if (!str) return 0;
                    return parseFloat(String(str).replace(/,/g, '')) || 0;
                },

                updateRow(id) {
                    let product = this.products[id];
                    let hargaBeli = this.parseNumber(product.harga_beli);
                    let jumlahBeli = parseInt(product.jumlah_beli) || 0;
                    let diskon = this.parseNumber(product.diskon.nominal);
                    // Cashback does not affect the row subtotal (bill amount)
                    // let cashback = this.parseNumber(product.cashback.nominal);

                    // Subtotal = Price * Qty - Discount
                    let subtotal = (hargaBeli * jumlahBeli) - diskon;

                    // Update Alpine state
                    this.products[id].subtotal = this.formatNumber(subtotal);
                },

                calculateTotal() {
                    let totalHargaBeli = 0;
                    let totalJumlahBeli = 0;
                    let totalDiskon = 0;
                    let totalCashback = 0;
                    let totalSubtotal = 0;
                    let uniqueItems = 0;

                    for (let id in this.products) {
                        uniqueItems++;
                        let product = this.products[id];
                        let hargaBeli = this.parseNumber(product.harga_beli);
                        let jumlahBeli = parseInt(product.jumlah_beli) || 0;
                        let diskon = this.parseNumber(product.diskon.nominal);
                        let cashback = this.parseNumber(product.cashback.nominal);
                        let subtotal = (hargaBeli * jumlahBeli) -
                            diskon; // Cashback excluded from subtotal

                        totalHargaBeli += hargaBeli;
                        totalJumlahBeli += jumlahBeli;
                        totalDiskon += diskon;
                        totalCashback += cashback;
                        totalSubtotal += subtotal;
                    }

                    // 1. Calculate Global Discount
                    let globalDiscountAmount = 0;
                    let globalDiscountInput = this.parseNumber(this.diskon.jumlah);
                    if (this.diskon.jenis === 'nominal') {
                        globalDiscountAmount = globalDiscountInput;
                    } else {
                        globalDiscountAmount = (totalSubtotal * globalDiscountInput) / 100;
                    }

                    // 2. Calculate Global Cashback
                    let globalCashbackAmount = 0;
                    let globalCashbackInput = this.parseNumber(this.cashback.jumlah);
                    if (this.cashback.jenis === 'nominal') {
                        globalCashbackAmount = globalCashbackInput;
                    } else {
                        // Cashback normally calculated on subtotal (after item discount)
                        globalCashbackAmount = (totalSubtotal * globalCashbackInput) / 100;
                    }

                    // 3. Calculate Tax (PPN 11%)
                    // Base for tax: (Subtotal - Global Discount) 
                    // Cashback is irrelevant for Tax base usually (it's a marketing expense, not price reduction)
                    let runningTotal = totalSubtotal - globalDiscountAmount;
                    let taxAmount = 0;
                    if (this.jenisPajak === 'PPN') {
                        taxAmount = runningTotal * 0.11;
                    }

                    // 4. Grand Total
                    // Total to Pay = Subtotal - GlobalDiscount + Tax
                    // Cashback is NOT subtracted from what user pays now.
                    let grandTotal = runningTotal + taxAmount;

                    // Update Objects
                    this.totalProducts = {
                        harga_beli: this.formatNumber(totalHargaBeli),
                        jumlah_beli: totalJumlahBeli,
                        diskon: this.formatNumber(totalDiskon),
                        cashback: this.formatNumber(totalCashback),
                        subtotal: this.formatNumber(totalSubtotal)
                    };

                    this.summary = {
                        items: uniqueItems,
                        itemCount: totalJumlahBeli,
                        subtotal: this.formatNumber(totalSubtotal),
                        orderDiscount: this.formatNumber(globalDiscountAmount),
                        orderCashback: this.formatNumber(
                            globalCashbackAmount), // Displayed but not in Math
                        orderTax: this.formatNumber(taxAmount),
                        grandTotal: this.formatNumber(grandTotal)
                    };

                    // Sync to Livewire
                    @this.set('total', this.formatNumber(grandTotal), true);

                    // Recalculate Change
                    this.calculateKembalian();
                },

                calculateKembalian() {
                    let totalToPay = this.parseNumber(this.summary.grandTotal);
                    let bayar = this.parseNumber(this.bayar);

                    if (bayar >= totalToPay) {
                        this.kembali = this.formatNumber(bayar - totalToPay);
                    } else {
                        this.kembali = 0;
                    }
                },

                openDiscountModal(id) {
                    this.activeModalProductIndex = id;
                    // Deep copy to avoid direct mutation before save
                    this.modalProduct = JSON.parse(JSON.stringify(this.products[id]));

                    $('#discountModal').modal('show');
                },

                saveDiscount() {
                    if (this.activeModalProductIndex !== null) {
                        let product = this.modalProduct;
                        let hargaBeli = this.parseNumber(product.harga_beli);
                        let jumlahBeli = parseInt(product.jumlah_beli) || 0;
                        let jumlahDiskon = this.parseNumber(product.diskon.jumlah);

                        let nominalDiskon = 0;
                        if (product.diskon.jenis === 'nominal') {
                            nominalDiskon = jumlahDiskon;
                        } else {
                            nominalDiskon = (hargaBeli * jumlahBeli * jumlahDiskon) / 100;
                        }

                        // Update local object
                        product.diskon.nominal = this.formatNumber(nominalDiskon);

                        // Commit to main products list
                        this.products[this.activeModalProductIndex].diskon = product.diskon;
                        this.products[this.activeModalProductIndex].diskon.nominal = this.formatNumber(
                            nominalDiskon);

                        this.updateRow(this.activeModalProductIndex);
                        this.calculateTotal();

                        // Close Modal
                        $('#discountModal').modal('hide');
                    }
                },

                openCashbackModal(id) {
                    this.activeModalProductIndex = id;
                    this.modalProduct = JSON.parse(JSON.stringify(this.products[id]));

                    $('#cashbackModal').modal('show');
                },

                saveCashback() {
                    if (this.activeModalProductIndex !== null) {
                        let product = this.modalProduct;
                        let hargaBeli = this.parseNumber(product.harga_beli);
                        let jumlahBeli = parseInt(product.jumlah_beli) || 0;
                        let jumlahCashback = this.parseNumber(product.cashback.jumlah);

                        let nominalCashback = 0;
                        if (product.cashback.jenis === 'nominal') {
                            nominalCashback = jumlahCashback;
                        } else {
                            nominalCashback = (hargaBeli * jumlahBeli * jumlahCashback) / 100;
                        }

                        // Update local object
                        product.cashback.nominal = this.formatNumber(nominalCashback);

                        // Commit to main products list
                        this.products[this.activeModalProductIndex].cashback = product.cashback;
                        this.products[this.activeModalProductIndex].cashback.nominal = this
                            .formatNumber(nominalCashback);

                        this.updateRow(this.activeModalProductIndex);
                        this.calculateTotal();

                        // Close Modal
                        $('#cashbackModal').modal('hide');
                    }
                },

                saveAll() {
                    @this.call('saveAll', {
                        products: this.products,
                        diskon: this.diskon,
                        cashback: this.cashback,
                        totalProducts: this.totalProducts,
                        total: this.totalProducts.subtotal,
                        jumlahBayar: this.parseNumber(this.bayar),
                        kembalian: this.parseNumber(this.kembali),
                        jenisPembayaran: this.jenisPembayaran,
                        preOrder: this.preOrder
                    });
                }
            }));
        });

        window.addEventListener('livewire:initialized', () => {
            new TomSelect('#supplier', {
                valueField: 'id',
                labelField: 'nama_supplier',
                searchField: ['nama_supplier'],
                maxOptions: 200,
                load: function(query, callback) {
                    if (!query || query.length < 2) {
                        return callback();
                    }

                    @this.call('loadSuppliers', query, 0)
                        .then(result => {
                            callback(result.data);
                        })
                        .catch(() => {
                            callback();
                        });
                },

                onChange: function(value) {
                    @this.set('supplier', value);
                }
            });

            new TomSelect('#searchProduct', {
                valueField: 'id',
                labelField: 'nama_produk',
                searchField: ['nama_produk'],
                maxOptions: 200,
                render: {
                    option: function(data, escape) {
                        return `<div class="list-group-item">
                                <div class="row">
                                    <div class="col-auto">
                                        <span class="avatar avatar-1" style="background-image: url(${'/storage/' + data.product.gambar})"></span>
                                    </div>
                                    <div class="col text-truncate">
                                        <div class="text-body d-block">${data.product.nama_produk}</div>
                                        <div class="text-secondary text-truncate mt-n1">${data.product.sku}</div>
                                    </div>
                                </div>
                            </div>`;
                    }
                },
                load: function(query, callback) {
                    if (!query || query.length < 2) {
                        return callback();
                    }

                    @this.call('loadSearchProducts', query, 0)
                        .then(result => {
                            callback(result.data);
                        })
                        .catch(() => {
                            console.log('error')
                            callback();
                        });
                },

                onChange: function(value) {
                    const selectedOption = this.options[value];
                    this.clear();
                    this.clearOptions();

                    if (selectedOption) {
                        window.dispatchEvent(new CustomEvent('add-product-client', {
                            detail: {
                                product: selectedOption.product
                            }
                        }));
                    }
                },
            });



            // Update Alpine data when Livewire updates (e.g. after adding product)
            Livewire.on('products-updated', (data) => {
                // We need a way to reach the Alpine component scope.
                // A clean way is using $dispatch('update-products', { products: ... }) from PHP
                // and x-on:update-products.window="products = $event.detail.products"
            });

            var jenisPembayaranValue = '';
            document.querySelector('#jenisPembayaran').addEventListener('change', function() {
                if (jenisPembayaranValue != this.value) {
                    @this.call('resetNoRekening');
                    jenisPembayaranValue = this.value;
                }
            });
        })
    </script>
@endsection
