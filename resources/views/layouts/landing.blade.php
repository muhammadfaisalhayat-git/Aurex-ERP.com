<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Aurex ERP - Smart Business Management')</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">

    @yield('styles')
</head>
<body>
    <!-- Background Layers -->
    <div class="mesh-bg"></div>
    <div class="ambient-light ambient-1"></div>
    <div class="ambient-light ambient-2"></div>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <a href="{{ route('landing.home') }}" class="logo">
                <img src="{{ asset('images/landing/logo.png') }}" alt="Aurex ERP Logo">
            </a>
            
            <ul class="nav-links">
                <li><a href="{{ route('landing.home') }}" class="nav-link {{ request()->routeIs('landing.home') ? 'active' : '' }}">Home</a></li>
                <li><a href="{{ route('landing.features') }}" class="nav-link {{ request()->routeIs('landing.features') ? 'active' : '' }}">Features</a></li>
                <li><a href="{{ route('landing.pricing') }}" class="nav-link {{ request()->routeIs('landing.pricing') ? 'active' : '' }}">Pricing</a></li>
                <li><a href="{{ route('landing.docs') }}" class="nav-link {{ request()->routeIs('landing.docs') ? 'active' : '' }}">Docs</a></li>
            </ul>

            <div class="nav-btns">
                <a href="{{ route('login') }}" class="btn btn-secondary">Login</a>
                <a href="{{ route('landing.demo') }}" class="btn btn-primary">Request Demo</a>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    @yield('content')

    <!-- Premium Footer -->
    <footer class="footer" style="background: rgba(2, 6, 23, 0.5); padding: 100px 0 40px; border-top: 1px solid var(--glass-border); margin-top: 120px; backdrop-filter: blur(20px);">
        <div class="container">
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 4rem; margin-bottom: 80px;">
                <div>
                    <a href="#" class="logo" style="margin-bottom: 1rem; display: inline-flex;">
                        <img src="{{ asset('images/landing/logo.png') }}" alt="Aurex ERP Logo" style="height: 150px;">
                    </a>
                    <p style="color: var(--text-muted); font-size: 1.1rem; margin-bottom: 2rem; max-width: 400px;">
                        The intelligent engine driving modern enterprises. Unify your operations with our next-generation ERP platform.
                    </p>
                    <div style="display: flex; gap: 1.5rem; font-size: 1.5rem;">
                        <a href="#" style="color: var(--text-muted); transition: 0.3s;"><i class="fab fa-twitter"></i></a>
                        <a href="#" style="color: var(--text-muted); transition: 0.3s;"><i class="fab fa-linkedin"></i></a>
                        <a href="#" style="color: var(--text-muted); transition: 0.3s;"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                
                <div>
                    <h4 style="margin-bottom: 2rem;">Product</h4>
                    <ul style="list-style: none; color: var(--text-muted); line-height: 2.5;">
                        <li><a href="{{ route('landing.features') }}" style="color: inherit; text-decoration: none;">Features</a></li>
                        <li><a href="{{ route('landing.pricing') }}" style="color: inherit; text-decoration: none;">Pricing</a></li>
                        <li><a href="{{ route('landing.demo') }}" style="color: inherit; text-decoration: none;">Live Demo</a></li>
                        <li><a href="{{ route('landing.docs') }}" style="color: inherit; text-decoration: none;">Docs</a></li>
                    </ul>
                </div>

                <div>
                    <h4 style="margin-bottom: 2rem;">Resources</h4>
                    <ul style="list-style: none; color: var(--text-muted); line-height: 2.5;">
                        <li><a href="#" style="color: inherit; text-decoration: none;">Marketplace</a></li>
                        <li><a href="#" style="color: inherit; text-decoration: none;">API Reference</a></li>
                        <li><a href="#" style="color: inherit; text-decoration: none;">Community</a></li>
                        <li><a href="#" style="color: inherit; text-decoration: none;">Cloud Status</a></li>
                    </ul>
                </div>

                <div>
                    <h4 style="margin-bottom: 2rem;">Company</h4>
                    <ul style="list-style: none; color: var(--text-muted); line-height: 2.5;">
                        <li><a href="#" style="color: inherit; text-decoration: none;">About</a></li>
                        <li><a href="#" style="color: inherit; text-decoration: none;">Careers</a></li>
                        <li><a href="#" style="color: inherit; text-decoration: none;">Privacy</a></li>
                        <li><a href="#" style="color: inherit; text-decoration: none;">Security</a></li>
                    </ul>
                </div>
            </div>
            
            <div style="text-align: center; color: var(--text-muted); border-top: 1px solid var(--glass-border); padding-top: 40px;">
                <p>&copy; {{ date('Y') }} Aurex ERP Solutions. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Navbar effect
            const navbar = document.querySelector('.navbar');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });

            // AOS Simulation
            const observerOptions = {
                threshold: 0.1
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('aos-animate');
                    }
                });
            }, observerOptions);

            document.querySelectorAll('[data-aos]').forEach(el => observer.observe(el));
        });
    </script>
    @yield('scripts')
</body>
</html>
