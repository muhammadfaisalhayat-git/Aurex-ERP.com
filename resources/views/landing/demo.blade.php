@extends('layouts.landing')

@section('title', 'Request Demo - Witness the Future of ERP')

@section('content')
<section style="padding-top: 180px; min-height: 100vh; position: relative;">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 6rem; align-items: center;">
            <div data-aos>
                <span class="section-tag">Direct Access</span>
                <h1 style="font-size: 4.5rem; line-height: 1.1; font-weight: 800; letter-spacing: -2px; margin-bottom: 2rem; background: linear-gradient(to bottom right, #FFFFFF 50%, #94A3B8); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    Witness the <br>Next Generation
                </h1>
                <p style="color: var(--text-muted); font-size: 1.3rem; margin-bottom: 3rem;">
                    Step into the future of business management. Our sales engineering team will show you exactly how Aurex scales with your complexity.
                </p>
                
                <div style="display: flex; flex-direction: column; gap: 2rem;">
                    <div style="display: flex; gap: 1.5rem; align-items: flex-start;">
                        <div style="width: 60px; height: 60px; background: rgba(99, 102, 241, 0.1); color: var(--accent); display: flex; align-items: center; justify-content: center; border-radius: 16px; border: 1px solid var(--glass-border); flex-shrink: 0;"><i class="fas fa-eye" style="font-size: 1.5rem;"></i></div>
                        <div>
                            <h4 style="font-size: 1.2rem; margin-bottom: 0.5rem;">Deep-Dive Consultation</h4>
                            <p style="color: var(--text-muted);">A personalized 45-minute tour of the modules that matter most to your specific industry.</p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 1.5rem; align-items: flex-start;">
                        <div style="width: 60px; height: 60px; background: rgba(0, 184, 148, 0.1); color: var(--secondary); display: flex; align-items: center; justify-content: center; border-radius: 16px; border: 1px solid var(--glass-border); flex-shrink: 0;"><i class="fas fa-microchip" style="font-size: 1.5rem;"></i></div>
                        <div>
                            <h4 style="font-size: 1.2rem; margin-bottom: 0.5rem;">Architectural Review</h4>
                            <p style="color: var(--text-muted);">Understand our API ecosystem, security protocols, and data migration strategies.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bento-item" style="height: auto; padding: 4rem; justify-content: flex-start;" data-aos style="transition-delay: 0.2s">
                <h3 style="font-size: 1.8rem; margin-bottom: 2.5rem;">Secure Your Slot</h3>
                
                @if(session('success'))
                    <div style="background: rgba(0, 184, 148, 0.1); color: var(--secondary); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid rgba(0, 184, 148, 0.3);">
                        <i class="fas fa-check-circle" style="margin-right: 10px;"></i> {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('landing.demo.submit') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.5rem;">
                    @csrf
                    <div>
                        <label style="display: block; margin-bottom: 10px; font-weight: 500; font-size: 0.9rem; color: var(--text-muted);">Full Name</label>
                        <input type="text" name="name" required style="width: 100%; padding: 14px; background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); border-radius: 10px; color: white; outline: none; transition: 0.3s;" placeholder="Alex Sterling">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div>
                            <label style="display: block; margin-bottom: 10px; font-weight: 500; font-size: 0.9rem; color: var(--text-muted);">Work Email</label>
                            <input type="email" name="email" required style="width: 100%; padding: 14px; background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); border-radius: 10px; color: white; outline: none; transition: 0.3s;" placeholder="alex@company.com">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 10px; font-weight: 500; font-size: 0.9rem; color: var(--text-muted);">Company</label>
                            <input type="text" name="company" required style="width: 100%; padding: 14px; background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); border-radius: 10px; color: white; outline: none; transition: 0.3s;" placeholder="Starlight Corp">
                        </div>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 10px; font-weight: 500; font-size: 0.9rem; color: var(--text-muted);">Primary Interest</label>
                        <select name="industry" required style="width: 100%; padding: 14px; background: rgba(2, 6, 23, 0.5); border: 1px solid var(--glass-border); border-radius: 10px; color: white; outline: none; appearance: none;">
                            <option value="Financials">Financial Operations</option>
                            <option value="SCM">Supply Chain & Inventory</option>
                            <option value="Retail">Retail & Omnichannel</option>
                            <option value="Enterprise">Full Suite Transformation</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-accent" style="padding: 1rem; font-size: 1.1rem; margin-top: 1rem;">Initialize Sync</button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
