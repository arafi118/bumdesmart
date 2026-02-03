<div class="col-12">
    <div class="card" wire:ignore x-data="stockAdjustment()" x-init="init()">
        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">Nomor Penyesuaian</label>
                    <input type="text" class="form-control" x-model="nomorAdjustment">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Tanggal Penyesuaian</label>
                    <input type="text" id="tanggalAdjustment" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Jenis Penyesuaian</label>
                    <select id="jenisAdjustment" class="form-select">
                        <option value="">-- pilih --</option>
                        <option value="damaged">Barang Rusak</option>
                        <option value="expired">Kadaluarsa</option>
                        <option value="lost">Hilang/Dicuri</option>
                        <option value="found">Temuan Barang</option>
                        <option value="correction">Koreksi</option>
                        <option value="sample">Sample</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select id="statusAdjustment" class="form-select">
                        <option value="draft">Draft</option>
                        <option value="approved">Approved</option>
                        <option value="completed">Completed</option>
                        <option value="rejected">Rejected</option>
                        <option value="canceled">Canceled</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
            </div>

            <hr>

            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" placeholder="Cari produk..." x-model.debounce.500ms="search"
                        @input="cari()">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Stok Sistem</th>
                            <th>Stok Fisik</th>
                            <th>Selisih</th>
                            <th>Alasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in products" :key="item.id">
                            <tr>
                                <td x-text="index + 1 + ((page - 1) * 10)"></td>
                                <td x-text="item.nama_produk"></td>
                                <td>
                                    <input type="text" class="form-control" :value="formatRupiah(item.harga_satuan)"
                                        @input="updateHarga(item, $event.target.value)">
                                </td>
                                <td>
                                    <input type="number" class="form-control" :value="item.stok_sistem" readonly>
                                </td>
                                <td>
                                    <input type="number" class="form-control" x-model.number="item.stok_fisik"
                                        @input="hitung(item)">
                                </td>
                                <td>
                                    <input type="number" class="form-control" :value="item.selisih" readonly>
                                </td>
                                <td>
                                    <input type="text" class="form-control" x-model="item.alasan">
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-3">
                <button class="btn btn-outline-secondary" :disabled="page === 1" @click="prevPage()">Prev</button>
                <button class="btn btn-outline-secondary" :disabled="!hasMore" @click="nextPage()">Next</button>
            </div>

            <hr>

            <div class="row mt-3">
                <div class="col-md-8">
                    <label class="form-label">Catatan</label>
                    <textarea class="form-control" rows="3" x-model="catatan"></textarea>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-primary w-100" @click="simpan()">Simpan Adjustment</button>
                </div>
            </div>

        </div>
    </div>
</div>
@section('script')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('stockAdjustment', () => ({
        nomorAdjustment: '',
        tanggalAdjustment: '',
        jenisAdjustment: '',
        status: 'draft',
        catatan: '',
        search: '',
        page: 1,
        hasMore: false,
        products: [],

        tsJenis: null,
        tsStatus: null,

        init() {
            this.initTanggal()
            this.initJenis()
            this.initStatus()
            this.load()
        },

        initTanggal() {
            if (this._tanggalInit) return
            this._tanggalInit = true

            new Litepicker({
                element: document.getElementById('tanggalAdjustment'),
                format: 'YYYY-MM-DD',
                autoApply: true,
                singleMode: true,
                setup: p => p.on('selected', d => {
                    this.tanggalAdjustment = d.format('YYYY-MM-DD')
                })
            })
        },

        initJenis() {
            const el = document.getElementById('jenisAdjustment')
            if (!el || el.tomselect) {
                this.tsJenis = el?.tomselect
                return
            }

            this.tsJenis = new TomSelect(el, {
                persist: true,
                onChange: v => this.jenisAdjustment = v
            })
        },

        initStatus() {
            const el = document.getElementById('statusAdjustment')
            if (!el || el.tomselect) {
                this.tsStatus = el?.tomselect
                return
            }

            this.tsStatus = new TomSelect(el, {
                persist: true,
                onChange: v => this.status = v
            })
        },

        load() {
            @this.call('loadProducts', this.page, this.search).then(res => {
                this.products = res.data.map(p => ({
                    id: p.id,
                    nama_produk: p.nama_produk,
                    stok_sistem: Number(p.stok_aktual),
                    stok_fisik: Number(p.stok_aktual),
                    selisih: 0,
                    harga_satuan: Number(p.harga_beli ?? 0),
                    alasan: ''
                }))
                this.hasMore = res.has_more
            })
        },

        hitung(item) {
            item.selisih = item.stok_fisik - item.stok_sistem
        },

        updateHarga(item, val) {
            item.harga_satuan = Number(val.replace(/[^\d]/g, '')) || 0
        },

        formatRupiah(val) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(val || 0)
        },

        cari() {
            this.page = 1
            this.load()
        },

        nextPage() {
            if (!this.hasMore) return
            this.page++
            this.load()
        },

        prevPage() {
            if (this.page === 1) return
            this.page--
            this.load()
        },

        simpan() {
    const items = this.products
        .filter(p => Number(p.selisih) !== 0)
        .map(p => ({
            product_id: p.id,
            stok_sistem: Number(p.stok_sistem),
            stok_fisik: Number(p.stok_fisik),
            selisih: Number(p.selisih),
            harga_satuan: Number(p.harga_satuan || 0),
            alasan: p.alasan || null
        }))

    @this.call('saveAdjustment', {
        no_penyesuaian: this.nomorAdjustment,
        tanggal_penyesuaian: this.tanggalAdjustment,
        jenis_penyesuaian: this.jenisAdjustment,
        status: this.status,
        catatan: this.catatan,
        items: items
    })
}

    }))
})
</script>
@endsection
