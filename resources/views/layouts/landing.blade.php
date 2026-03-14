<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Aurex ERP — Next-Gen Business Platform')</title>
    <meta name="description" content="@yield('meta_description', 'Aurex ERP unifies finance, inventory, sales, HR, and operations into one powerful platform. Modern ERP for the next generation of enterprise.')">

    <!-- Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Design System -->
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">

    @yield('styles')
</head>
<body>

    <!-- Ambient Background -->
    <div class="site-bg"></div>
    <div class="bg-grid"></div>
    <div class="bg-grid"></div>

    <!-- ===================== NAVBAR ===================== -->
    <nav class="navbar" id="main-navbar">
        <div class="container">
            <a href="{{ route('landing.home') }}" class="nav-logo">
                <img src="{{ asset('images/landing/logo.png') }}" alt="Aurex ERP">
            </a>

            <ul class="nav-links">
                <li><a href="{{ route('landing.home') }}" class="{{ request()->routeIs('landing.home') ? 'active' : '' }}">Home</a></li>
                <li><a href="{{ route('landing.features') }}" class="{{ request()->routeIs('landing.features') ? 'active' : '' }}">Features</a></li>
                <li><a href="{{ route('landing.pricing') }}" class="{{ request()->routeIs('landing.pricing') ? 'active' : '' }}">Pricing</a></li>
                <li><a href="{{ route('landing.docs') }}" class="{{ request()->routeIs('landing.docs') ? 'active' : '' }}">Docs</a></li>
            </ul>

            <div class="nav-actions">
                <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Login</a>
                <a href="{{ route('landing.demo') }}" class="btn btn-primary btn-sm">Request Demo</a>
            </div>
        </div>
    </nav>

    <!-- ===================== PAGE CONTENT ===================== -->
    @yield('content')

    <!-- ===================== FOOTER ===================== -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <img src="{{ asset('images/landing/logo.png') }}" alt="Aurex ERP">
                    <p class="footer-desc">
                        The intelligent engine powering modern enterprises. Unify your entire business on one high-performance platform.
                    </p>
                    <div class="footer-socials">
                        <a href="#" class="footer-social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="footer-social-link"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="footer-social-link"><i class="fab fa-github"></i></a>
                        <a href="#" class="footer-social-link"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>

                <div>
                    <h4 class="footer-col-title">Product</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('landing.features') }}">Features</a></li>
                        <li><a href="{{ route('landing.pricing') }}">Pricing</a></li>
                        <li><a href="{{ route('landing.demo') }}">Live Demo</a></li>
                        <li><a href="{{ route('landing.docs') }}">Documentation</a></li>
                        <li><a href="#">Changelog</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="footer-col-title">Resources</h4>
                    <ul class="footer-links">
                        <li><a href="#">API Reference</a></li>
                        <li><a href="#">Integrations</a></li>
                        <li><a href="#">Community</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Status</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="footer-col-title">Company</h4>
                    <ul class="footer-links">
                        <li><a href="#">About</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="{{ route('landing.privacy') }}">Privacy Policy</a></li>
                        <li><a href="{{ route('landing.privacy') }}">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <span>&copy; {{ date('Y') }} Aurex ERP Solutions. All rights reserved.</span>
                <div style="display:flex; gap:1.5rem;">
                    <a href="#" style="color:inherit; text-decoration:none;">Terms of Service</a>
                    <a href="{{ route('landing.privacy') }}" style="color:inherit; text-decoration:none;">Privacy</a>
                    <a href="{{ route('landing.privacy') }}" style="color:inherit; text-decoration:none;">Privacy</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- ===================== SCRIPTS ===================== -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Navbar scroll effect
        const navbar = document.getElementById('main-navbar');
        const onScroll = () => {
            navbar.classList.toggle('scrolled', window.scrollY > 40);
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();

        // AOS (Animate on Scroll)
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in-view');
                }
            });
        }, { threshold: 0.1 });
        document.querySelectorAll('[data-aos]').forEach(el => observer.observe(el));

        // FAQ accordion
        document.querySelectorAll('.faq-question').forEach(btn => {
            btn.addEventListener('click', () => {
                const item = btn.closest('.faq-item');
                const isOpen = item.classList.contains('open');
                document.querySelectorAll('.faq-item.open').forEach(i => i.classList.remove('open'));
                if (!isOpen) item.classList.add('open');
            });
        });
    });
    </script>

    @yield('scripts')
</body>
</html>
