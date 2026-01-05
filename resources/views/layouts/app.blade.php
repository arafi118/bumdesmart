<!doctype html>

<html lang="en" data-bs-theme-primary="teal">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>{{ $title }} &mdash; {{ env('APP_NAME') }} | {{ env('APP_TITLE') }}</title>
    <link rel="icon" href="{{ asset('assets/img/logo/logo.png') }}">

    <!-- BEGIN PAGE LEVEL STYLES -->
    <link href="{{ asset('assets/libs/jsvectormap/dist/jsvectormap.css') }}" rel="stylesheet" />
    <!-- END PAGE LEVEL STYLES -->

    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="{{ asset('assets/css/tabler.css') }}" rel="stylesheet" />
    <!-- END GLOBAL MANDATORY STYLES -->

    <!-- BEGIN PLUGINS STYLES -->
    <link href="{{ asset('assets/css/tabler-flags.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/tabler-socials.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/tabler-payments.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/tabler-vendors.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/tabler-marketing.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/tabler-themes.css') }}" rel="stylesheet" />

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
                            <h1 class="page-title">{{ $title }}</h1>
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
                    @yield('content')
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

    @yield('modal')

    <!-- BEGIN PAGE LIBRARIES -->
    <script src="{{ asset('assets/libs/apexcharts/dist/apexcharts.min.js') }}" defer></script>
    <script src="{{ asset('assets/libs/jsvectormap/dist/jsvectormap.min.js') }}" defer></script>
    <script src="{{ asset('assets/libs/jsvectormap/dist/maps/world.js') }}" defer></script>
    <script src="{{ asset('assets/libs/jsvectormap/dist/maps/world-merc.js') }}" defer></script>
    <!-- END PAGE LIBRARIES -->

    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="{{ asset('assets/js/tabler.min.js') }}" defer></script>
    <!-- END GLOBAL MANDATORY SCRIPTS -->

    @yield('script')

</body>

</html>
