<div class="modal fade" id="detailOpnameModal" tabindex="-1" role="dialog" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Detail Stock Opname {{ $selectedOpname?->no_opname }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if ($selectedOpname)
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="30%">Nomor Opname</th>
                                    <td>: {{ $selectedOpname->no_opname }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal</th>
                                    <td>: {{ \Carbon\Carbon::parse($selectedOpname->tanggal_opname)->format('d F Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>:
                                        @if ($selectedOpname->status == 'draft')
                                            <span class="badge bg-secondary text-white">Draft</span>
                                        @elseif($selectedOpname->status == 'approved')
                                            <span class="badge bg-success text-white">Approved</span>
                                        @elseif($selectedOpname->status == 'rejected')
                                            <span class="badge bg-danger text-white">Rejected</span>
                                        @else
                                            <span
                                                class="badge bg-info text-white">{{ ucfirst($selectedOpname->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="30%">Petugas</th>
                                    <td>: {{ $selectedOpname->user->nama_lengkap ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Disetujui Oleh</th>
                                    <td>: {{ $selectedOpname->approvedBy->nama_lengkap ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Catatan</th>
                                    <td>: {{ $selectedOpname->catatan ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Produk</th>
                                    <th class="text-center">Stok Sistem</th>
                                    <th class="text-center">Stok Fisik</th>
                                    <th class="text-center">Selisih</th>
                                    <th class="text-center">Status</th>
                                    <th>Alasan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($opnameDetails as $detail)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="font-weight-medium">{{ $detail->product->nama_produk ?? '-' }}
                                            </div>
                                            <div class="text-muted text-xs">{{ $detail->product->kode_produk ?? '' }}
                                            </div>
                                        </td>
                                        <td class="text-center">{{ $detail->stok_sistem }}</td>
                                        <td class="text-center">{{ $detail->stok_fisik }}</td>
                                        <td
                                            class="text-center {{ $detail->selisih < 0 ? 'text-danger' : ($detail->selisih > 0 ? 'text-success' : '') }}">
                                            {{ $detail->selisih > 0 ? '+' : '' }}{{ $detail->selisih }}
                                        </td>
                                        <td class="text-center">
                                            @if ($detail->selisih < 0)
                                                <span class="badge bg-danger-lt">Shortage</span>
                                            @elseif($detail->selisih > 0)
                                                <span class="badge bg-success-lt">Excess</span>
                                            @else
                                                <span class="badge bg-secondary-lt">Match</span>
                                            @endif
                                        </td>
                                        <td>{{ $detail->alasan ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Tidak ada detail produk</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2">Memuat data...</p>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
