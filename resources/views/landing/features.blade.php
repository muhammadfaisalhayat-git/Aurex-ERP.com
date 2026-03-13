@extends('layouts.landing')

@section('title', 'Platform Modules - Aurex ERP Deep-Dive')

@section('content')
<section style="padding-top: 180px; position: relative;">
    <div class="container">
        <div style="text-align: center; max-width: 900px; margin: 0 auto 6rem;" data-aos>
            <span class="section-tag">Explore the Ecosystem</span>
            <h1 style="font-size: 4rem; line-height: 1.1; font-weight: 800; letter-spacing: -2px; margin-bottom: 2rem; background: linear-gradient(to bottom right, #FFFFFF 50%, #94A3B8); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                Every Tool Your Enterprise <br>Needs to Excel
            </h1>
            <p style="color: var(--text-muted); font-size: 1.3rem;">From global finance to micro-inventory, Aurex provides the specialized modules required to run a complex modern business.</p>
        </div>

        <div style="display: grid; grid-template-columns: 1fr; gap: 4rem;">
            
            <!-- Accounting Module -->
            <div class="bento-item" style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; height: auto; padding: 4rem; align-items: center;" data-aos>
                <div>
                    <div class="bento-icon"><i class="fas fa-vault"></i></div>
                    <h2 style="font-size: 2.5rem; margin-bottom: 1.5rem;">Advanced Financial Management</h2>
                    <p style="font-size: 1.1rem; margin-bottom: 2rem;">Aurex Finance isn't just a ledger; it's a strategic weapon. Handle multi-company consolidations, real-time tax mapping, and automated financial reporting with ease.</p>
                    <ul style="list-style: none; color: var(--text-muted); display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <li><i class="fas fa-check" style="color: var(--accent); margin-right: 10px;"></i> Multi-Currency Support</li>
                        <li><i class="fas fa-check" style="color: var(--accent); margin-right: 10px;"></i> AI Bank Reconciliation</li>
                        <li><i class="fas fa-check" style="color: var(--accent); margin-right: 10px;"></i> Budgeting & Forecasting</li>
                        <li><i class="fas fa-check" style="color: var(--accent); margin-right: 10px;"></i> Dynamic Financial Statements</li>
                    </ul>
                </div>
                <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); border-radius: 20px; padding: 2rem; box-shadow: var(--shadow-premium);">
                     <!-- Finance Mockup Representation -->
                     <img src="{{ asset('images/landing/finance_mockup.png') }}" alt="Aurex Finance UI" style="width: 100%; border-radius: 12px; opacity: 0.8;">
                </div>
            </div>

            <!-- Inventory Module -->
            <div class="bento-item" style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; height: auto; padding: 4rem; align-items: center;" data-aos>
                <div style="order: 2;">
                    <div class="bento-icon" style="color: var(--secondary); filter: drop-shadow(0 0 10px var(--secondary));"><i class="fas fa-cubes-stacked"></i></div>
                    <h2 style="font-size: 2.5rem; margin-bottom: 1.5rem;">Intelligent Inventory Control</h2>
                    <p style="font-size: 1.1rem; margin-bottom: 2rem;">Manage complex supply chains without the headache. Track products across unlimited locations, manage variants, and use barcode integration for rapid operations.</p>
                    <ul style="list-style: none; color: var(--text-muted); display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <li><i class="fas fa-check" style="color: var(--secondary); margin-right: 10px;"></i> Serial & Batch Tracking</li>
                        <li><i class="fas fa-check" style="color: var(--secondary); margin-right: 10px;"></i> Automated Re-ordering</li>
                        <li><i class="fas fa-check" style="color: var(--secondary); margin-right: 10px;"></i> Warehouse Mapping</li>
                        <li><i class="fas fa-check" style="color: var(--secondary); margin-right: 10px;"></i> FIFO/WAC Valuation</li>
                    </ul>
                </div>
                <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); border-radius: 20px; padding: 2rem; box-shadow: var(--shadow-premium); order: 1;">
                     <!-- Inventory Mockup Representation -->
                     <img src="{{ asset('images/landing/inventory_mockup.png') }}" alt="Aurex Inventory UI" style="width: 100%; border-radius: 12px; opacity: 0.8;">
                </div>
            </div>

            <!-- More Modules in a smaller grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <!-- Sales -->
                <div class="bento-item" style="height: auto; padding: 3rem;" data-aos>
                    <div class="bento-icon" style="color: #60A5FA; filter: drop-shadow(0 0 10px #60A5FA);"><i class="fas fa-shopping-bag"></i></div>
                    <h3>Unified Sales & POS</h3>
                    <p>Drive revenue with an integrated sales engine. Manage quotations, sales orders, and retail POS in one ecosystem. Modern, fast, and simple.</p>
                </div>
                <!-- Procurement -->
                <div class="bento-item" style="height: auto; padding: 3rem;" data-aos style="transition-delay: 0.1s">
                    <div class="bento-icon" style="color: #F87171; filter: drop-shadow(0 0 10px #F87171);"><i class="fas fa-truck-ramp-box"></i></div>
                    <h3>Strategic Procurement</h3>
                    <p>Optimize your purchase cycle. Centralize vendor management, automate purchase requisitions, and maintain strict spend control.</p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Detailed Modules Table Section -->
<section style="padding: 120px 0;">
    <div class="container">
        <div style="background: var(--bg-card); border: 1px solid var(--glass-border); border-radius: 32px; padding: 4rem;" data-aos>
            <h2 style="margin-bottom: 3rem; text-align: center;">Operational Deep-Dive</h2>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; color: var(--text-muted); font-size: 1.1rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--glass-border);">
                            <th style="padding: 1.5rem; text-align: left; color: white;">Feature Category</th>
                            <th style="padding: 1.5rem; text-align: left; color: white;">Key Capabilities</th>
                            <th style="padding: 1.5rem; text-align: left; color: white;">Impact</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border-bottom: 1px solid var(--glass-border);">
                            <td style="padding: 2rem; color: white; font-weight: 700;">Financial Operations</td>
                            <td style="padding: 2rem;">Journal Entries, Ledger Management, Automated Tax Filing, VAT Integration.</td>
                            <td style="padding: 2rem; color: var(--secondary);">99% Accuracy</td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--glass-border);">
                            <td style="padding: 2rem; color: white; font-weight: 700;">Supply Chain</td>
                            <td style="padding: 2rem;">Landed Cost, Multi-location Sync, Supplier Portals, Quality Control.</td>
                            <td style="padding: 2rem; color: var(--secondary);">-20% Stockouts</td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--glass-border);">
                            <td style="padding: 2rem; color: white; font-weight: 700;">Retail & POS</td>
                            <td style="padding: 2rem;">Offline Transactions, Loyalty Programs, Thermal Print Support, Session Management.</td>
                            <td style="padding: 2rem; color: var(--secondary);">Instant Sync</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
