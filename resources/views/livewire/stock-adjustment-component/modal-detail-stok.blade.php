<div class="modal fade" id="detailProdukModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    {{ $titleModal ?? 'Detail Produk' }}
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @if ($adjustmentDetail)
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="30%">No. Penyesuaian</td>
                                    <td>: <strong>{{ $adjustmentDetail->no_penyesuaian }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Tanggal</td>
                                    <td>: {{ $adjustmentDetail->tanggal_penyesuaian }}</td>
                                </tr>
                                <tr>
                                    <td>Oleh</td>
                                    <td>: {{ $adjustmentDetail->user->nama_lengkap ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6 text-end">
                            <span
                                class="badge bg-{{ $adjustmentDetail->status == 'approved' ? 'success' : 'secondary' }}">
                                {{ ucfirst($adjustmentDetail->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Jenis</th>
                                    <th class="text-end">Jumlah</th>
                                    <th>Alasan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($adjustmentDetail->details as $detail)
                                    <tr>
                                        <td>
                                            <div class="d-flex py-1 align-items-center">
                                                @if ($detail->product->gambar)
                                                    <span class="avatar me-2"
                                                        style="background-image: url({{ asset('storage/' . $detail->product->gambar) }})"></span>
                                                @endif
                                                <div class="flex-fill">
                                                    <div class="font-weight-medium">{{ $detail->product->nama_produk }}
                                                    </div>
                                                    <div class="text-muted"><a href="#" class="text-reset">Sku:
                                                            {{ $detail->product->sku }}</a></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $detail->jenis == 'in' ? 'success' : 'danger' }}-lt">
                                                {{ $detail->jenis == 'in' ? 'Masuk' : 'Keluar' }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            {{ $detail->jumlah }} {{ $detail->product->unit->nama_satuan ?? '' }}
                                        </td>
                                        <td>
                                            {{ $detail->alasan ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada item</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($adjustmentDetail->catatan)
                        <div class="mt-3">
                            <strong>Catatan:</strong>
                            <p class="text-muted">{{ $adjustmentDetail->catatan }}</p>
                        </div>
                    @endif
                @else
                    <div class="text-center p-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2">Memuat data...</p>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary ms-auto" data-bs-dismiss="modal">
                    Tutup
                </button>
            </div>

        </div>
    </div>
</div>
