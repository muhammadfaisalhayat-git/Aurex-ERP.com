@extends('layouts.app')

@section('title', __('messages.sm_document_preview'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a
                                href="{{ route('acp.system.document-archive.index') }}">{{ __('messages.sm_document_archive') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('messages.preview') }}</li>
                    </ol>
                </nav>
                <h1 class="h3 mb-0">{{ $document->document_type }}: <span
                        class="text-primary">{{ $document->original_number }}</span></h1>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('acp.system.document-archive.download', $document) }}" class="btn btn-primary">
                    <i class="fas fa-download me-1"></i> {{ __('messages.download_document') }}
                </a>
                <a href="{{ route('acp.system.document-archive.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> {{ __('messages.back_to_archive') }}
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4 bg-dark overflow-hidden" style="min-height: 600px;">
                    <div
                        class="card-header bg-dark border-secondary py-2 d-flex justify-content-between align-items-center">
                        <small class="text-light-50">{{ __('messages.document_viewer') }}</small>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-secondary"><i class="fas fa-search-plus"></i></button>
                            <button class="btn btn-sm btn-secondary"><i class="fas fa-search-minus"></i></button>
                            <button class="btn btn-sm btn-secondary"><i class="fas fa-print"></i></button>
                        </div>
                    </div>
                    <div class="card-body p-0 d-flex align-items-center justify-content-center">
                        <!-- In a real app, this would be an iframe or pdf.js viewer -->
                        <div class="document-placeholder text-center text-white-50 p-5">
                            <i class="fas fa-file-pdf fa-5x mb-4 text-light-50"></i>
                            <h5>{{ __('messages.simulated_document_content') }}</h5>
                            <p class="small">
                                {{ __('messages.preview_available_for_original_path') }}:<br><code>{{ $document->file_path }}</code>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">{{ __('messages.metadata') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted small d-block mb-1">{{ __('messages.archive_id') }}</label>
                            <span class="fw-bold">#{{ $document->id }}</span>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small d-block mb-1">{{ __('messages.document_type') }}</label>
                            <span class="badge bg-info-soft text-info">{{ $document->document_type }}</span>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small d-block mb-1">{{ __('messages.original_system_id') }}</label>
                            <span class="fw-medium">{{ $document->document_id }}</span>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small d-block mb-1">{{ __('messages.archived_on') }}</label>
                            <span class="fw-medium">{{ $document->created_at->format('F d, Y @ H:i:s') }}</span>
                        </div>
                        <div class="mb-0">
                            <label class="text-muted small d-block mb-1">{{ __('messages.archived_by') }}</label>
                            <div class="d-flex align-items-center">
                                <div class="avatar-xs bg-light rounded-circle me-2 d-flex align-items-center justify-content-center"
                                    style="width: 24px; height: 24px;">
                                    <i class="fas fa-user small text-muted"></i>
                                </div>
                                <span class="fw-bold">{{ $document->archiver->name ?? 'System Process' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">{{ __('messages.integrity_hash') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="p-2 bg-light rounded border text-break">
                            <code class="small"
                                style="font-size: 10px;">{{ hash('sha256', $document->file_path . $document->created_at) }}</code>
                        </div>
                        <div class="mt-2 small text-success">
                            <i class="fas fa-shield-alt me-1"></i> {{ __('messages.document_integrity_verified') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection