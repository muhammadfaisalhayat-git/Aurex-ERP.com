@extends('layouts.landing')

@section('title', 'Documentation - Architectural Blueprint')

@section('content')
<section style="padding-top: 180px; min-height: 100vh; position: relative;">
    <div class="container">
        <div style="display: grid; grid-template-columns: 320px 1fr; gap: 5rem;">
            <!-- Sidebar -->
            <div style="position: sticky; top: 120px; height: fit-content;" data-aos>
                <div style="margin-bottom: 3rem;">
                    <h4 style="margin-bottom: 1.5rem; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 2px; color: var(--accent);">Blueprint</h4>
                    <ul style="padding: 0; list-style: none; display: flex; flex-direction: column; gap: 1rem;">
                        <li><a href="#" style="color: white; font-weight: 600; text-decoration: none;">Introduction</a></li>
                        <li><a href="#" style="color: var(--text-muted); text-decoration: none; transition: 0.3s;">Core Architecture</a></li>
                        <li><a href="#" style="color: var(--text-muted); text-decoration: none; transition: 0.3s;">Database Schema</a></li>
                    </ul>
                </div>
                <div style="margin-bottom: 3rem;">
                    <h4 style="margin-bottom: 1.5rem; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 2px; color: var(--accent);">Operational Flow</h4>
                    <ul style="padding: 0; list-style: none; display: flex; flex-direction: column; gap: 1rem;">
                        <li><a href="#" style="color: var(--text-muted); text-decoration: none; transition: 0.3s;">Finance Workflows</a></li>
                        <li><a href="#" style="color: var(--text-muted); text-decoration: none; transition: 0.3s;">Inventory Logic</a></li>
                        <li><a href="#" style="color: var(--text-muted); text-decoration: none; transition: 0.3s;">POS Configuration</a></li>
                    </ul>
                </div>
            </div>

            <!-- Content Area -->
            <div class="bento-item" style="height: auto; padding: 5rem; justify-content: flex-start;" data-aos>
                <h1 style="font-size: 3rem; margin-bottom: 2rem; font-weight: 800;">The Foundation of Enterprise</h1>
                <p style="color: var(--text-muted); font-size: 1.2rem; margin-bottom: 4rem;">
                    Aurex ERP is engineered for high-availability, consistent performance, and absolute data integrity. This documentation serves as the master blueprint for your implementation.
                </p>
                
                <div style="border-bottom: 1px solid var(--glass-border); padding-bottom: 4rem; margin-bottom: 4rem;">
                    <h3 style="font-size: 1.5rem; margin-bottom: 1.5rem; color: white;">Architectural Vision</h3>
                    <p style="color: var(--text-muted); margin-bottom: 2rem;">
                        Our system follows a modular monolith architecture, ensuring high internal cohesion while allowing independent scaling of mission-critical services like POS and Reporting engines.
                    </p>
                    <div style="background: rgba(99, 102, 241, 0.05); border-left: 4px solid var(--accent); padding: 2rem; border-radius: 8px;">
                        <p style="font-style: italic; color: var(--text-main);">
                            "Efficiency is not about doing more; it's about eliminating the friction of doing." - Aurex Engineering Thesis
                        </p>
                    </div>
                </div>

                <div>
                    <h3 style="font-size: 1.5rem; margin-bottom: 1.5rem; color: white;">System Stack</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                        <div style="padding: 1.5rem; background: rgba(255,255,255,0.02); border: 1px solid var(--glass-border); border-radius: 12px;">
                            <h5 style="margin-bottom: 10px; color: var(--accent);">Core Engine</h5>
                            <p style="font-size: 0.9rem; color: var(--text-muted);">Laravel 10 Framework with strict typing and robust security middleware.</p>
                        </div>
                        <div style="padding: 1.5rem; background: rgba(255,255,255,0.02); border: 1px solid var(--glass-border); border-radius: 12px;">
                            <h5 style="margin-bottom: 10px; color: var(--accent);">Data Layer</h5>
                            <p style="font-size: 0.9rem; color: var(--text-muted);">Relational RDBMS with custom indexing for million-row queries.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
