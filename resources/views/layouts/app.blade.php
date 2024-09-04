<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TimeTrove') }}</title>

    <!-- Fonts -->
    <!-- <link rel="dns-prefetch" href="//fonts.bunny.net"> -->
    <!-- <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet"> -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7f7f7;
            /* padding-top: 56px; */

        }

        .navbar {
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand,
        .navbar-brand:hover {
            font-weight: bold;
            color: #1d72b8;
        }

        .navbar-nav .nav-link {
            color: #333;
            transition: color 0.3s;
        }

        .navbar-nav .nav-link:hover {
            color: #1d72b8;
        }

        .btn-primary {
            background-color: #1d72b8;
            border-color: #1a63d7;
        }

        .btn-primary:hover {
            background-color: #1a63d7;
            border-color: #1559b0;
        }

        .btn-outline-primary {
            color: #1d72b8;
            border-color: #1a63d7;
        }

        .btn-outline-primary:hover {
            background-color: #1a63d7;
            color: #ffffff;
        }

        .footer {
            background-color: #ffffff;
            padding: 40px 0;
            box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
        }

        .footer .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .footer .social-icons a {
            color: #1d72b8;
            margin: 0 10px;
            font-size: 1.5rem;
        }

        .footer .social-icons a:hover {
            color: #1559b0;
        }

        .star-rating {
            font-size: 24px;
        }

        .star {
            cursor: pointer;
            color: #ccc;
        }

        .star.selected {
            color: gold;
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body class="d-flex flex-column min-vh-100">
    <div id="app" class="flex-grow-1">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light fixed-top">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    TimeTrove
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="#">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Features</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Pricing</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Contact</a>
                        </li>
                        @if (Route::has('login'))
                        @auth

                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link btn dropdown-toggle" href="#" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->first_name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>

                        @endauth
                        @endif
                    </ul>
                </div>
            </div>
        </nav>

        <main class="mt-5 py-4 flex-grow-1">
            @yield('content')
        </main>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <h5>About TimeTrove</h5>
                    <p>TimeTrove helps you streamline your appointment management with ease. From online booking to automated reminders, we’ve got you covered.</p>
                </div>
                <div class="col-md-6 mb-3 text-md-end">
                    <h5>Follow Us</h5>
                    <div class="social-icons">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-twitter"></i></a>
                        <a href="#"><i class="bi bi-linkedin"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <p>&copy; 2024 TimeTrove. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script> -->

</body>

</html>