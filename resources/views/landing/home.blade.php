@extends('layouts.landing')

@section('title', 'Aurex ERP — Next-Gen Business Platform')
@section('meta_description', 'Run finance, inventory, sales, HR, and operations from one intelligent system. Aurex ERP — the next-generation business platform.')

@section('content')

{{-- ====================================================================
     1. HERO
     ==================================================================== --}}
<section class="hero section-lg">
    <div class="hero-glow"></div>
    <div class="container">
        <div class="hero-inner" style="position: relative; z-index: 10;">

            {{-- Left: Copy --}}
            <div data-aos>
                <div class="hero-badge">
                    <span class="hero-badge-dot"><i class="fas fa-bolt" style="color:white;"></i></span>
                    Next-Gen Business Platform
                </div>

                <h1 class="hero-title">
                    The Intelligent Engine<br>
                    for <span class="gradient-text">Modern Enterprise</span>
                </h1>

                <p class="hero-subtitle">
                    Run finance, inventory, sales, HR, and operations from one unified system.
                    Real-time data. Role-based control. Built for the businesses of tomorrow.
                </p>

                <div class="hero-actions">
                    <a href="{{ route('landing.demo') }}" class="btn btn-accent btn-lg">
                        <i class="fas fa-play"></i> Request Live Demo
                    </a>
                    <a href="{{ route('landing.features') }}" class="btn btn-ghost btn-lg">
                        Explore Platform <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="hero-stats">
                    <div>
                        <div class="hero-stat-num">40%</div>
                        <div class="hero-stat-label">Efficiency Gains</div>
                    </div>
                    <div>
                        <div class="hero-stat-num">2.5×</div>
                        <div class="hero-stat-label">Faster Operations</div>
                    </div>
                    <div>
                        <div class="hero-stat-num">99.9%</div>
                        <div class="hero-stat-label">Uptime SLA</div>
                    </div>
                    <div>
                        <div class="hero-stat-num">50+</div>
                        <div class="hero-stat-label">Business Modules</div>
                    </div>
                </div>
            </div>

            {{-- Right: Mockup --}}
            <div class="hero-visual float" data-aos style="transition-delay:0.2s;">
                <div class="hero-mockup">
                    <img src="{{ asset('images/landing/dashboard_mockup.png') }}" alt="Aurex ERP Dashboard">
                    <div class="hero-mockup-glow"></div>
                </div>

                {{-- Floating indicators --}}
                <div class="hero-float-card card-1">
                    <span style="color:var(--success); font-size:1.4rem;">●</span>
                    <div>
                        <div style="font-weight:700; font-size:0.9rem; color: var(--text-primary);">Revenue Up 34%</div>
                        <div style="color:var(--text-secondary); font-size:0.8rem;">vs last quarter</div>
                    </div>
                </div>
                <div class="hero-float-card card-2">
                    <span style="background:var(--grad-primary); -webkit-background-clip:text; -webkit-text-fill-color:transparent; font-size:1.4rem; font-weight:800;">✓</span>
                    <div>
                        <div style="font-weight:700; font-size:0.9rem; color: var(--text-primary);">Approval Sent</div>
                        <div style="color:var(--text-secondary); font-size:0.8rem;">Purchase Order #4821</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ====================================================================
     2. TRUST STRIP
     ==================================================================== --}}
<section class="trust-strip">
    <div class="container">
        <div class="trust-inner">
            <div class="trust-metric" data-aos>
                <div class="trust-metric-num">99.9%</div>
                <div class="trust-metric-label">Uptime Guaranteed</div>
            </div>
            <div class="trust-sep"></div>
            <div class="trust-metric" data-aos>
                <div class="trust-metric-num">500+</div>
                <div class="trust-metric-label">Companies Served</div>
            </div>
            <div class="trust-sep"></div>
            <div class="trust-metric" data-aos>
                <div class="trust-metric-num">40%</div>
                <div class="trust-metric-label">Faster Operations</div>
            </div>
            <div class="trust-sep"></div>
            <div class="trust-metric" data-aos>
                <div class="trust-metric-num">Multi-Branch</div>
                <div class="trust-metric-label">Real-Time Sync</div>
            </div>
            <div class="trust-sep"></div>
            <div class="trust-metric" data-aos>
                <div class="trust-metric-num">SEC-Grade</div>
                <div class="trust-metric-label">Data Security</div>
            </div>
        </div>
    </div>
