@extends('layouts.app')

@section('title', __('messages.sm_signatures_management'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">{{ __('messages.sm_signatures_management') }}</h1>
                <p class="text-muted">{{ __('messages.sm_e_signatures_desc') }}</p>
            </div>
            <a href="{{ route('acp.user-mgmt.signatures.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> {{ __('messages.add_new_signature') }}
            </a>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
            @forelse($signatures as $sig)
                <div class="col">
                    <div class="card h-100 shadow-sm border-0 signature-card border-top border-primary border-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="fw-bold mb-1">{{ $sig->title }}</h5>
                                    <div class="small text-muted">{{ $sig->user->name }}</div>
                                </div>
                                @if($sig->is_default)
                                    <span class="badge bg-success-soft text-success rounded-pill"
                                        style="background: rgba(46, 204, 113, 0.1);">
                                        <i class="fas fa-check-circle me-1"></i> {{ __('messages.default') }}
                                    </span>
                                @endif
                            </div>

                            <div class="bg-light p-3 rounded mb-3 text-center border overflow-hidden"
                                style="height: 120px; display: flex; align-items: center; justify-content: center;">
                                <img src="{{ $sig->signature_data }}" alt="Signature" class="mw-100 mh-100"
                                    style="mix-blend-mode: multiply;">
                            </div>

                            <div class="d-flex justify-content-between align-items-center small text-muted mb-3">
                                <span><i class="fas fa-calendar-alt me-1"></i> {{ $sig->created_at->format('Y-m-d') }}</span>
                                <span>ID: #{{ $sig->id }}</span>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="{{ route('acp.user-mgmt.signatures.edit', $sig) }}"
                                    class="btn btn-sm btn-outline-primary flex-grow-1">
                                    <i class="fas fa-edit me-1"></i> {{ __('messages.edit') }}
                                </a>
                                <form action="{{ route('acp.user-mgmt.signatures.destroy', $sig) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('{{ __('messages.confirm_delete_signature') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="fas fa-pen-nib fa-3x text-light mb-3"></i>
                    <h5 class="text-muted">{{ __('messages.no_signatures_found') }}</h5>
                </div>
            @endforelse
        </div>

        @if($signatures->hasPages())
            <div class="mt-4">
                {{ $signatures->links() }}
            </div>
        @endif
    </div>
@endsection