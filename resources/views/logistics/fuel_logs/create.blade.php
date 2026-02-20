@extends('layouts.app')

@section('title', __('messages.record_fuel') . ' - ' . __('messages.logistics'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('messages.record_fuel_expense') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('messages.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('logistics.fuel-logs.index') }}">{{ __('messages.fuel_logs') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('messages.record') }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="card border-0 shadow-sm border-start border-4 border-primary">
            <div class="card-body p-4">
                <div class="alert alert-info border-0 bg-info-subtle mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ __('messages.fuel_log_ledger_notice') }}
                </div>

                <form action="{{ route('logistics.fuel-logs.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label">{{ __('messages.select_vehicle') }} <span class="text-danger">*</span></label>
                            <select name="delivery_vehicle_id" class="form-select @error('delivery_vehicle_id') is-invalid @enderror" required>
                                <option value="">{{ __('messages.select_vehicle_placeholder') ?? __('messages.select_vehicle') }}...</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}">
                                        {{ $vehicle->plate_number }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.entry_date') }} <span class="text-danger">*</span></label>
                            <input type="date" name="entry_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('messages.odometer_reading') }} <span class="text-danger">*</span></label>
                            <input type="number" name="odometer_reading" class="form-control" placeholder="125430" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.liters') }} <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="liters" id="liters" class="form-control" placeholder="45.50" required onchange="calculateTotal()">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.cost_per_liter') }} <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="cost_per_liter" id="cost_per_liter" class="form-control" placeholder="2.45" required onchange="calculateTotal()">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.total_cost') }}</label>
                            <input type="text" id="total_cost_display" class="form-control bg-light" value="0.00" readonly>
                        </div>

                        <div class="col-12">
                            <label class="form-label">{{ __('messages.fuel_station') }}</label>
                            <input type="text" name="fuel_station" class="form-control" placeholder="ADNOC Service Station #XX">
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-between">
                        <a href="{{ route('logistics.fuel-logs.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>{{ __('messages.discard') }}
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-check-circle me-2"></i>{{ __('messages.record_post_expense') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function calculateTotal() {
    const liters = document.getElementById('liters').value || 0;
    const cost = document.getElementById('cost_per_liter').value || 0;
    const total = parseFloat(liters) * parseFloat(cost);
    document.getElementById('total_cost_display').value = total.toFixed(2);
}
</script>
@endsection