</section>

{{-- ====================================================================
     3. FEATURES GRID
     ==================================================================== --}}
<section class="section">
    <div class="container">
        <div class="section-center" data-aos>
            <span class="section-tag">Core Capabilities</span>
            <h2 class="section-title">Every Module Your Business Needs</h2>
            <p class="section-subtitle">
                Aurex ERP delivers a fully integrated suite — from finance to fulfillment —
                built on a single data layer that eliminates silos and manual handoffs.
            </p>
        </div>

        <div class="feature-grid" style="margin-top:4rem;">
            <div class="feature-card" data-aos>
                <div class="feature-icon"><i class="fas fa-chart-pie"></i></div>
                <h3>Finance & Accounting</h3>
                <p>Full double-entry ledger, multi-currency, tax compliance, automated reconciliation, and real-time P&L for your CFO.</p>
            </div>
            <div class="feature-card" data-aos>
                <div class="feature-icon cyan"><i class="fas fa-user-tie"></i></div>
                <h3>Sales & CRM</h3>
                <p>Manage the full sales pipeline — quotations, invoices, customer statements, and revenue forecasting in one view.</p>
            </div>
            <div class="feature-card" data-aos>
                <div class="feature-icon green"><i class="fas fa-boxes-stacked"></i></div>
                <h3>Inventory & Warehousing</h3>
                <p>Track millions of SKUs across warehouses. Batch tracking, low-stock alerts, FIFO/LIFO costing, and demand forecasting.</p>
            </div>
            <div class="feature-card" data-aos>
                <div class="feature-icon orange"><i class="fas fa-truck"></i></div>
                <h3>Purchases & Vendors</h3>
                <p>Streamline procurement with vendor management, purchase orders, GRN, 3-way matching, and payment scheduling.</p>
            </div>
            <div class="feature-card" data-aos>
                <div class="feature-icon pink"><i class="fas fa-users"></i></div>
                <h3>HR & Payroll</h3>
                <p>Complete employee lifecycle management — onboarding, attendance, leave, payroll processing, and payslips.</p>
            </div>
            <div class="feature-card" data-aos>
                <div class="feature-icon teal"><i class="fas fa-diagram-project"></i></div>
                <h3>Projects & Operations</h3>
                <p>Plan, assign, and track projects with Gantt views, resource allocation, time tracking, and cost control.</p>
            </div>
            <div class="feature-card" data-aos>
                <div class="feature-icon amber"><i class="fas fa-cash-register"></i></div>
                <h3>Point of Sale (POS)</h3>
                <p>Offline-first POS with instant inventory sync, barcode scanning, shift reports, and multi-branch support.</p>
            </div>
            <div class="feature-card" data-aos>
                <div class="feature-icon"><i class="fas fa-chart-bar"></i></div>
                <h3>Reports & Analytics</h3>
                <p>Executive dashboards, branch comparisons, custom report builder, and exportable financial summaries.</p>
            </div>
            <div class="feature-card" data-aos>
                <div class="feature-icon red"><i class="fas fa-shield-halved"></i></div>
                <h3>Role-Based Access Control</h3>
                <p>Granular permissions for every module. Control who can view, edit, approve, or export — by user, role, or branch.</p>
            </div>
            <div class="feature-card" data-aos>
                <div class="feature-icon cyan"><i class="fas fa-scroll"></i></div>
                <h3>Audit Logs</h3>
                <p>Immutable logs of every transaction, approval, and system change. Full compliance traceability across all modules.</p>
            </div>
        </div>
    </div>
</section>

{{-- ====================================================================
     4. PRODUCT SHOWCASE
     ==================================================================== --}}
