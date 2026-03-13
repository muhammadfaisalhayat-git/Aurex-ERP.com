@extends('layouts.landing')

@section('title', 'Pricing - Scale Your Enterprise with Aurex')

@section('content')
<section style="padding-top: 180px; position: relative;">
    <div class="container">
        <div style="text-align: center; max-width: 800px; margin: 0 auto 6rem;" data-aos>
            <span class="section-tag">Investment for Growth</span>
            <h1 style="font-size: 4rem; line-height: 1.1; font-weight: 800; letter-spacing: -2px; margin-bottom: 2rem; background: linear-gradient(to bottom right, #FFFFFF 50%, #94A3B8); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                Predictable Pricing for <br>Global Ambition
            </h1>
            <p style="color: var(--text-muted); font-size: 1.3rem;">From lean startups to multinational conglomerates, our plans scale with your complexity.</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 2rem; margin-bottom: 80px;">
            <!-- Starter -->
            <div class="bento-item" style="height: auto; padding: 3.5rem; justify-content: flex-start;" data-aos>
                <h3 style="color: var(--text-muted); font-size: 1.25rem; margin-bottom: 0.5rem;">Starter</h3>
                <div style="font-size: 4rem; font-weight: 800; color: white; margin-bottom: 1.5rem;">$49<span style="font-size: 1rem; color: var(--text-muted);">/mo</span></div>
                <p style="color: var(--text-muted); margin-bottom: 2.5rem;">Ideal for retail boutiques and micro-businesses.</p>
                <div style="height: 1px; background: var(--glass-border); margin-bottom: 2.5rem;"></div>
                <ul style="list-style: none; color: var(--text-muted); margin-bottom: 3rem; line-height: 2.5;">
                    <li><i class="fas fa-check" style="color: var(--accent); margin-right: 12px;"></i> Core Ledger & Taxes</li>
                    <li><i class="fas fa-check" style="color: var(--accent); margin-right: 12px;"></i> Individual POS Session</li>
                    <li><i class="fas fa-check" style="color: var(--accent); margin-right: 12px;"></i> Basic Stock Tracking</li>
                    <li><i class="fas fa-check" style="color: var(--accent); margin-right: 12px;"></i> Email Support</li>
                </ul>
                <a href="{{ route('landing.demo') }}" class="btn btn-secondary" style="width: 100%; text-align: center;">Begin Journey</a>
            </div>

            <!-- Pro -->
            <div class="bento-item" style="height: auto; padding: 3.5rem; border: 2px solid var(--accent); scale: 1.05; z-index: 10; justify-content: flex-start;" data-aos style="transition-delay: 0.1s">
                <div style="position: absolute; top: 1.5rem; right: 1.5rem; background: var(--accent); color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">RECOMMENDED</div>
                <h3 style="color: var(--accent); font-size: 1.25rem; margin-bottom: 0.5rem;">Professional</h3>
                <div style="font-size: 4rem; font-weight: 800; color: white; margin-bottom: 1.5rem;">$149<span style="font-size: 1rem; color: var(--text-muted);">/mo</span></div>
                <p style="color: var(--text-muted); margin-bottom: 2.5rem;">Advanced features for multi-branch companies.</p>
                <div style="height: 1px; background: var(--glass-border); margin-bottom: 2.5rem;"></div>
                <ul style="list-style: none; color: var(--text-main); margin-bottom: 3rem; line-height: 2.5;">
                    <li><i class="fas fa-check" style="color: var(--accent); margin-right: 12px;"></i> Everything in Starter</li>
                    <li><i class="fas fa-check" style="color: var(--accent); margin-right: 12px;"></i> Multi-Branch Sync</li>
                    <li><i class="fas fa-check" style="color: var(--accent); margin-right: 12px;"></i> Procurement Module</li>
                    <li><i class="fas fa-check" style="color: var(--accent); margin-right: 12px;"></i> API Data Access</li>
                </ul>
                <a href="{{ route('landing.demo') }}" class="btn btn-accent" style="width: 100%; text-align: center;">Execute Growth</a>
            </div>

            <!-- Enterprise -->
            <div class="bento-item" style="height: auto; padding: 3.5rem; justify-content: flex-start;" data-aos style="transition-delay: 0.2s">
                <h3 style="color: var(--text-muted); font-size: 1.25rem; margin-bottom: 0.5rem;">Enterprise</h3>
                <div style="font-size: 3.5rem; font-weight: 800; color: white; margin-bottom: 1.5rem;">Custom</div>
                <p style="color: var(--text-muted); margin-bottom: 2.5rem;">Limitless potential for global enterprises.</p>
                <div style="height: 1px; background: var(--glass-border); margin-bottom: 2.5rem;"></div>
                <ul style="list-style: none; color: var(--text-muted); margin-bottom: 3rem; line-height: 2.5;">
                    <li><i class="fas fa-check" style="color: var(--accent); margin-right: 12px;"></i> Unlimited Scale</li>
                    <li><i class="fas fa-check" style="color: var(--accent); margin-right: 12px;"></i> Dedicated Cloud Instance</li>
                    <li><i class="fas fa-check" style="color: var(--accent); margin-right: 12px;"></i> Custom Integrations</li>
                    <li><i class="fas fa-check" style="color: var(--accent); margin-right: 12px;"></i> 24/7 Strategic Support</li>
                </ul>
                <a href="{{ route('landing.demo') }}" class="btn btn-secondary" style="width: 100%; text-align: center;">Contact Syndicate</a>
            </div>
        </div>
    </div>
</section>

<!-- Comparison Table Link / CTA -->
<section style="padding-bottom: 120px;">
    <div class="container">
        <div style="text-align: center; color: var(--text-muted);" data-aos>
            <p>Need a more granular breakdown? <a href="{{ route('landing.features') }}" style="color: var(--accent); font-weight: 600;">View Feature Matrix →</a></p>
        </div>
    </div>
</section>
@endsection
