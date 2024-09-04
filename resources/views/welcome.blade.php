<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to TimeTrove</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7f7f7;
            padding-top: 56px;
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

        .feature-card,
        .info-card,
        .pricing-card {
            transition: transform 0.3s;
            background-color: #ffffff;
            border-radius: .375rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .feature-card:hover,
        .info-card:hover,
        .pricing-card:hover {
            transform: translateY(-5px);
        }

        .feature-card .feature-icon {
            color: #1d72b8;
        }

        .info-card {
            padding: 20px;
            margin-bottom: 20px;
        }

        .pricing {
            background-color: #ffffff;
            padding: 60px 0;
        }

        .pricing-card {
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid #e1e5ea;
        }

        .pricing-card h3 {
            font-size: 1.75rem;
        }

        .pricing-card .price {
            font-size: 2.5rem;
            font-weight: bold;
            color: #1d72b8;
        }

        .pricing-card .plan-details ul {
            list-style: none;
            padding: 0;
        }

        .pricing-card .plan-details ul li {
            border-bottom: 1px solid #e9ecef;
            padding: 10px 0;
        }

        .pricing-card .btn-signup {
            background-color: #1d72b8;
            color: #ffffff;
            border-color: #1d72b8;
        }

        .pricing-card .btn-signup:hover {
            background-color: #1559b0;
            border-color: #144b8a;
        }

        .text-industry {
            color: #1d72b8;
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
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">TimeTrove</a>
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
                    <li class="nav-item">
                        <a class="btn btn-outline-primary ms-2" href="{{ url('/home') }}">Dashboard</a>
                    </li>
                    @else
                    <li class="nav-item">
                        <a class="btn btn-primary ms-2" href="{{ route('login') }}">Log in</a>
                    </li>
                    @if (Route::has('register'))
                    <li class="nav-item">
                        <a class="btn btn-outline-primary ms-2" href="{{ route('register') }}">Register</a>
                    </li>
                    @endif
                    @endauth
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-5">
        <header class="text-center mb-5">
            <h1 class="display-4 text-primary fw-bold">Welcome to TimeTrove</h1>
            <p class="lead">Streamline Your Appointment Management</p>
            <a href="#" class="btn btn-primary btn-lg mt-3">Get Started</a>
        </header>

        <!-- Features Section -->
        <section class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="feature-card p-4 rounded">
                    <div class="feature-icon mb-3">
                        <i class="bi bi-calendar-check fs-2"></i>
                    </div>
                    <h4>Online Booking</h4>
                    <p>Let your clients book appointments through your website or social media.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-card p-4 rounded">
                    <div class="feature-icon mb-3">
                        <i class="bi bi-bell fs-2"></i>
                    </div>
                    <h4>Automated Reminders</h4>
                    <p>Reduce no-shows with automatic email and SMS reminders.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-card p-4 rounded">
                    <div class="feature-icon mb-3">
                        <i class="bi bi-credit-card fs-2"></i>
                    </div>
                    <h4>Payments & Invoicing</h4>
                    <p>Accept payments online and streamline your invoicing process.</p>
                </div>
            </div>
        </section>

        <!-- Integration Section -->
        <section class="row text-center mt-5">
            <div class="col-md-6 mb-4">
                <div class="feature-card p-4 rounded">
                    <div class="feature-icon mb-3">
                        <i class="bi bi-calendar-range fs-2"></i>
                    </div>
                    <h4>Calendar Integration</h4>
                    <p>Sync with Google, Outlook, and iCal to keep all your appointments in one place.</p>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="feature-card p-4 rounded">
                    <div class="feature-icon mb-3">
                        <i class="bi bi-people fs-2"></i>
                    </div>
                    <h4>Customer Management</h4>
                    <p>Track customer details and appointment history easily.</p>
                </div>
            </div>
        </section>
    </div>

    <!-- Industry Segments Section -->
    <div class="container py-5">
        <section class="text-center">
            <h2 class="mb-4 text-primary">Perfect for Any Industry</h2>
            <p class="lead mb-5">From healthcare to consulting, TimeTrove adapts to your business needs.</p>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="info-card p-4 rounded">
                        <i class="bi bi-hospital fs-2 text-industry"></i>
                        <h4 class="mt-3">Healthcare</h4>
                        <p>Manage patient appointments and records efficiently.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="info-card p-4 rounded">
                        <i class="bi bi-briefcase fs-2 text-industry"></i>
                        <h4 class="mt-3">Business</h4>
                        <p>Coordinate meetings, manage client interactions, and track progress.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="info-card p-4 rounded">
                        <i class="bi bi-person-workspace fs-2 text-industry"></i>
                        <h4 class="mt-3">Freelancers</h4>
                        <p>Organize your freelance schedule and keep your clients updated.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Pricing Section -->
    <div class="pricing py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="text-primary">Choose Your Plan</h2>
                <p class="lead">Affordable pricing for every business size.</p>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="pricing-card text-center rounded">
                        <h3>Basic</h3>
                        <div class="price">$19/month</div>
                        <div class="plan-details mt-4">
                            <ul>
                                <li>Basic Features</li>
                                <li>Up to 10 Users</li>
                                <li>Email Support</li>
                            </ul>
                        </div>
                        <a href="#" class="btn btn-signup mt-4">Sign Up</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="pricing-card text-center rounded">
                        <h3>Professional</h3>
                        <div class="price">$49/month</div>
                        <div class="plan-details mt-4">
                            <ul>
                                <li>All Basic Features</li>
                                <li>Up to 50 Users</li>
                                <li>Priority Support</li>
                                <li>Integration Features</li>
                            </ul>
                        </div>
                        <a href="#" class="btn btn-signup mt-4">Sign Up</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="pricing-card text-center rounded">
                        <h3>Enterprise</h3>
                        <div class="price">$99/month</div>
                        <div class="plan-details mt-4">
                            <ul>
                                <li>All Professional Features</li>
                                <li>Unlimited Users</li>
                                <li>Dedicated Account Manager</li>
                                <li>Custom Solutions</li>
                            </ul>
                        </div>
                        <a href="#" class="btn btn-signup mt-4">Sign Up</a>
                    </div>
                </div>
            </div>
        </div>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>