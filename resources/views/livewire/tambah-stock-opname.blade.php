<div class="col-12">
    <div class="card" wire:ignore x-data="stockOpname()" x-init="init()">
        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Nomor Opname</label>
                    <input type="text" class="form-control" x-model="nomorOpname">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Tanggal Opname</label>
                    <input type="text" id="tanggalOpname" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select id="statusOpname" class="form-select">
                        <option value="draft">Draft</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="canceled">Canceled</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
            </div>
            <hr class="my-3">
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" placeholder="Cari produk..." x-model.debounce.500ms="search" @input="cari()">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th width="2%">No</th>
                        <th width="10%">Produk</th>
                        <th width="15%">Harga Satuan</th>
                        <th width="15%">Stok Sistem</th>
                        <th width="15%">Stok Fisik</th>
                        <th width="10%">Selisih</th>
                        <th width="8%">Jenis</th>
                        <th width="35%">Alasan</th>
                    </tr>
                    </thead>
                    <tbody>
                    <template x-for="(item, index) in products" :key="item.id">
                        <tr>
                            <td x-text="index + 1 + ((page - 1) * 10)"></td>
                            <td x-text="item.nama_produk"></td>
                            <td>
                                <input type="text" class="form-control" :value="formatRupiah(item.harga_beli)" @input="updateHarga(item, $event.target.value)">
                            </td>
                            <td>
                                <input type="number" class="form-control" :value="item.sistem" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control" x-model.number="item.fisik" @input="hitung(item)">
                            </td>
                            <td>
                                <input type="number" class="form-control" :value="item.selisih" readonly>
                            </td>
                            <td>
                                <span class="badge bg-danger text-white" x-show="item.jenis === 'shortage'">Shortage</span>
                                <span class="badge bg-success text-white" x-show="item.jenis === 'excess'">Excess</span>
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

            <div class="row">
                <div class="col-md-8 mb-3">
                    <div class="row">
                        <div class="col-md-6" x-show="status === 'approved'" x-transition>
                            <label class="form-label">Tanggal Approved</label>
                            <input type="text" id="tanggalApproved" class="form-control">
                        </div>
                        <div class="col-md-6" x-show="status === 'approved'" x-transition>
                            <label class="form-label">Approved By</label>
                            <input type="text" class="form-control" x-model="approvedBy">
                        </div>
                        <div class="col-md-12 mt-3">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" rows="3" x-model="catatan"></textarea>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-light h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between text-danger">
                                <span class="text-danger">Shortage</span>
                                <span x-text="summary.shortage"></span>
                            </div>
                            <div class="d-flex justify-content-between text-success">
                                <span>Excess</span>
                                <span x-text="summary.excess"></span>
                            </div>
                            <hr class="my-1">
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Total Produk</span>
                                <span x-text="summary.total"></span>
                            </div>
                            <button class="btn btn-primary w-100 mt-3" @click="simpan()">Simpan Opname</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@section('script')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('stockOpname', () => ({
            nomorOpname: '',
            status: 'draft',
            tanggalOpname: '',
            tanggalApproved: '',
            approvedBy: '',
            catatan: '',
            search: '',
            page: 1,
            hasMore: false,
            products: [],
            summary: { shortage: 0, excess: 0, total: 0 },

            init() {
                this.initTanggalOpname()
                this.initStatus()
                this.load()
            },

            initTanggalOpname() {
                new Litepicker({
                    element: document.getElementById('tanggalOpname'),
                    format: 'YYYY-MM-DD',
                    autoApply: true,
                    singleMode: true,
                    setup: p => p.on('selected', d => this.tanggalOpname = d.format('YYYY-MM-DD'))
                })
            },

            initTanggalApproved() {
                const el = document.getElementById('tanggalApproved')
                if (!el || el._litepicker) return
                el._litepicker = new Litepicker({
                    element: el,
                    format: 'YYYY-MM-DD',
                    autoApply: true,
                    singleMode: true,
                    setup: p => p.on('selected', d => this.tanggalApproved = d.format('YYYY-MM-DD'))
                })
            },

            initStatus() {
                new TomSelect('#statusOpname', {
                    onChange: v => {
                        this.status = v
                        if (v === 'approved') {
                            this.$nextTick(() => this.initTanggalApproved())
                        } else {
                            this.tanggalApproved = ''
                            this.approvedBy = ''
                        }
                    }
                })
            },

            load() {
                @this.call('loadProducts', this.page, this.search).then(res => {
                    this.products = res.data.map(p => ({
                        id: p.id,
                        nama_produk: p.nama_produk,
                        harga_beli: Number(p.harga_beli ?? 0),
                        sistem: Number(p.stok_aktual),
                        fisik: Number(p.stok_aktual),
                        selisih: 0,
                        jenis: null,
                        alasan: ''
                    }))
                    this.hasMore = res.has_more
                    this.hitungSummary()
                })
            },

            hitung(item) {
                item.selisih = item.fisik - item.sistem
                if (item.selisih < 0) item.jenis = 'shortage'
                else if (item.selisih > 0) item.jenis = 'excess'
                else item.jenis = null
                this.hitungSummary()
            },

            hitungSummary() {
                let shortage = 0, excess = 0
                this.products.forEach(p => {
                    if (p.jenis === 'shortage') shortage++
                    if (p.jenis === 'excess') excess++
                })
                this.summary.shortage = shortage
                this.summary.excess = excess
                this.summary.total = this.products.length
            },

            updateHarga(item, val) {
                item.harga_beli = Number(val.replace(/[^\d]/g, '')) || 0
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
                @this.call('saveOpname', {
                    no_opname: this.nomorOpname,
                    status: this.status,
                    tanggal: this.tanggalOpname,
                    tanggal_approved: this.status === 'approved' ? this.tanggalApproved : null,
                    approved_by: this.status === 'approved' ? this.approvedBy : null,
                    catatan: this.catatan,
                    items: this.products
                        .filter(p => p.id && p.selisih !== null)
                        .map(p => ({
                            product_id: p.id,
                            stok_sistem: p.sistem,
                            stok_fisik: p.fisik,
                            selisih: p.selisih,
                            jenis_selisih: p.jenis ?? 'none',
                            harga_satuan: p.harga_beli,
                            alasan: p.alasan
                        }))
                })
            }

        }))
    })
</script>
@endsection