<section class="section" style="background: rgba(255,255,255,0.01); border-top: 1px solid var(--border-soft); border-bottom: 1px solid var(--border-soft);">
    <div class="container">
        <div class="section-center" data-aos>
            <span class="section-tag">Product Showcase</span>
            <h2 class="section-title">See Aurex ERP in Action</h2>
            <p class="section-subtitle">
                Each module looks and feels like a premium product. Unified design. Unified data. One platform.
            </p>
        </div>

        {{-- Showcase 1 --}}
        <div class="showcase-grid" style="margin-top: 5rem;" data-aos>
            <div class="showcase-img">
                <img src="{{ asset('images/landing/dashboard_mockup.png') }}" alt="Executive Dashboard">
            </div>
            <div class="showcase-content">
                <span class="section-tag">Executive Dashboard</span>
                <h2>Your entire business at a glance</h2>
                <p>Real-time KPIs, branch performance, outstanding approvals, and financial summaries — all on a single executive command center.</p>
                <ul class="showcase-list">
                    <li>Live revenue and expense tracking</li>
                    <li>Multi-branch comparison panels</li>
                    <li>Approval queue with one-click actions</li>
                    <li>Exportable reports in PDF and Excel</li>
                </ul>
            </div>
        </div>

        {{-- Showcase 2 --}}
        <div class="showcase-grid reverse" style="margin-top: 5rem;" data-aos>
            <div class="showcase-content">
                <span class="section-tag">Finance & Accounting</span>
                <h2>Full financial control. Zero blind spots.</h2>
                <p>From journal entries to trial balance, tax filings, and customer statements — your accounting team gets a system built for precision.</p>
                <ul class="showcase-list">
                    <li>Automated double-entry bookkeeping</li>
                    <li>Multi-currency with live exchange rates</li>
                    <li>Tax reports and VAT compliance</li>
                    <li>Bank reconciliation in seconds</li>
                </ul>
            </div>
            <div class="showcase-img">
                <img src="{{ asset('images/landing/finance_mockup.png') }}" alt="Finance Module">
            </div>
        </div>

        {{-- Showcase 3 --}}
        <div class="showcase-grid" style="margin-top: 5rem;" data-aos>
            <div class="showcase-img">
                <img src="{{ asset('images/landing/inventory_mockup.png') }}" alt="Inventory Module">
            </div>
            <div class="showcase-content">
                <span class="section-tag">Inventory Management</span>
                <h2>Precision inventory across every warehouse</h2>
                <p>Track stock in real time across multiple locations. Know exactly what you have, where it is, and when to reorder.</p>
                <ul class="showcase-list">
                    <li>Multi-warehouse with transfer orders</li>
                    <li>Barcode and QR scanning</li>
                    <li>FIFO / LIFO / Average costing</li>
                    <li>Low-stock alerts and reorder automation</li>
                </ul>
            </div>
        </div>

        {{-- Showcase 4 --}}
        <div class="showcase-grid reverse" style="margin-top: 5rem;" data-aos>
            <div class="showcase-content">
                <span class="section-tag">Point of Sale</span>
                <h2>Retail-ready POS in any environment</h2>
                <p>Serve customers faster with a POS that works online and offline — and syncs automatically with inventory and accounting.</p>
                <ul class="showcase-list">
                    <li>Offline-first design for reliability</li>
                    <li>Touch-optimized UI for cashiers</li>
                    <li>Cash, card, and split payments</li>
                    <li>Shift reports and daily closing</li>
                </ul>
            </div>
            <div class="showcase-img">
                <img src="{{ asset('images/landing/pos_mockup.png') }}" alt="POS Module">
            </div>
        </div>
    </div>
</section>

{{-- ====================================================================
     5. BENEFITS
     ==================================================================== --}}
