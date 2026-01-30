<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light dark:bg-dark text-dark dark:text-light d-flex p-3 p-lg-4 align-items-center justify-content-center min-vh-100 flex-column">
<header class="w-100 container mb-4">
    @if (Route::has('login'))
        <nav class="d-flex align-items-center justify-content-end gap-3">
            @auth
                <a href="{{ url('/home') }}"
                   class="btn btn-outline-dark dark:btn-outline-light rounded-1 px-3 py-1">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="btn btn-link text-dark dark:text-light px-3 py-1">
                    Log in
                </a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                       class="btn btn-outline-dark dark:btn-outline-light rounded-1 px-3 py-1">
                        Register
                    </a>
                @endif
            @endauth
        </nav>
    @endif
</header>

<div class="d-flex align-items-center justify-content-center w-100 transition-opacity opacity-100">
    <main class="d-flex flex-column-reverse flex-lg-row w-100 container">
        <!-- Content Card -->
        <div class="card rounded-top rounded-lg-start rounded-lg-bottom-0 rounded-lg-end-lg-0 shadow-sm">
            <div class="card-body p-4 p-lg-5">
                <h1 class="h5 mb-2 fw-medium">Let's get started</h1>
                <p class="mb-3 text-muted">Laravel has an incredibly rich ecosystem. <br>We suggest starting with the following.</p>

                <ul class="list-unstyled mb-4">
                    <li class="d-flex align-items-center gap-3 py-2 position-relative">
                            <span class="bg-white dark:bg-dark rounded-circle shadow-sm border border-light dark:border-dark p-1">
                                <span class="bg-secondary rounded-circle d-block" style="width: 14px; height: 14px;"></span>
                            </span>
                        <span>
                                Read the
                                <a href="https://laravel.com/docs" target="_blank" class="text-danger text-decoration-underline ms-1">
                                    Documentation
                                    <svg width="10" height="11" viewBox="0 0 10 11" fill="currentColor" class="ms-1" style="width: 10px; height: 10px;">
                                        <path d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001" stroke="currentColor" stroke-linecap="square"/>
                                    </svg>
                                </a>
                            </span>
                    </li>
                    <li class="d-flex align-items-center gap-3 py-2 position-relative">
                            <span class="bg-white dark:bg-dark rounded-circle shadow-sm border border-light dark:border-dark p-1">
                                <span class="bg-secondary rounded-circle d-block" style="width: 14px; height: 14px;"></span>
                            </span>
                        <span>
                                Watch video tutorials at
                                <a href="https://laracasts.com" target="_blank" class="text-danger text-decoration-underline ms-1">
                                    Laracasts
                                    <svg width="10" height="11" viewBox="0 0 10 11" fill="currentColor" class="ms-1" style="width: 10px; height: 10px;">
                                        <path d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001" stroke="currentColor" stroke-linecap="square"/>
                                    </svg>
                                </a>
                            </span>
                    </li>
                </ul>

                <a href="https://cloud.laravel.com" target="_blank"
                   class="btn btn-dark dark:btn-light text-white dark:text-dark rounded-1 px-3 py-1">
                    Deploy now
                </a>
            </div>
        </div>

        <!-- Image Card -->
        <div class="card rounded-bottom rounded-lg-start-0 rounded-lg-end-lg bg-danger bg-opacity-10 dark:bg-danger dark:bg-opacity-10 position-relative overflow-hidden" style="aspect-ratio: 335/376;">
            <!-- Laravel Logo SVG -->
            <svg class="w-100 text-danger dark:text-danger opacity-100" viewBox="0 0 438 104" fill="currentColor">
                <!-- SVG paths here (same as original) -->
            </svg>

            <!-- Light/Dark mode illustrations would go here -->

            <div class="position-absolute top-0 start-0 w-100 h-100 rounded-bottom rounded-lg-start-0 rounded-lg-end-lg border border-light dark:border-dark opacity-10"></div>
        </div>
    </main>
</div>

@if (Route::has('login'))
    <div class="d-none d-lg-block" style="height: 58px;"></div>
@endif

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
