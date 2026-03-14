@extends('layouts.landing')

@section('title', 'Privacy Policy & Terms — Aurex ERP')
@section('meta_description', 'Read the privacy policy and terms of service for Aurex ERP. We are committed to protecting your business data.')

@section('content')

{{-- Hero --}}
<section class="hero" style="min-height: 40vh; padding-top: 140px; padding-bottom: 80px; position: relative; z-index: 10;">
    <div class="hero-glow"></div>
    <div class="container" style="text-align: center;">
        <div data-aos>
            <span class="section-tag">Legal & Trust</span>
            <h1 class="hero-title" style="font-size: clamp(2.5rem, 5vw, 4rem);">
                Privacy & <span class="gradient-text">Terms</span>
            </h1>
            <p class="hero-subtitle" style="margin: 1.5rem auto; max-width: 640px; font-size: 1.15rem;">
                Last Updated: March 14, 2026. We take your security and data privacy seriously. 
                Transparent policies for a professional partnership.
            </p>
        </div>
    </div>
</section>

{{-- Policy Content --}}
<section class="section" style="padding-top: 0;">
    <div class="container" style="max-width: 900px;">
        <div class="glass-card" style="padding: 4rem; position: relative; z-index: 10;">
            
            <div style="margin-bottom: 3rem;">
                <h2 style="font-size: 1.8rem; margin-bottom: 1.5rem; color: var(--text-primary);">1. Data Protection Commitment</h2>
                <p style="color: var(--text-secondary); line-height: 1.8;">
                    Aurex ERP is built on a foundation of trust. We implement SEC-grade security measures to ensure that your enterprise data, inclusive of financial records, employee information, and inventory levels, remain encrypted and accessible only by your authorized personnel.
                </p>
                <ul style="margin-top: 1rem; color: var(--text-secondary); list-style: circle; padding-left: 1.5rem;">
                    <li>AES-256 encryption at rest and in transit</li>
                    <li>Regular third-party security audits</li>
                    <li>No data selling or unauthorized third-party sharing</li>
                </ul>
            </div>

            <div style="margin-bottom: 3rem;">
                <h2 style="font-size: 1.8rem; margin-bottom: 1.5rem; color: var(--text-primary);">2. Information Collection</h2>
                <p style="color: var(--text-secondary); line-height: 1.8;">
                    We collect information necessary to provide and improve the Aurex platform. This includes account details, usage logs for security auditing, and configuration data. We do not inspect your business logic or private financial transactions unless explicitly requested for support purposes.
                </p>
            </div>

            <div style="margin-bottom: 3rem;">
                <h2 style="font-size: 1.8rem; margin-bottom: 1.5rem; color: var(--text-primary);">3. Terms of Service</h2>
                <p style="color: var(--text-secondary); line-height: 1.8;">
                    By using Aurex ERP, you agree to our terms. This includes fair usage of our cloud resources, adherence to local tax and financial regulations, and maintaining the confidentiality of your administrative credentials.
                </p>
            </div>

            <div style="margin-bottom: 3rem;">
                <h2 style="font-size: 1.8rem; margin-bottom: 1.5rem; color: var(--text-primary);">4. Multi-Tenant Isolation</h2>
                <p style="color: var(--text-secondary); line-height: 1.8;">
                    Our architecture ensures total isolation between client organizations. Your data is housed in dedicated logical partitions, preventing any possibility of data leakage between different Aurex ERP subscribers.
                </p>
            </div>

            <div style="margin-bottom: 3rem;">
                <h2 style="font-size: 1.8rem; margin-bottom: 1.5rem; color: var(--text-primary);">5. Contact Legal</h2>
                <p style="color: var(--text-secondary); line-height: 1.8;">
                    Questions regarding our privacy compliance or terms can be directed to our legal team at <a href="mailto:legal@aurexerp.com" style="color: var(--brand-primary);">legal@aurexerp.com</a>.
                </p>
            </div>

            <div style="margin-top: 4rem; padding-top: 2rem; border-top: 1px solid var(--border-soft); text-align: center;">
                <a href="{{ route('landing.demo') }}" class="btn btn-primary">Download Full Legal PDF</a>
            </div>

        </div>
    </div>
</section>

@endsection
