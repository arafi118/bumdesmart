<div wire:ignore x-data="jurnalUmum()" x-init="initData(@js($jurnalUmum))">
    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Transaksi</label>
                            <input type="text" class="form-control litepicker" id="tanggal_transaksi"
                                value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Transaksi</label>
                            <select class="form-control" id="jenis_transaksi" x-model="selectedJenisTransaksi">
                                <option value="">-- Pilih Jenis Transaksi --</option>
                            </select>
                        </div>
                    </div>

                    <div class="row" id="kd_rekening">
                        <div class="col-sm-6 mb-3">
                            <label class="form-label" for="sumber_dana">Sumber Dana</label>
                            <select class="form-control" x-model="selectedSumberDana" id="sumber_dana">
                                <option value="">-- Pilih Sumber Dana --</option>
                            </select>
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label class="form-label" for="disimpan_ke">Disimpan Ke</label>
                            <select class="form-control" x-model="selectedDisimpanKe" id="disimpan_ke">
                                <option value="">-- Disimpan Ke --</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <template x-for="(item, index) in inputKeterangan" :key="index">
                            <div class="mb-3" :class="inputKeterangan.length > 1 ? 'col-sm-6' : 'col-12'">
                                <label class="form-label" x-text="item.label"></label>
                                <input type="text" class="form-control" :value="item.value" x-model="item.value">
                            </div>
                        </template>
                    </div>

                    <div class="row" id="form_nominal">
                        <div class="col-12 my-3">
                            <label class="form-label">Nominal Rp.</label>
                            <input type="text" id="nominal" class="form-control">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-github" x-on:click="simpanTransaksi">Simpan Transaksi</button>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label mb-0">Saldo</label>
                        <h4 class="mb-0">Rp. <span id="saldo">0</span></h4>
                    </div>
                    <input type="hidden" id="saldo_trx">
                </div>
            </div>
        </div>
    </div>
</div>
@section('script')
<script>
    let jenisTransaksi = new TomSelect('#jenis_transaksi', {
        valueField: 'id',
        labelField: 'label',
        searchField: 'label',
        options: [],
    });
    
    let sumberDana = new TomSelect('#sumber_dana', {
        valueField: 'id',
        labelField: 'label',
        searchField: 'label',
        options: [],
    });

    let disimpanKe = new TomSelect('#disimpan_ke', {
        valueField: 'id',
        labelField: 'label',
        searchField: 'label',
        options: [],
    });

    document.addEventListener('alpine:init', () => {
        Alpine.data('jurnalUmum', () => ({
            akun: [],
            jenisTransaksi: [],
            inputKeterangan: [],

            selectedJenisTransaksi: '',
            selectedSumberDana: '',
            selectedDisimpanKe: '',

            init() {
                this.$watch('selectedJenisTransaksi', (value) => {
                    this.setKodeAkun(value);
                });

                const updateKeterangan = () => {
                    const sumber = this.akun.find(a => a.id == this.selectedSumberDana);
                    const disimpan = this.akun.find(a => a.id == this.selectedDisimpanKe);

                    if (sumber && disimpan && this.inputKeterangan.length > 1) {
                        this.inputKeterangan[1].value = `${sumber.nama} → ${disimpan.nama}`;
                    }
                };

                this.$watch('selectedSumberDana', updateKeterangan);
                this.$watch('selectedDisimpanKe', updateKeterangan);
            },

            initData(jurnalUmum) {
                this.akun = jurnalUmum.akun;
                this.jenisTransaksi = jurnalUmum.jenis_transaksi;

                const jenisTransaksiOptions = this.jenisTransaksi.map((item) => {
                    return {
                        id: item.id,
                        label: item.nama
                    }
                })

                jenisTransaksi.clearOptions()
                jenisTransaksi.addOption(jenisTransaksiOptions)
            },

            setKodeAkun(jenisTransaksiId) {
                sumberDana.clearOptions();
                disimpanKe.clearOptions();
                
                if (jenisTransaksiId === '1') {
                    this.inputKeterangan = [
                        { label: 'Relasi', value: '' },
                        { label: 'Keterangan', value: '' }
                    ]
                    this.setAkunJenisTransaksi1();
                }
                
                if (jenisTransaksiId === '2') {
                    this.setAkunJenisTransaksi2();
                }

                if (jenisTransaksiId === '3'){
                    this.setAkunJenisTransaksi3();
                }
            },

            setAkunJenisTransaksi1() {
                let akunSumberDana = [];
                this.akun.forEach(item => {
                    const kode = item.kode;
                    const nama = item.nama;

                    if (!['2.1.04.01','2.1.04.02','2.1.04.03','2.1.02.01','2.1.03.01'].includes(kode) && !item.kode.startsWith('4.1.01')) {
                        akunSumberDana.push({
                            id: item.id,
                            label: `${kode}. - ${nama}`
                        });
                    }
                    
                });
                sumberDana.addOption(akunSumberDana);

                let akunDisimpanKe = [];
                this.akun.forEach(item => {
                    const kode = item.kode;
                    const nama = item.nama;

                    akunDisimpanKe.push({
                        id: item.id,
                        label: `${kode}. - ${nama}`
                    });
                });
                disimpanKe.addOption(akunDisimpanKe);
            },

            setAkunJenisTransaksi2() {
                let akunSumberDana = [];
                this.akun.forEach(item => {
                    const kode = item.id;
                    const nama = item.nama;
                    
                    if (!item.kode.startsWith('2.1.04')) {
                        akunSumberDana.push({
                            id: item.id,
                            label: `${kode}. - ${nama}`
                        });
                    }
                });
                sumberDana.addOption(akunSumberDana);

                let akunDisimpanKe = [];
                this.akun.forEach(item => {
                    const kode = item.id;
                    const nama = item.nama;

                    akunDisimpanKe.push({
                        id: item.id,
                        label: `${kode}. - ${nama}`
                    });
                });
                disimpanKe.addOption(akunDisimpanKe);
            },
            
            setAkunJenisTransaksi3() {
                let akunSumberDana = [];
                this.akun.forEach(item => {
                    const kode = item.id;
                    const nama = item.nama;
                    
                    akunSumberDana.push({
                        id: item.id,
                        label: `${kode}. - ${nama}`
                    });
                });
                sumberDana.addOption(akunSumberDana);

                let akunDisimpanKe = [];
                this.akun.forEach(item => {
                    const kode = item.id;
                    const nama = item.nama;

                    if (!item.kode.startsWith('1.1.03')) {
                        akunDisimpanKe.push({
                            id: item.id,
                            label: `${kode}. - ${nama}`
                        });
                    }
                });
                disimpanKe.addOption(akunDisimpanKe);
            },

            simpanTransaksi() {
                console.log(this.selectedDisimpanKe, this.selectedSumberDana, this.selectedJenisTransaksi, this.inputKeterangan);
            }
        }))
    })
</script>
@endsection
