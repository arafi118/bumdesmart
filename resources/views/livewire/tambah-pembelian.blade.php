<div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Nomor Pembelian</label>
                    <input type="text" class="form-control" wire:model="nomorPembelian" placeholder="Nomor Pembelian" />
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
                <tbody>
                    @foreach ($products as $id => $product)
                        <tr wire:key="{{ $id }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                {{ $product['nama_produk'] }} - {{ $product['sku'] }}
                            </td>
                            <td>
                                <input type="text" class="form-control" x-mask:dynamic="$money($input)"
                                    wire:model.blur="products.{{ $id }}.harga_beli">
                            </td>
                            <td>
                                <input type="number" class="form-control"
                                    wire:model.blur="products.{{ $id }}.jumlah_beli">
                            </td>
                            <td>
                                <input type="text" class="form-control"
                                    wire:click="openModal({{ $id }}, 'discountModal')"
                                    wire:model.live="products.{{ $id }}.diskon.nominal" readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control"
                                    wire:click="openModal({{ $id }}, 'cashbackModal')"
                                    wire:model.live="products.{{ $id }}.cashback.nominal" readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control"
                                    wire:model.live="products.{{ $id }}.subtotal" readonly>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger"
                                    wire:click="$dispatch('delete-product', {id: {{ $product['id'] }}})">
                                    <span class="material-symbols-outlined">
                                        delete
                                    </span>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2">Total</th>
                        <th>
                            {{ number_format($totalProducts['harga_beli']) }}
                        </th>
                        <th>
                            {{ $totalProducts['jumlah_beli'] }}
                        </th>
                        <th>
                            {{ number_format($totalProducts['diskon']) }}
                        </th>
                        <th>
                            {{ number_format($totalProducts['cashback']) }}
                        </th>
                        <th>
                            {{ number_format($totalProducts['subtotal']) }}
                        </th>
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
                                        wire:model.live="diskon.jenis"
                                        {{ $diskon['jenis'] == 'nominal' ? 'checked' : '' }} />
                                    <span class="form-selectgroup-label">Nominal</span>
                                </label>
                                <label class="form-selectgroup-item flex-grow-1">
                                    <input type="radio" value="persen" class="form-selectgroup-input"
                                        wire:model.live="diskon.jenis"
                                        {{ $diskon['jenis'] == 'persen' ? 'checked' : '' }} />
                                    <span class="form-selectgroup-label">Persen</span>
                                </label>
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">Jumlah Diskon</label>
                            <input type="text" class="form-control" x-mask:dynamic="$money($input)"
                                wire:model.blur="jumlahDiskon" placeholder="Jumlah Diskon" />
                            @error('jumlahDiskon')
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
                                        wire:model.live="cashback.jenis"
                                        {{ $cashback['jenis'] == 'nominal' ? 'checked' : '' }} />
                                    <span class="form-selectgroup-label">Nominal</span>
                                </label>
                                <label class="form-selectgroup-item flex-grow-1">
                                    <input type="radio" value="persen" class="form-selectgroup-input"
                                        wire:model.live="cashback.jenis"
                                        {{ $cashback['jenis'] == 'persen' ? 'checked' : '' }} />
                                    <span class="form-selectgroup-label">Persen</span>
                                </label>
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">Jumlah Cashback</label>
                            <input type="text" class="form-control" x-mask:dynamic="$money($input)"
                                wire:model.blur="jumlahCashback" placeholder="Jumlah Cashback" />
                            @error('jumlahCashback')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="row">
                        <div class="col-12 mb-3" wire:ignore>
                            <label class="form-label">Jenis Pajak</label>
                            <select class="form-select tom-select" id="jenisPajak" wire:model.blur="jenisPajak">
                                <option value="tidak ada" selected>Tidak Ada</option>
                                <option value="PPN">PPN</option>
                            </select>
                            @error('jenisPajak')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">Total</label>
                            <input type="text" class="form-control" readonly wire:model.live="total"
                                placeholder="Total" />
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
                            <div class="row">
                                <div class="col-12 mb-3" wire:ignore>
                                    <label class="form-label">Jenis Pembayaran</label>
                                    <select class="form-select tom-select" id="jenisPembayaran"
                                        wire:model.blur="jenisPembayaran">
                                        <option value="cash" selected>Cash</option>
                                        <option value="transfer">Transfer</option>
                                    </select>
                                    @error('jenisPembayaran')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                @if ($jenisPembayaran == 'transfer')
                                    <div class="col-12 mb-3">
                                        <label class="form-label">No. Rekening</label>
                                        <input type="text" class="form-control" wire:model.blur="noRekening"
                                            placeholder="No. Rekening" />
                                        @error('noRekening')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                @endif

                                <div class="col-md-8 mb-3">
                                    <label class="form-label">Jumlah Bayar</label>
                                    <input type="text" class="form-control" x-mask:dynamic="$money($input)"
                                        wire:model.blur="jumlahBayar" placeholder="Jumlah Bayar" />
                                    @error('jumlahBayar')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Kembalian</label>
                                    <input type="text" class="form-control" wire:model.live="kembalian"
                                        placeholder="Kembalian" readonly />
                                    @error('kembalian')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <label class="form-check form-switch form-switch-3">
                                <input class="form-check-input" type="checkbox" wire:model="preOrder">
                                <span class="form-check-label">Pre Order</span>
                            </label>

                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-primary" wire:click="simpan">
                                    Simpan
                                </button>
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
                        console.log(selectedOption);
                        @this.call('addProduct', selectedOption.product);
                    }

                },
            });

            Livewire.on('delete-product', (event) => {
                Swal.fire({
                    title: 'Hapus Produk',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('removeProduct', event.id);
                    }
                });
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