<section class="section">
    <div class="container">
        <div class="section-center" data-aos>
            <span class="section-tag">Business Outcomes</span>
            <h2 class="section-title">Built to Deliver Real Results</h2>
            <p class="section-subtitle">
                Companies using Aurex ERP report measurable improvements within the first 90 days.
            </p>
        </div>

        <div class="benefits-grid" style="margin-top: 4rem;">
            <div class="benefit-card" data-aos>
                <div class="benefit-icon"><i class="fas fa-clock"></i></div>
                <h4>Reduce Operational Delays</h4>
                <p>Automate approvals, reminders, and workflows. Eliminate manual handoffs that slow your team down.</p>
            </div>
            <div class="benefit-card" data-aos>
                <div class="benefit-icon"><i class="fas fa-database"></i></div>
                <h4>Centralize Business Data</h4>
                <p>Every transaction, document, and record in one place. No more switching between systems and spreadsheets.</p>
            </div>
            <div class="benefit-card" data-aos>
                <div class="benefit-icon"><i class="fas fa-eye"></i></div>
                <h4>Improve Financial Visibility</h4>
                <p>Real-time dashboards and financial statements give leadership instant clarity on performance.</p>
            </div>
            <div class="benefit-card" data-aos>
                <div class="benefit-icon"><i class="fas fa-warehouse"></i></div>
                <h4>Control Inventory in Real Time</h4>
                <p>Always know what stock you have, where it is, and what it costs — across every branch and warehouse.</p>
            </div>
            <div class="benefit-card" data-aos>
                <div class="benefit-icon"><i class="fas fa-check-double"></i></div>
                <h4>Stronger Approval Workflows</h4>
                <p>Multi-level approval chains with audit trails. Ensure every purchase, payment, and adjustment is authorized.</p>
            </div>
            <div class="benefit-card" data-aos>
                <div class="benefit-icon"><i class="fas fa-scale-balanced"></i></div>
                <h4>Compliance & Auditability</h4>
                <p>Immutable audit logs and role-based access make regulatory compliance and internal audits effortless.</p>
            </div>
        </div>
    </div>
</section>

{{-- ====================================================================
     6. WHY AUREX ERP
     ==================================================================== --}}
<section class="section" style="background: rgba(255,255,255,0.01); border-top: 1px solid var(--border-soft); border-bottom: 1px solid var(--border-soft);">
    <div class="container">
        <div class="section-center" data-aos>
            <span class="section-tag">Why Aurex</span>
            <h2 class="section-title">One Platform vs. Fragmented Tools</h2>
            <p class="section-subtitle">
                Stop paying for 10 disconnected tools. One unified platform beats a patchwork every time.
            </p>
        </div>

        <div class="compare-grid" style="margin-top: 4rem;" data-aos>
            <div class="compare-col bad">
                <h3 style="color: var(--danger);">❌ The Old Way</h3>
                <ul class="compare-list">
                    <li><span class="icon" style="color:var(--danger);">✗</span>Multiple disconnected accounting, CRM & HR tools</li>
                    <li><span class="icon" style="color:var(--danger);">✗</span>Manual reconciliation between systems</li>
                    <li><span class="icon" style="color:var(--danger);">✗</span>No real-time visibility into business performance</li>
                    <li><span class="icon" style="color:var(--danger);">✗</span>Spreadsheets as the "source of truth"</li>
                    <li><span class="icon" style="color:var(--danger);">✗</span>IT-dependent for every report and change</li>
                    <li><span class="icon" style="color:var(--danger);">✗</span>Zero audit trail on who changed what</li>
                    <li><span class="icon" style="color:var(--danger);">✗</span>Hard to scale across branches or locations</li>
                    <li><span class="icon" style="color:var(--danger);">✗</span>Outdated UX that slows your team down</li>
                </ul>
            </div>
            <div class="compare-col good">
                <h3 style="color: var(--brand-primary);">✓ The Aurex Way</h3>
                <ul class="compare-list">
                    <li><span class="icon" style="color:var(--brand-primary);">✓</span>One unified platform — finance, ops, HR, POS, inventory</li>
                    <li><span class="icon" style="color:var(--brand-primary);">✓</span>Automatic real-time sync across all modules</li>
                    <li><span class="icon" style="color:var(--brand-primary);">✓</span>Executive dashboards with live KPIs</li>
                    <li><span class="icon" style="color:var(--brand-primary);">✓</span>Single source of truth — always accurate</li>
                    <li><span class="icon" style="color:var(--brand-primary);">✓</span>Self-service reports for any team</li>
                    <li><span class="icon" style="color:var(--brand-primary);">✓</span>Immutable audit logs on every transaction</li>
                    <li><span class="icon" style="color:var(--brand-primary);">✓</span>Scales from 1 branch to enterprise globally</li>
                    <li><span class="icon" style="color:var(--brand-primary);">✓</span>Premium modern UX — your team will love it</li>
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- ====================================================================
     7. PRICING
     ==================================================================== --}}
