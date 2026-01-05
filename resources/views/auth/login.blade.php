<!doctype html>

<html lang="en" data-bs-theme-primary="teal">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Login &mdash; {{ env('APP_NAME') }} | {{ env('APP_TITLE') }}</title>
    <link rel="icon" href="{{ asset('assets/img/logo/logo.png') }}">

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
    <!-- END PLUGINS STYLES -->

    <!-- BEGIN CUSTOM FONT -->
    <style>
        @import url("https://rsms.me/inter/inter.css");
    </style>
    <!-- END CUSTOM FONT -->
</head>

<body>
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <a href="." aria-label="Tabler" class="navbar-brand navbar-brand-autodark">
                    <img src="{{ asset('assets/img/logo/logo-transparent.png') }}" alt="{{ env('APP_NAME') }}"
                        width="128">
                </a>
            </div>
            <div class="card card-md">
                <div class="card-body">
                    <h2 class="h2 text-center mb-4">Login {{ env('APP_NAME') }}</h2>
                    <form action="/auth" method="post" autocomplete="off" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username"
                                placeholder="Username" autocomplete="off" />
                        </div>
                        <div class="mb-2">
                            <label class="form-label">
                                Password
                            </label>
                            <div class="input-group input-group-flat">
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Password" autocomplete="off" />
                                <span class="input-group-text">
                                    <a href="#" class="link-secondary" title="Show password"
                                        data-bs-toggle="tooltip">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"
                                            focusable="false" class="icon icon-1">
                                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                            <path
                                                d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                        </svg>
                                    </a>
                                </span>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-check">
                                <input type="checkbox" class="form-check-input" />
                                <span class="form-check-label">Remember me on this device</span>
                            </label>
                        </div>
                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="./dist/js/tabler.min.js?1767120590" defer></script>
    <!-- END GLOBAL MANDATORY SCRIPTS -->
    <!-- BEGIN DEMO SCRIPTS -->
    <script src="./preview/js/demo.min.js?1767120590" defer></script>
    <!-- END DEMO SCRIPTS -->
</body>

</html>
