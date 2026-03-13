@extends('layouts.landing')

@section('title', 'Features — Aurex ERP')
@section('meta_description', 'Explore all the features of Aurex ERP. Finance, Sales, Inventory, HR, POS, Projects, and more.')

@section('content')

{{-- Hero --}}
<section class="hero" style="min-height: 50vh; padding-top: 120px; padding-bottom: 60px;">
    <div class="hero-glow"></div>
    <div class="container" style="text-align: center;">
        <div data-aos>
            <span class="section-tag">Full Feature Set</span>
            <h1 class="hero-title" style="font-size: clamp(2rem, 4vw, 3.5rem);">
                Everything Included.<br>
                <span class="gradient-text">No Hidden Extras.</span>
            </h1>
            <p class="hero-subtitle" style="margin: 1.5rem auto; max-width: 600px;">
                Every Aurex ERP plan unlocks a full-stack business operating system — designed for real-world enterprise workflows.
            </p>
        </div>
    </div>
</section>

{{-- Feature Categories --}}
<section class="section">
    <div class="container">

        {{-- Finance --}}
        <div style="margin-bottom: 5rem;" data-aos>
            <span class="section-tag"><i class="fas fa-chart-pie"></i> Finance & Accounting</span>
            <h2 class="section-title" style="font-size: 2rem; margin-bottom: 2rem;">Complete Financial Control</h2>
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-book"></i></div>
                    <h3>Double-Entry Ledger</h3>
                    <p>Fully compliant double-entry bookkeeping that auto-posts every transaction in real time.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-coins"></i></div>
                    <h3>Multi-Currency</h3>
                    <p>Support for unlimited currencies with live exchange rates and gain/loss tracking.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                    <h3>Invoicing & Billing</h3>
                    <p>Create, send, and track professional invoices. Automated reminders and payment matching.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-calculator"></i></div>
                    <h3>VAT & Tax Reporting</h3>
                    <p>Jurisdiction-aware tax rules and one-click tax report generation for compliance.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-building-columns"></i></div>
                    <h3>Bank Reconciliation</h3>
                    <p>Import bank statements and auto-match transactions in seconds.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                    <h3>Financial Statements</h3>
                    <p>P&L, Balance Sheet, and Cash Flow statements generated automatically — by period or branch.</p>
                </div>
            </div>
        </div>

        {{-- Inventory --}}
        <div style="margin-bottom: 5rem;" data-aos>
            <span class="section-tag"><i class="fas fa-boxes-stacked"></i> Inventory & Warehousing</span>
            <h2 class="section-title" style="font-size: 2rem; margin-bottom: 2rem;">Full Inventory Precision</h2>
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-icon green"><i class="fas fa-tags"></i></div>
                    <h3>Product Catalog</h3>
                    <p>Manage products, variants, barcodes, SKUs, units of measure, and product images.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon green"><i class="fas fa-warehouse"></i></div>
                    <h3>Multi-Warehouse</h3>
                    <p>Track stock levels per warehouse. Create inter-branch transfer orders seamlessly.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon green"><i class="fas fa-bell"></i></div>
                    <h3>Reorder Alerts</h3>
                    <p>Set reorder points per product and get automated alerts when stock falls below threshold.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon green"><i class="fas fa-layer-group"></i></div>
                    <h3>Batch & Serial Tracking</h3>
                    <p>Track inventory by batch number or serial number for full traceability and recall support.</p>
                </div>
            </div>
        </div>

        {{-- HR --}}
        <div style="margin-bottom: 5rem;" data-aos>
            <span class="section-tag"><i class="fas fa-users"></i> HR & Payroll</span>
            <h2 class="section-title" style="font-size: 2rem; margin-bottom: 2rem;">Your People, Managed Professionally</h2>
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-icon pink"><i class="fas fa-user-plus"></i></div>
                    <h3>Employee Onboarding</h3>
                    <p>Add employees, assign roles, manage contracts, and set up payroll in minutes.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon pink"><i class="fas fa-calendar-days"></i></div>
                    <h3>Attendance & Leave</h3>
                    <p>Track attendance, manage leave balances, and process approvals automatically.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon pink"><i class="fas fa-money-bill-wave"></i></div>
                    <h3>Payroll Processing</h3>
                    <p>One-click payroll runs with deductions, allowances, and tax calculations built in.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon pink"><i class="fas fa-file-pdf"></i></div>
                    <h3>Payslips & Reports</h3>
                    <p>Generate payslips and HR reports automatically for any pay period.</p>
                </div>
            </div>
        </div>

    </div>
</section>

{{-- CTA --}}
<section class="section-sm">
    <div class="container">
        <div class="cta-block" data-aos>
            <h2>See Every Feature in Action</h2>
            <p>Book a personalized demo and we'll walk you through exactly the modules relevant to your business.</p>
            <div class="cta-actions">
                <a href="{{ route('landing.demo') }}" class="btn btn-accent btn-lg">Request Demo</a>
                <a href="{{ route('landing.pricing') }}" class="btn btn-ghost btn-lg">View Pricing</a>
            </div>
        </div>
    </div>
</section>

@endsection
