@extends('layouts.landing')

@section('title', 'Pricing — Aurex ERP')
@section('meta_description', 'Simple, transparent pricing for Aurex ERP. Starter, Business, and Enterprise plans for teams of all sizes.')

@section('content')

{{-- Hero --}}
<section class="hero" style="min-height: 40vh; padding-top: 140px; padding-bottom: 80px; position: relative; z-index: 10;">
    <div class="hero-glow"></div>
    <div class="container" style="text-align: center;">
        <div data-aos>
            <span class="section-tag">Pricing</span>
            <h1 class="hero-title" style="font-size: clamp(2.5rem, 5vw, 4rem);">
                Simple, Transparent Pricing.<br>
                <span class="gradient-text">No Surprises.</span>
            </h1>
            <p class="hero-subtitle" style="margin: 1.5rem auto; max-width: 640px; font-size: 1.15rem;">
                Every plan includes the full Aurex ERP platform. You pay for users, not modules.
                No lock-in. Cancel any time.
            </p>
        </div>
    </div>
</section>

{{-- Pricing Cards --}}
<section class="section">
    <div class="container">
        <div class="pricing-grid" data-aos>
            {{-- Starter --}}
            <div class="pricing-card">
                <div class="pricing-plan">Starter</div>
                <div class="pricing-price"><sup>$</sup>49<span>/mo</span></div>
                <div class="pricing-desc">For small teams ready to replace spreadsheets with a real system.</div>
                <div class="pricing-divider"></div>
                <ul class="pricing-features">
                    <li><i class="fas fa-check check"></i> Up to 5 users</li>
                    <li><i class="fas fa-check check"></i> Finance & Accounting</li>
                    <li><i class="fas fa-check check"></i> Inventory (1 warehouse)</li>
                    <li><i class="fas fa-check check"></i> Sales module</li>
                    <li><i class="fas fa-check check"></i> Standard reports</li>
                    <li><i class="fas fa-check check"></i> Email support</li>
                    <li><i class="fas fa-check check"></i> 10 GB storage</li>
                    <li class="muted"><i class="fas fa-times cross"></i> HR & Payroll</li>
                    <li class="muted"><i class="fas fa-times cross"></i> POS</li>
                    <li class="muted"><i class="fas fa-times cross"></i> Multi-branch</li>
                    <li class="muted"><i class="fas fa-times cross"></i> API access</li>
                </ul>
                <a href="{{ route('landing.demo') }}" class="btn btn-outline" style="width:100%; justify-content:center;">Get Started</a>
            </div>

            {{-- Business --}}
            <div class="pricing-card featured">
                <div class="pricing-badge">Most Popular</div>
                <div class="pricing-plan">Business</div>
                <div class="pricing-price"><sup>$</sup>149<span>/mo</span></div>
                <div class="pricing-desc">The complete ERP for growing businesses. Everything included, nothing held back.</div>
                <div class="pricing-divider"></div>
                <ul class="pricing-features">
                    <li><i class="fas fa-check check"></i> Up to 25 users</li>
                    <li><i class="fas fa-check check"></i> All Finance modules</li>
                    <li><i class="fas fa-check check"></i> Inventory (3 warehouses)</li>
                    <li><i class="fas fa-check check"></i> Sales & CRM</li>
                    <li><i class="fas fa-check check"></i> Purchases & Vendors</li>
                    <li><i class="fas fa-check check"></i> HR & Payroll</li>
                    <li><i class="fas fa-check check"></i> POS module</li>
                    <li><i class="fas fa-check check"></i> Projects & Operations</li>
                    <li><i class="fas fa-check check"></i> Advanced reports</li>
                    <li><i class="fas fa-check check"></i> 100 GB storage</li>
                    <li class="muted"><i class="fas fa-times cross"></i> Custom workflows</li>
                    <li class="muted"><i class="fas fa-times cross"></i> API access</li>
                </ul>
                <a href="{{ route('landing.demo') }}" class="btn btn-primary" style="width:100%; justify-content:center;">Start Free Trial</a>
            </div>

            {{-- Enterprise --}}
            <div class="pricing-card">
                <div class="pricing-plan">Enterprise</div>
                <div class="pricing-price" style="font-size:2.2rem; letter-spacing:-1px;">Custom</div>
                <div class="pricing-desc">For large organizations that need unlimited scale, security, and dedicated support.</div>
                <div class="pricing-divider"></div>
                <ul class="pricing-features">
                    <li><i class="fas fa-check check"></i> Unlimited users</li>
                    <li><i class="fas fa-check check"></i> All Business plan features</li>
                    <li><i class="fas fa-check check"></i> Unlimited warehouses</li>
                    <li><i class="fas fa-check check"></i> Multi-branch management</li>
                    <li><i class="fas fa-check check"></i> Custom workflows</li>
                    <li><i class="fas fa-check check"></i> Full API access</li>
                    <li><i class="fas fa-check check"></i> Custom integrations</li>
                    <li><i class="fas fa-check check"></i> Dedicated account manager</li>
                    <li><i class="fas fa-check check"></i> 99.9% SLA guarantee</li>
                    <li><i class="fas fa-check check"></i> On-premise option</li>
                    <li><i class="fas fa-check check"></i> Unlimited storage</li>
                </ul>
                <a href="{{ route('landing.demo') }}" class="btn btn-ghost" style="width:100%; justify-content:center;">Contact Sales</a>
            </div>
        </div>

        {{-- Feature comparison note --}}
        <div style="text-align:center; margin-top: 4rem; color: var(--text-secondary); font-size: 0.9rem;" data-aos>
            <p>All plans include a <strong style="color: var(--text-primary);">14-day free trial</strong> with no credit card required. 
            Need help choosing? <a href="{{ route('landing.demo') }}" style="color: var(--brand-primary);">Talk to our team →</a></p>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="section-sm">
    <div class="container">
        <div class="cta-block" data-aos>
            <h2>Start Your Free Trial Today</h2>
            <p>No credit card required. Get up and running in minutes with your 14-day free Business trial.</p>
            <div class="cta-actions">
                <a href="{{ route('landing.demo') }}" class="btn btn-accent btn-lg">
                    <i class="fas fa-rocket"></i> Book a Demo
                </a>
                <a href="{{ route('login') }}" class="btn btn-ghost btn-lg">Go to Portal</a>
            </div>
        </div>
    </div>
</section>

@endsection
