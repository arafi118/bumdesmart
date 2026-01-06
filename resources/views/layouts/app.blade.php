<!doctype html>

<html lang="en" data-bs-theme-primary="teal">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>{{ $title ?? '' }} &mdash; {{ env('APP_NAME') }} | {{ env('APP_TITLE') }}</title>
    <link rel="icon" href="{{ asset('assets/img/logo/logo.png') }}">

    <!-- BEGIN PAGE LEVEL STYLES -->
    <link href="{{ asset('assets/libs/jsvectormap/dist/jsvectormap.css') }}" rel="stylesheet" />
    <!-- END PAGE LEVEL STYLES -->

    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="{{ asset('assets/css/tabler.css') }}" rel="stylesheet" />
    <!-- END GLOBAL MANDATORY STYLES -->

    <!-- BEGIN PLUGINS STYLES -->
    <link href="{{ asset('assets/css/tabler-vendors.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/tabler-themes.css') }}" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.23.0/sweetalert2.min.css"
        integrity="sha512-Ivy7sPrd6LPp20adiK3al16GBelPtqswhJnyXuha3kGtmQ1G2qWpjuipfVDaZUwH26b3RDe8x707asEpvxl7iA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="{{ asset('assets/libs/tom-select/dist/css/tom-select.bootstrap5.min.css') }}" rel="stylesheet" />

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

    <!-- END PLUGINS STYLES -->

    <!-- BEGIN CUSTOM FONT -->
    <style>
        @import url("https://rsms.me/inter/inter.css");

        .nav-icon {
            margin-inline-end: 0.5rem;
            color: inherit;
        }
    </style>
    <!-- END CUSTOM FONT -->

    @yield('link')
</head>

<body>
    <div class="page">
        {{-- NAVBAR --}}
        @include('layouts.navbar')

        {{-- MENU --}}
        @include('layouts.menu')

        <div class="page-wrapper">

            <!-- BEGIN PAGE HEADER -->
            <div class="page-header d-print-none">
                <div class="container-xl">
                    <div class="row g-2 align-items-center">
                        <div class="col">
                            <!-- Page pre-title -->
                            <h1 class="page-title">{{ $title ?? '' }}</h1>
                        </div>
                        <!-- Page title actions -->
                        <div class="col-auto ms-auto d-print-none">
                            @yield('button-header')
                        </div>
                    </div>
                </div>
            </div>
            <!-- END PAGE HEADER -->

            <!-- BEGIN PAGE BODY -->
            <main id="content" class="page-body">
                <div class="container-xl">
                    {{ $slot }}
                </div>
            </main>
            <!-- END PAGE BODY -->
            <!-- BEGIN FOOTER -->
            <!--  BEGIN FOOTER  -->
            <footer class="footer footer-transparent d-print-none">
                <div class="container-xl">
                    <div class="row text-center align-items-center flex-row-reverse">
                        <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                            <ul class="list-inline list-inline-dots mb-0">
                                <li class="list-inline-item">
                                    Copyright &copy; {{ date('Y') }}
                                    <a href="." class="link-secondary">{{ env('APP_NAME') }}</a>.
                                    All rights reserved.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
            <!--  END FOOTER  -->
            <!-- END FOOTER -->
        </div>
    </div>

    <!-- BEGIN PAGE LIBRARIES -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.23.0/sweetalert2.all.min.js"
        integrity="sha512-J+4Nt/+nieSNJjQGCPb8jKf5/wv31QiQM10bOotEHUKc9tB1Pn0gXQS6XXPtDoQhHHao5poTnSByMInzafUqzA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ asset('assets/libs/tom-select/dist/js/tom-select.base.min.js') }}"></script>
    <!-- END PAGE LIBRARIES -->

    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="{{ asset('assets/js/tabler.min.js') }}" defer></script>
    <!-- END GLOBAL MANDATORY SCRIPTS -->

    <script>
        const Select = [];
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        window.addEventListener('show-modal', (event) => {
            var modalId = event.detail.modalId;

            $('#' + modalId).modal('show');
        });

        window.addEventListener('hide-modal', (event) => {
            var modalId = event.detail.modalId;

            $('#' + modalId).modal('hide');
        });

        window.addEventListener('alert', (event) => {
            Toast.fire({
                icon: event.detail.type,
                title: event.detail.message,
            });
        });

        document.addEventListener('livewire:initialized', () => {
            Livewire.on('confirm-delete', (event) => {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('delete-confirmed', {
                            id: event.id
                        });
                    }
                });
            });
        });

        window.addEventListener('set-select-value', (event) => {
            var selectId = event.detail.selectId;
            var value = event.detail.value;

            if (!Select[selectId]) {
                const el = document.getElementById(selectId);
                if (el && el.classList.contains('tom-select')) {
                    Select[selectId] = new TomSelect(el, {
                        copyClassesToDropdown: false,
                        dropdownParent: "body",
                        controlInput: "<input>",
                    });
                }
            }

            if (Select[selectId]) {
                Select[selectId].clear();
                Select[selectId].setValue(value);
            }
        });

        document.addEventListener('DOMContentLoaded', function(e) {
            initTomSelect();
        });

        function initTomSelect() {
            document.querySelectorAll('.tom-select').forEach((el) => {
                const selectId = el.getAttribute('id');

                if (!Select[selectId] && !el.tomselect) {
                    Select[selectId] = new TomSelect(el, {
                        copyClassesToDropdown: false,
                        dropdownParent: "body",
                        controlInput: "<input>",
                        render: {
                            item: function(data, escape) {
                                if (data.customProperties) {
                                    return '<div><span class="dropdown-item-indicator">' + data
                                        .customProperties + "</span>" + escape(data.text) +
                                        "</div>";
                                }
                                return "<div>" + escape(data.text) + "</div>";
                            },
                            option: function(data, escape) {
                                if (data.customProperties) {
                                    return '<div><span class="dropdown-item-indicator">' + data
                                        .customProperties + "</span>" + escape(data.text) +
                                        "</div>";
                                }
                                return "<div>" + escape(data.text) + "</div>";
                            },
                        },
                        onChange: function(value) {
                            el.value = value;
                            el.dispatchEvent(new Event('change', {
                                bubbles: true
                            }));
                        }
                    });
                }
            });
        }
    </script>

    @yield('script')

</body>

</html>
