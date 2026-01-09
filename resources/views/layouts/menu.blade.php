@php
    $listMenu = [
        [
            'title' => 'Dashboard',
            'url' => '/dashboard',
            'icon' => 'home',
        ],
        [
            'title' => 'Master Data',
            'url' => '/master-data',
            'icon' => 'database',
            'child' => [
                [
                    'title' => 'Role',
                    'url' => '/master-data/role',
                ],
                [
                    'title' => 'User',
                    'url' => '/master-data/user',
                ],
                [
                    'title' => 'Member',
                    'url' => '/master-data/member',
                ],
                [
                    'title' => 'Pelanggan',
                    'url' => '/master-data/pelanggan',
                ],
                [
                    'title' => 'Supplier',
                    'url' => '/master-data/supplier',
                ],
            ],
        ],
        [
            'title' => 'Master Produk',
            'url' => '/master-produk',
            'icon' => 'box',
            'child' => [
                [
                    'title' => 'Satuan',
                    'url' => '/master-produk/satuan',
                ],
                [
                    'title' => 'Kategori',
                    'url' => '/master-produk/kategori',
                ],
                [
                    'title' => 'Merek',
                    'url' => '/master-produk/merek',
                ],
                [
                    'title' => 'Rak',
                    'url' => '/master-produk/rak',
                ],
                [
                    'title' => 'Produk',
                    'url' => '/master-produk/produk',
                ],
            ],
        ],
        [
            'title' => 'Penjualan',
            'url' => '/penjualan',
            'icon' => 'point_of_sale',
            'child' => [
                [
                    'title' => 'Tambah Penjualan',
                    'url' => '/penjualan/tambah',
                ],
                [
                    'title' => 'Daftar Penjualan',
                    'url' => '/penjualan/daftar',
                ],
                [
                    'title' => 'Daftar Return',
                    'url' => '/penjualan/daftar-return',
                ],
                [
                    'title' => 'POS',
                    'url' => '/penjualan/pos',
                ],
            ],
        ],
        [
            'title' => 'Pembelian',
            'url' => '/pembelian',
            'icon' => 'add_shopping_cart',
            'child' => [
                [
                    'title' => 'Tambah Pembelian',
                    'url' => '/pembelian/tambah',
                ],
                [
                    'title' => 'Daftar Pembelian',
                    'url' => '/pembelian/daftar',
                ],
                [
                    'title' => 'Daftar Return',
                    'url' => '/pembelian/daftar-return',
                ],
            ],
        ],
        [
            'title' => 'Master Stok',
            'url' => '/master-stok',
            'icon' => 'inventory',
            'child' => [
                [
                    'title' => 'Tambah Stok',
                    'url' => '/master-stok/tambah',
                ],
                [
                    'title' => 'Daftar Stok',
                    'url' => '/master-stok/daftar',
                ],
            ],
        ],
        [
            'title' => 'Pengaturan',
            'url' => '/pengaturan',
            'icon' => 'settings',
        ],
    ];

    $path = '/' . request()->path();
@endphp

<div class="navbar-expand-md">
    <div class="collapse navbar-collapse" id="navbar-menu">
        <div class="navbar">
            <div class="container-xl">
                <div class="row flex-column flex-md-row flex-fill align-items-center">
                    <div class="col">
                        <!-- BEGIN NAVBAR MENU -->
                        <nav aria-label="Primary">
                            <!-- BEGIN NAVBAR MENU -->
                            <ul class="navbar-nav">
                                @foreach ($listMenu as $menu)
                                    @if (isset($menu['child']))
                                        @php
                                            $active = false;
                                            if (str_contains($path, $menu['url'])) {
                                                $active = true;
                                            }
                                        @endphp

                                        <li class="nav-item dropdown {{ $active ? 'active' : '' }}">
                                            <a class="nav-link dropdown-toggle" href="#navbar-form"
                                                data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button"
                                                aria-haspopup="true" aria-expanded="false">
                                                <span class="nav-icon d-md-none d-lg-inline-block">
                                                    <span class="material-symbols-outlined">
                                                        {{ $menu['icon'] }}
                                                    </span>
                                                </span>
                                                <span class="nav-link-title"> {{ $menu['title'] }} </span>
                                            </a>
                                            <div class="dropdown-menu">
                                                @foreach ($menu['child'] as $child)
                                                    <a class="dropdown-item {{ $path == $child['url'] ? 'active' : '' }}"
                                                        href="{{ $child['url'] }}">
                                                        {{ $child['title'] }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </li>
                                    @else
                                        <li class="nav-item {{ $path == $menu['url'] ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ $menu['url'] }}">
                                                <span class="nav-icon d-md-none d-lg-inline-block">
                                                    <span class="material-symbols-outlined">
                                                        {{ $menu['icon'] }}
                                                    </span>
                                                </span>
                                                <span class="nav-link-title"> {{ $menu['title'] }} </span>
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                            <!-- END NAVBAR MENU -->
                        </nav>
                        <!-- END NAVBAR MENU -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
