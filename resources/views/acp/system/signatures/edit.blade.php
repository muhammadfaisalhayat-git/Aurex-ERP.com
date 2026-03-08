@extends('layouts.app')

@section('title', __('messages.sm_edit_signature'))

@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a
                            href="{{ route('acp.user-mgmt.signatures.index') }}">{{ __('messages.sm_signatures_management') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ __('messages.edit') }}</li>
                </ol>
            </nav>
            <h1 class="h3">{{ __('messages.sm_edit_e_signature') }}: {{ $signature->title }}</h1>
        </div>

        <form action="{{ route('acp.user-mgmt.signatures.update', $signature) }}" method="POST" id="signatureForm">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold">{{ __('messages.signature_details') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('messages.assign_to_user') }} <span
                                            class="text-danger">*</span></label>
                                    <select name="user_id" class="form-select select2" required>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ (old('user_id') ?? $signature->user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('messages.signature_title') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control"
                                        value="{{ old('title', $signature->title) }}" required>
                                </div>
                                <div class="col-12">
                                    <label
                                        class="form-label fw-bold badge bg-primary mb-2">{{ __('messages.update_signature_below') }}</label>
                                    <div class="signature-canvas-container border bg-white rounded position-relative"
                                        style="height: 300px;">
                                        <canvas id="signature-pad" class="w-100 h-100 cursor-crosshair"></canvas>
                                        <div class="position-absolute bottom-0 end-0 p-3">
                                            <button type="button" id="clear-pad"
                                                class="btn btn-sm btn-outline-secondary me-2">
                                                <i class="fas fa-eraser"></i> {{ __('messages.clear') }}
                                            </button>
                                            <button type="button" id="undo-pad" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-undo"></i> {{ __('messages.undo') }}
                                            </button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="signature_data" id="signature_data"
                                        value="{{ $signature->signature_data }}">
                                    <div class="form-text small">{{ __('messages.leave_untouched_to_keep_current') }}</div>
                                    <div class="mt-3 p-3 bg-light rounded text-center border">
                                        <div class="small text-muted mb-2">{{ __('messages.current_stored_signature') }}:
                                        </div>
                                        <img src="{{ $signature->signature_data }}" alt="Current"
                                            style="height: 80px; mix-blend-mode: multiply;">
                                    </div>
                                </div>
                                <div class="col-12 border-top pt-3 mt-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_default" value="1"
                                            id="is_default" {{ (old('is_default') ?? $signature->is_default) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold"
                                            for="is_default">{{ __('messages.set_as_primary_signature') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 sticky-top" style="top: 100px;">
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary w-100 mb-2 py-2 fw-bold">
                                <i class="fas fa-save me-1"></i> {{ __('messages.update_signature') }}
                            </button>
                            <a href="{{ route('acp.user-mgmt.signatures.index') }}" class="btn btn-outline-secondary w-100">
                                {{ __('messages.cancel') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
        <script>
            document.addEventListener('turbo:load', function () {
                const canvas = document.getElementById('signature-pad');
                if (!canvas) return;

                const signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgba(255, 255, 255, 0)',
                    penColor: 'rgb(0, 0, 0)'
                });

                let initialDataLoaded = false;

                function resizeCanvas() {
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);
                    const width = canvas.offsetWidth;
                    const height = canvas.offsetHeight;

                    if (canvas.width !== width * ratio || canvas.height !== height * ratio) {
                        const data = signaturePad.toData();
                        canvas.width = width * ratio;
                        canvas.height = height * ratio;
                        canvas.getContext("2d").scale(ratio, ratio);

                        if (!initialDataLoaded) {
                            const initialData = document.getElementById('signature_data').value;
                            if (initialData) {
                                signaturePad.fromDataURL(initialData);
                            }
                            initialDataLoaded = true;
                        } else {
                            signaturePad.fromData(data);
                        }
                    }
                }

                window.addEventListener("resize", resizeCanvas);
                resizeCanvas();

                document.getElementById('clear-pad').addEventListener('click', function () {
                    signaturePad.clear();
                });

                document.getElementById('undo-pad').addEventListener('click', function () {
                    const data = signaturePad.toData();
                    if (data && data.length > 0) {
                        data.pop(); // remove last stroke
                        signaturePad.fromData(data);
                    }
                });

                document.getElementById('signatureForm').addEventListener('submit', function (e) {
                    if (!signaturePad.isEmpty()) {
                        document.getElementById('signature_data').value = signaturePad.toDataURL();
                    }
                });
            });
        </script>
    @endpush
@endsection