<section class="section">
    <div class="container">
        <div class="section-center" data-aos>
            <span class="section-tag">Simple Pricing</span>
            <h2 class="section-title">Plans That Grow With You</h2>
            <p class="section-subtitle">
                No hidden fees. No per-module charges. Choose a plan and get everything Aurex has to offer.
            </p>
        </div>

        <div class="pricing-grid" style="margin-top: 4rem;" data-aos>
            {{-- Starter --}}
            <div class="pricing-card">
                <div class="pricing-plan">Starter</div>
                <div class="pricing-price"><sup>$</sup>49<span>/mo</span></div>
                <div class="pricing-desc">Perfect for small businesses and startups getting organized.</div>
                <div class="pricing-divider"></div>
                <ul class="pricing-features">
                    <li><i class="fas fa-check check"></i> Up to 5 users</li>
                    <li><i class="fas fa-check check"></i> Finance & Accounting</li>
                    <li><i class="fas fa-check check"></i> Inventory (1 warehouse)</li>
                    <li><i class="fas fa-check check"></i> Basic Sales module</li>
                    <li><i class="fas fa-check check"></i> Standard reports</li>
                    <li><i class="fas fa-check check"></i> Email support</li>
                    <li class="muted"><i class="fas fa-times cross"></i> HR & Payroll</li>
                    <li class="muted"><i class="fas fa-times cross"></i> Multi-branch</li>
                    <li class="muted"><i class="fas fa-times cross"></i> Custom workflows</li>
                </ul>
                <a href="{{ route('landing.demo') }}" class="btn btn-ghost" style="width:100%; justify-content: center;">Get Started</a>
            </div>

            {{-- Business (Featured) --}}
            <div class="pricing-card featured">
                <div class="pricing-badge">Most Popular</div>
                <div class="pricing-plan">Business</div>
                <div class="pricing-price"><sup>$</sup>149<span>/mo</span></div>
                <div class="pricing-desc">The full Aurex platform for growing businesses. Everything you need, nothing you don't.</div>
                <div class="pricing-divider"></div>
                <ul class="pricing-features">
                    <li><i class="fas fa-check check"></i> Up to 25 users</li>
                    <li><i class="fas fa-check check"></i> All Finance modules</li>
                    <li><i class="fas fa-check check"></i> Inventory (3 warehouses)</li>
                    <li><i class="fas fa-check check"></i> Full Sales & CRM</li>
                    <li><i class="fas fa-check check"></i> Purchases & Vendors</li>
                    <li><i class="fas fa-check check"></i> HR & Payroll</li>
                    <li><i class="fas fa-check check"></i> POS module</li>
                    <li><i class="fas fa-check check"></i> Advanced reports</li>
                    <li class="muted"><i class="fas fa-times cross"></i> Custom workflows</li>
                </ul>
                <a href="{{ route('landing.demo') }}" class="btn btn-primary" style="width:100%; justify-content: center;">Start Free Trial</a>
            </div>

            {{-- Enterprise --}}
            <div class="pricing-card">
                <div class="pricing-plan">Enterprise</div>
                <div class="pricing-price" style="font-size:2rem; letter-spacing:-1px;">Custom</div>
                <div class="pricing-desc">For large organizations needing unlimited scale, custom integrations, and dedicated support.</div>
                <div class="pricing-divider"></div>
                <ul class="pricing-features">
                    <li><i class="fas fa-check check"></i> Unlimited users</li>
                    <li><i class="fas fa-check check"></i> All Business modules</li>
                    <li><i class="fas fa-check check"></i> Unlimited warehouses</li>
                    <li><i class="fas fa-check check"></i> Multi-branch management</li>
                    <li><i class="fas fa-check check"></i> Custom workflows</li>
                    <li><i class="fas fa-check check"></i> API access</li>
                    <li><i class="fas fa-check check"></i> Dedicated account manager</li>
                    <li><i class="fas fa-check check"></i> SLA with 24/7 support</li>
                    <li><i class="fas fa-check check"></i> On-premise option</li>
                </ul>
                <a href="{{ route('landing.demo') }}" class="btn btn-ghost" style="width:100%; justify-content: center;">Contact Sales</a>
            </div>
        </div>
    </div>
