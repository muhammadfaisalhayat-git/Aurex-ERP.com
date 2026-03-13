@extends('layouts.landing')

@section('title', 'Aurex ERP - The Intelligent Engine for Modern Business')

@section('content')
<!-- High-End Hero Section -->
<section class="hero">
    <div class="container" style="display: flex; align-items: center; justify-content: space-between;">
        <div class="hero-content" data-aos>
            <span class="section-tag">Next-Gen Business Platform</span>
            <h1>The Intelligent Engine for <br>Modern Enterprise</h1>
            <p>Aurex ERP unifies your entire business operations into a single, high-performance platform. Experience real-time visibility and automated scaling.</p>
            <div class="hero-btns" style="display: flex; gap: 1.5rem;">
                <a href="{{ route('landing.demo') }}" class="btn btn-accent">Request Live Demo</a>
                <a href="{{ route('landing.features') }}" class="btn btn-secondary">Explore Platform</a>
            </div>
        </div>
        
        <div class="hero-visual float" data-aos style="transition-delay: 0.2s">
            <div class="hero-mockup-wrapper">
                <img src="{{ asset('images/landing/dashboard_mockup.png') }}" alt="Aurex ERP Dashboard Mockup">
                <!-- Ambient Glow Decoration -->
                <div style="position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; background: var(--accent); filter: blur(40px); opacity: 0.3; border-radius: 50%;"></div>
            </div>
        </div>
    </div>
</section>

<!-- Trust Metric Layer -->
<section style="padding: 60px 0; background: rgba(255,255,255,0.02); border-top: 1px solid var(--glass-border); border-bottom: 1px solid var(--glass-border);">
    <div class="container">
        <div style="display: flex; justify-content: space-around; align-items: center; flex-wrap: wrap; gap: 3rem;">
            <div style="text-align: center;" data-aos>
                <div style="font-size: 2.5rem; font-weight: 800; color: white;">40%</div>
                <div style="color: var(--text-muted); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px;">Efficiency Gains</div>
            </div>
            <div style="text-align: center;" data-aos style="transition-delay: 0.1s">
                <div style="font-size: 2.5rem; font-weight: 800; color: white;">2.5x</div>
                <div style="color: var(--text-muted); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px;">Faster Growth</div>
            </div>
            <div style="text-align: center;" data-aos style="transition-delay: 0.2s">
                <div style="font-size: 2.5rem; font-weight: 800; color: white;">100%</div>
                <div style="color: var(--text-muted); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px;">Cloud Native</div>
            </div>
            <div style="text-align: center;" data-aos style="transition-delay: 0.3s">
                <div style="font-size: 2.5rem; font-weight: 800; color: white;">SEC</div>
                <div style="color: var(--text-muted); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px;">Grade Security</div>
            </div>
        </div>
    </div>
</section>

<!-- Bento Grid Features -->
<section class="glass-section">
    <div class="container">
        <div style="text-align: center; max-width: 800px; margin: 0 auto 5rem;" data-aos>
            <span class="section-tag">Core Capabilities</span>
            <h2 style="font-size: 3.5rem; margin-bottom: 1.5rem; font-weight: 800;">Integrated from the Ground Up</h2>
            <p style="color: var(--text-muted); font-size: 1.2rem;">Don't just manage. Orchestrate your business with a unified data layer that connects every department.</p>
        </div>

        <div class="bento-grid">
            <div class="bento-item bento-1" data-aos>
                <div class="bento-icon"><i class="fas fa-chart-pie"></i></div>
                <h3>Advanced Financial Hub</h3>
                <p>Complete multi-currency accounting with real-time tax compliance and automated reconciliation. The source of truth for your CFO.</p>
                <div style="position: absolute; bottom: 0; right: 0; padding: 2rem; opacity: 0.1; font-size: 8rem;"><i class="fas fa-vault"></i></div>
            </div>
            
            <div class="bento-item bento-2" data-aos style="transition-delay: 0.1s">
                <div class="bento-icon" style="color: #4ADE80; filter: drop-shadow(0 0 10px #4ADE80);"><i class="fas fa-boxes-stacked"></i></div>
                <h3>Smart Inventory Engine</h3>
                <p>Track millions of SKUs across global warehouses with AI-driven demand forecasting and batch tracking.</p>
            </div>

            <div class="bento-item bento-3" data-aos style="transition-delay: 0.2s">
                <div class="bento-icon" style="color: #FB923C; filter: drop-shadow(0 0 10px #FB923C);"><i class="fas fa-cash-register"></i></div>
                <h3>Unified POS</h3>
                <p>Seamless retail experiences with offline-first point of sale and instant inventory sync.</p>
            </div>

            <div class="bento-item bento-4" data-aos style="transition-delay: 0.3s">
                <div class="bento-icon" style="color: #F472B6; filter: drop-shadow(0 0 10px #F472B6);"><i class="fas fa-shield-halved"></i></div>
                <h3>E2E Security</h3>
                <p>Enterprise-grade encryption and role-based access to keep your mission-critical data safe.</p>
            </div>
        </div>
    </div>
</section>

<!-- High-Gloss CTA -->
<section style="padding: 120px 0;">
    <div class="container">
        <div style="background: radial-gradient(circle at top right, var(--accent) 0%, var(--primary) 100%); padding: 80px; border-radius: 40px; text-align: center; border: 1px solid var(--glass-border); box-shadow: var(--shadow-premium); overflow: hidden; position: relative;" data-aos>
            <!-- Background Glows -->
            <div style="position: absolute; top: -50px; left: -50px; width: 300px; height: 300px; background: white; opacity: 0.05; border-radius: 50%; filter: blur(80px);"></div>
            
            <h2 style="font-size: 3.5rem; color: white; margin-bottom: 2rem; font-weight: 800;">Ready to Modernize?</h2>
            <p style="color: rgba(255,255,255,0.7); font-size: 1.3rem; margin-bottom: 3rem; max-width: 700px; margin-left: auto; margin-right: auto;">
                Join the fastest-growing businesses scaling on the Aurex platform. Your transformation starts here.
            </p>
            <div style="display: flex; gap: 1.5rem; justify-content: center;">
                <a href="{{ route('landing.demo') }}" class="btn btn-primary" style="padding: 1.2rem 3rem; font-size: 1.1rem;">Schedule Your Onboarding</a>
                <a href="{{ route('login') }}" class="btn btn-secondary" style="padding: 1.2rem 3rem; font-size: 1.1rem; background: rgba(255,255,255,0.1);">Visit Portal</a>
            </div>
        </div>
    </div>
</section>
@endsection
