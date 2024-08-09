<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to TimeTrove</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand text-primary" href="#">TimeTrove </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                @if (Route::has('login'))
                <ul class="navbar-nav">
                    @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/dashboard') }}">Dashboard</a>
                    </li>
                    @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Log in</a>
                    </li>
                    @if (Route::has('register'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">Register</a>
                    </li>
                    @endif
                    @endauth
                </ul>
                @endif
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <header class="text-center mb-5">
            <h1 class="display-4 text-primary">TimeTrove</h1>
            <p class="lead">Streamline Your Appointment Management</p>
        </header>

        <main>
            <section class="bg-white rounded shadow p-4 mb-4">
                <h2>About TimeTrove</h2>
                <p>
                    TimeTrove is a cutting-edge appointment scheduling application designed to revolutionize appointment management for service providers across various industries. In today's competitive market, efficient appointment handling and seamless client communication are crucial. Whether you're expanding into new markets or looking to enhance your current scheduling process, TimeTrove is your go-to solution.
                </p>
                <p>
                    Our platform caters to a wide range of users, from individual professionals to large service providers, offering a personalized experience through user profiles and comprehensive client management. With TimeTrove, you can effortlessly manage appointments, services, and client interactions all in one place.
                </p>
            </section>

            <section class="bg-white rounded shadow p-4">
                <h2>Key Features</h2>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Intuitive appointment scheduling for multiple services</li>
                    <li class="list-group-item">Comprehensive client information management</li>
                    <li class="list-group-item">Visual appointment calendar for effective schedule management</li>
                    <li class="list-group-item">Automated appointment reminders via email and SMS</li>
                    <li class="list-group-item">Customizable service offerings</li>
                    <li class="list-group-item">User-friendly interface for both service providers and clients</li>
                </ul>
            </section>
        </main>

        <footer class="mt-5 text-center text-muted">
            <p>&copy; 2024 TimeTrove. All rights reserved.</p>
        </footer>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>