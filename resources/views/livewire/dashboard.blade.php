@section('button-header')
    <div class="btn-list">
        <span class="d-none d-sm-inline">
            <a href="{{ url('/penjualan/daftar') }}" class="btn btn-white">
                Laporan
            </a>
        </span>
        <a href="{{ url('/penjualan/pos') }}" class="btn btn-primary d-none d-sm-inline-block">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M12 5l0 14" />
                <path d="M5 12l14 0" />
            </svg>
            Penjualan Baru
        </a>
    </div>
@endsection

<div>
    <div class="page-body">
        <div class="row row-deck row-cards">
            <!-- Sales Today -->
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Penjualan Hari Ini</div>
                            <div class="ms-auto lh-1">
                                <div class="dropdown">
                                    <a class="dropdown-toggle text-muted" href="#" data-bs-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">Hari Ini</a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item active" href="#">Hari Ini</a>
                                        <a class="dropdown-item" href="#">Kemarin</a>
                                        <a class="dropdown-item" href="#">Minggu Ini</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="h1 mb-3">Rp {{ number_format($todaySales, 0, ',', '.') }}</div>
                        <div class="d-flex mb-2">
                            <div>Conversion rate</div>
                            <div class="ms-auto">
                                <span class="text-green d-inline-flex align-items-center lh-1">
                                    {{ $totalTransactions }} Trx
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 17l6 -6l4 4l8 -8" />
                                        <path d="M14 7l7 0l0 7" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-blue" style="width: 75%" role="progressbar" aria-valuenow="75"
                                aria-valuemin="0" aria-valuemax="100" aria-label="75% Complete">
                                <span class="visually-hidden">75% Complete</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Month -->
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Penjualan Bulan Ini</div>
                            <div class="ms-auto lh-1">
                                <div class="dropdown">
                                    <a class="dropdown-toggle text-muted" href="#" data-bs-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">Bulan Ini</a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item active" href="#">Bulan Ini</a>
                                        <a class="dropdown-item" href="#">Bulan Lalu</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="h1 mb-3">Rp {{ number_format($monthSales, 0, ',', '.') }}</div>
                        <div class="d-flex mb-2">
                            <div>Revenue</div>
                            <div class="ms-auto">
                                <span class="text-green d-inline-flex align-items-center lh-1">
                                    7% <!-- Placeholder for dynamic growth if available -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 17l6 -6l4 4l8 -8" />
                                        <path d="M14 7l7 0l0 7" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-blue" style="width: 75%" role="progressbar" aria-valuenow="75"
                                aria-valuemin="0" aria-valuemax="100" aria-label="75% Complete">
                                <span class="visually-hidden">75% Complete</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profit Today -->
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Profit Hari Ini</div>
                        </div>
                        <div class="d-flex align-items-baseline">
                            <div class="h1 mb-3 me-2">Rp {{ number_format($todayProfit, 0, ',', '.') }}</div>
                        </div>
                        <div class="d-flex mb-2">
                            <div>Profit Margin</div>
                            <div class="ms-auto">
                                <span class="text-green d-inline-flex align-items-center lh-1">
                                    <!-- Placeholder for margin -->
                                </span>
                            </div>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-green" style="width: 100%" role="progressbar"
                                aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"
                                aria-label="100% Complete">
                                <span class="visually-hidden">100% Complete</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profit Month -->
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Profit Bulan Ini</div>
                        </div>
                        <div class="d-flex align-items-baseline">
                            <div class="h1 mb-3 me-2">Rp {{ number_format($monthProfit, 0, ',', '.') }}</div>
                        </div>
                        <div class="d-flex mb-2">
                            <div>Total Profit</div>
                            <div class="ms-auto">
                                <span class="text-green d-inline-flex align-items-center lh-1">
                                    <!-- Placeholder for margin -->
                                </span>
                            </div>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-green" style="width: 100%" role="progressbar"
                                aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"
                                aria-label="100% Complete">
                                <span class="visually-hidden">100% Complete</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="card-title">Transaksi Terakhir</div>
                    </div>
                    <div class="card-table table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>No. Invoice</th>
                                    <th>Pelanggan</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Waktu</th>
                                    <th class="w-1"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions as $sale)
                                    <tr>
                                        <td>
                                            <a href="{{ url('/penjualan/edit/' . $sale->id) }}" class="text-reset"
                                                tabindex="-1">{{ $sale->no_invoice }}</a>
                                        </td>
                                        <td>
                                            {{ $sale->customer ? $sale->customer->nama_pelanggan : 'Umum' }}
                                        </td>
                                        <td class="text-muted">
                                            Rp {{ number_format($sale->total, 0, ',', '.') }}
                                        </td>
                                        <td>
                                            @if ($sale->status == 'completed' || $sale->status == 'paid')
                                                <span class="badge bg-success me-1"></span> Lunas
                                            @else
                                                <span class="badge bg-warning me-1"></span>
                                                {{ ucfirst($sale->status) }}
                                            @endif
                                        </td>
                                        <td class="text-muted">
                                            {{ $sale->created_at->format('H:i') }}
                                        </td>
                                        <td>
                                            <a href="{{ url('/penjualan/edit/' . $sale->id) }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24"
                                                    height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M9 6l6 6l-6 6" />
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Belum ada transaksi</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Top Selling & Low Stock -->
            <div class="col-lg-4">
                <div class="row">
                    <!-- Top Selling -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Produk Terlaris</h3>
                            </div>
                            <table class="table card-table table-vcenter">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th colspan="2">Terjual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topSellingProducts as $item)
                                        <tr>
                                            <td>
                                                {{ $item->product->nama_produk ?? 'Unknown' }}
                                                <a href="#" class="ms-1" aria-label="Open website">
                                                </a>
                                            </td>
                                            <td>{{ number_format($item->total_qty) }}</td>
                                            <td class="w-50">
                                                <div class="progress progress-xs">
                                                    <div class="progress-bar bg-primary"
                                                        style="width: {{ min(100, ($item->total_qty / 100) * 100) }}%">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">Belum ada data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Low Stock -->
                    <div class="col-12">
                        <div class="card mt-3">
                            <div class="card-header">
                                <h3 class="card-title text-danger">Stok Menipis</h3>
                            </div>
                            <div class="list-group list-group-flush">
                                @forelse($lowStockProducts as $product)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="text-dark">{{ $product->nama_produk }}</span>
                                        </div>
                                        <span class="badge bg-danger-lt">{{ $product->stok_aktual }} left</span>
                                    </div>
                                @empty
                                    <div class="list-group-item text-center text-muted">
                                        Stok Aman
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