</section>

{{-- ====================================================================
     8. CTA BLOCK
     ==================================================================== --}}
<section class="section-sm">
    <div class="container">
        <div class="cta-block" data-aos>
            <h2>Ready to Transform Your Business?</h2>
            <p>
                Join hundreds of businesses running on Aurex ERP. Book a personalized demo and see how Aurex can work for your specific workflows.
            </p>
            <div class="cta-actions">
                <a href="{{ route('landing.demo') }}" class="btn btn-accent btn-lg">
                    <i class="fas fa-calendar-check"></i> Book a Demo
                </a>
                <a href="{{ route('login') }}" class="btn btn-ghost btn-lg">
                    Go to Portal <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ====================================================================
     9. FAQ
     ==================================================================== --}}
<section class="section">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 4rem; align-items: start;">
            <div data-aos>
                <span class="section-tag">FAQ</span>
                <h2 class="section-title" style="font-size: 2.5rem;">Got Questions?</h2>
                <p class="section-subtitle" style="font-size: 1rem;">
                    Everything you need to know about Aurex ERP. Can't find an answer? <a href="{{ route('landing.demo') }}" style="color: var(--brand-primary);">Talk to our team.</a>
                </p>
            </div>

            <div class="faq-list" data-aos>
                <div class="faq-item open">
                    <button class="faq-question">
                        What modules are included in Aurex ERP?
                        <i class="fas fa-chevron-down faq-chevron"></i>
                    </button>
                    <div class="faq-answer">
                        Aurex ERP includes Finance & Accounting, Sales & CRM, Purchases & Vendors, Inventory & Warehousing, HR & Payroll, Projects & Operations, Point of Sale, Reports & Analytics, Role-Based Access Control, and Audit Logs. Enterprise plans also include custom workflow automation and API integrations.
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question">
                        Is Aurex ERP suitable for multi-branch businesses?
                        <i class="fas fa-chevron-down faq-chevron"></i>
                    </button>
                    <div class="faq-answer">
                        Yes. Aurex ERP is purpose-built for multi-branch and multi-entity organizations. You can manage separate branches, compare performance, control access per location, and view consolidated reports across all entities — all from a single account.
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question">
                        How long does it take to get set up?
                        <i class="fas fa-chevron-down faq-chevron"></i>
                    </button>
                    <div class="faq-answer">
                        Most businesses are fully operational within 2–4 weeks. Our onboarding team handles data migration, configuration, and team training. For simpler operations, you can be up and running in as little as a few days.
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question">
                        Can I migrate data from my existing software?
                        <i class="fas fa-chevron-down faq-chevron"></i>
                    </button>
                    <div class="faq-answer">
                        Absolutely. We support data migration from Excel, QuickBooks, Sage, SAP, and most other ERP and accounting systems. Our team works with you to validate and import your data cleanly into Aurex ERP.
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question">
                        What security measures protect my business data?
                        <i class="fas fa-chevron-down faq-chevron"></i>
                    </button>
                    <div class="faq-answer">
                        Aurex ERP uses AES-256 encryption at rest and in transit, role-based access control, immutable audit logs, two-factor authentication, and regular security audits. Enterprise clients can opt for dedicated hosting or on-premise deployment.
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question">
                        Does Aurex ERP work on mobile devices?
                        <i class="fas fa-chevron-down faq-chevron"></i>
                    </button>
                    <div class="faq-answer">
                        Yes. Aurex ERP is fully responsive and works on any modern browser across mobile, tablet, and desktop. Key views — including dashboards, approvals, and inventory lookups — are optimized for on-the-go access.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
