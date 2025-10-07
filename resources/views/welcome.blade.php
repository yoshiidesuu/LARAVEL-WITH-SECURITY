<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.scss', 'resources/js/app.js'])
    </head>
    <body class="min-vh-100 d-flex flex-column bg-light">
        <!-- Navigation -->
        @if (Route::has('login'))
            <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
                <div class="container">
                    <a class="navbar-brand fw-bold" href="/">Laravel</a>
                    <div class="ms-auto">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-outline-dark">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-link text-decoration-none text-dark">
                                Log in
                            </a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-outline-dark ms-2">
                                    Register
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </nav>
        @endif

        <!-- Main Content -->
        <main class="flex-grow-1 d-flex align-items-center justify-content-center py-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="card shadow-lg border-0 overflow-hidden">
                            <div class="row g-0">
                                <!-- Left Content Section -->
                                <div class="col-lg-6 bg-white p-5">
                                    <div class="d-flex flex-column h-100">
                                        <div class="mb-4">
                                            <!-- Laravel Logo SVG -->
                                            <svg class="text-danger" width="150" viewBox="0 0 438 104" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M17.2036 -3H0V102.197H49.5189V86.7187H17.2036V-3Z" fill="currentColor" />
                                                <path d="M110.256 41.6337C108.061 38.1275 104.945 35.3731 100.905 33.3681C96.8667 31.3647 92.8016 30.3618 88.7131 30.3618C83.4247 30.3618 78.5885 31.3389 74.201 33.2923C69.8111 35.2456 66.0474 37.928 62.9059 41.3333C59.7643 44.7401 57.3198 48.6726 55.5754 53.1293C53.8287 57.589 52.9572 62.274 52.9572 67.1813C52.9572 72.1925 53.8287 76.8995 55.5754 81.3069C57.3191 85.7173 59.7636 89.6241 62.9059 93.0293C66.0474 96.4361 69.8119 99.1155 74.201 101.069C78.5885 103.022 83.4247 103.999 88.7131 103.999C92.8016 103.999 96.8667 102.997 100.905 100.994C104.945 98.9911 108.061 96.2359 110.256 92.7282V102.195H126.563V32.1642H110.256V41.6337ZM108.76 75.7472C107.762 78.4531 106.366 80.8078 104.572 82.8112C102.776 84.8161 100.606 86.4183 98.0637 87.6206C95.5202 88.823 92.7004 89.4238 89.6103 89.4238C86.5178 89.4238 83.7252 88.823 81.2324 87.6206C78.7388 86.4183 76.5949 84.8161 74.7998 82.8112C73.004 80.8078 71.6319 78.4531 70.6856 75.7472C69.7356 73.0421 69.2644 70.1868 69.2644 67.1821C69.2644 64.1758 69.7356 61.3205 70.6856 58.6154C71.6319 55.9102 73.004 53.5571 74.7998 51.5522C76.5949 49.5495 78.738 47.9451 81.2324 46.7427C83.7252 45.5404 86.5178 44.9396 89.6103 44.9396C92.7012 44.9396 95.5202 45.5404 98.0637 46.7427C100.606 47.9451 102.776 49.5487 104.572 51.5522C106.367 53.5571 107.762 55.9102 108.76 58.6154C109.756 61.3205 110.256 64.1758 110.256 67.1821C110.256 70.1868 109.756 73.0421 108.76 75.7472Z" fill="currentColor" />
                                                <path d="M242.805 41.6337C240.611 38.1275 237.494 35.3731 233.455 33.3681C229.416 31.3647 225.351 30.3618 221.262 30.3618C215.974 30.3618 211.138 31.3389 206.75 33.2923C202.36 35.2456 198.597 37.928 195.455 41.3333C192.314 44.7401 189.869 48.6726 188.125 53.1293C186.378 57.589 185.507 62.274 185.507 67.1813C185.507 72.1925 186.378 76.8995 188.125 81.3069C189.868 85.7173 192.313 89.6241 195.455 93.0293C198.597 96.4361 202.361 99.1155 206.75 101.069C211.138 103.022 215.974 103.999 221.262 103.999C225.351 103.999 229.416 102.997 233.455 100.994C237.494 98.9911 240.611 96.2359 242.805 92.7282V102.195H259.112V32.1642H242.805V41.6337ZM241.31 75.7472C240.312 78.4531 238.916 80.8078 237.122 82.8112C235.326 84.8161 233.156 86.4183 230.614 87.6206C228.07 88.823 225.251 89.4238 222.16 89.4238C219.068 89.4238 216.275 88.823 213.782 87.6206C211.289 86.4183 209.145 84.8161 207.35 82.8112C205.554 80.8078 204.182 78.4531 203.236 75.7472C202.286 73.0421 201.814 70.1868 201.814 67.1821C201.814 64.1758 202.286 61.3205 203.236 58.6154C204.182 55.9102 205.554 53.5571 207.35 51.5522C209.145 49.5495 211.288 47.9451 213.782 46.7427C216.275 45.5404 219.068 44.9396 222.16 44.9396C225.251 44.9396 228.07 45.5404 230.614 46.7427C233.156 47.9451 235.326 49.5487 237.122 51.5522C238.917 53.5571 240.312 55.9102 241.31 58.6154C242.306 61.3205 242.806 64.1758 242.806 67.1821C242.805 70.1868 242.305 73.0421 241.31 75.7472Z" fill="currentColor" />
                                                <path d="M438 -3H421.694V102.197H438V-3Z" fill="currentColor" />
                                                <path d="M139.43 102.197H155.735V48.2834H183.712V32.1665H139.43V102.197Z" fill="currentColor" />
                                                <path d="M324.49 32.1665L303.995 85.794L283.498 32.1665H266.983L293.748 102.197H314.242L341.006 32.1665H324.49Z" fill="currentColor" />
                                                <path d="M376.571 30.3656C356.603 30.3656 340.797 46.8497 340.797 67.1828C340.797 89.6597 356.094 104 378.661 104C391.29 104 399.354 99.1488 409.206 88.5848L398.189 80.0226C398.183 80.031 389.874 90.9895 377.468 90.9895C363.048 90.9895 356.977 79.3111 356.977 73.269H411.075C413.917 50.1328 398.775 30.3656 376.571 30.3656ZM357.02 61.0967C357.145 59.7487 359.023 43.3761 376.442 43.3761C393.861 43.3761 395.978 59.7464 396.099 61.0967H357.02Z" fill="currentColor" />
                                            </svg>
                                        </div>

                                        <h1 class="h3 fw-bold mb-3">Let's get started</h1>
                                        <p class="text-muted mb-4">Laravel has an incredibly rich ecosystem. We suggest starting with the following.</p>

                                        <ul class="list-unstyled mb-4">
                                            <li class="mb-3">
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-danger rounded-circle me-3" style="width: 12px; height: 12px;"></span>
                                                    <span>
                                                        Read the
                                                        <a href="https://laravel.com/docs" target="_blank" class="text-danger text-decoration-none fw-medium">
                                                            Documentation
                                                            <svg width="10" height="11" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg" class="ms-1" style="display: inline-block; width: 10px; height: 11px;">
                                                                <path d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001" stroke="currentColor" stroke-linecap="square" />
                                                            </svg>
                                                        </a>
                                                    </span>
                                                </div>
                                            </li>
                                            <li class="mb-3">
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-danger rounded-circle me-3" style="width: 12px; height: 12px;"></span>
                                                    <span>
                                                        Watch video tutorials at
                                                        <a href="https://laracasts.com" target="_blank" class="text-danger text-decoration-none fw-medium">
                                                            Laracasts
                                                            <svg width="10" height="11" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg" class="ms-1" style="display: inline-block; width: 10px; height: 11px;">
                                                                <path d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001" stroke="currentColor" stroke-linecap="square" />
                                                            </svg>
                                                        </a>
                                                    </span>
                                                </div>
                                            </li>
                                        </ul>

                                        <div class="mt-auto">
                                            <a href="https://cloud.laravel.com" target="_blank" class="btn btn-dark">
                                                Deploy now
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Visual Section -->
                                <div class="col-lg-6 bg-danger bg-opacity-10 d-flex align-items-center justify-content-center p-5 position-relative">
                                    <div class="text-center">
                                        <div class="display-1 fw-bold text-danger mb-3" style="font-size: 8rem; line-height: 1;">12</div>
                                        <p class="lead text-danger mb-0">The latest version</p>
                                        <p class="text-muted">Built for modern web applications</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-top py-3 mt-auto">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center text-muted small">
                        Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
                    </div>
                </div>
            </div>
        </footer>
    </body>
</html>
