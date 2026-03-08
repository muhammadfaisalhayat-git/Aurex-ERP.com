@extends('layouts.app')

@section('title', __('messages.sm_document_archive'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">{{ __('messages.sm_document_archive') }}</h1>
                <p class="text-muted">{{ __('messages.sm_doc_retention_desc') }}</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('acp.system.document-archive.index') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">{{ __('messages.document_type') }}</label>
                        <select name="type" class="form-select">
                            <option value="">{{ __('messages.all_types') }}</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-bold">{{ __('messages.search_by_number') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i
                                    class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0"
                                placeholder="e.g. INV-2023-001" value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100 shadow-sm">
                            <i class="fas fa-filter me-1"></i> {{ __('messages.apply_filters') }}
                        </button>
                        @if(request()->anyFilled(['type', 'search']))
                            <a href="{{ route('acp.system.document-archive.index') }}"
                                class="btn btn-link text-muted ms-2 px-0"><i class="fas fa-times"></i></a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">{{ __('messages.document') }}</th>
                                <th>{{ __('messages.original_number') }}</th>
                                <th>{{ __('messages.archived_by') }}</th>
                                <th>{{ __('messages.timestamp') }}</th>
                                <th class="pe-4 text-end">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($documents as $doc)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light p-2 rounded me-3">
                                                <i class="fas fa-file-pdf text-danger fa-lg"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $doc->document_type }}</div>
                                                <div class="small text-muted">Ref ID: #{{ $doc->document_id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><code>{{ $doc->original_number }}</code></td>
                                    <td>{{ $doc->archiver->name ?? '-' }}</td>
                                    <td>{{ $doc->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="pe-4 text-end">
                                        <div class="btn-group">
                                            <a href="{{ route('acp.system.document-archive.show', $doc) }}"
                                                class="btn btn-sm btn-light border" title="{{ __('messages.view') }}">
                                                <i class="fas fa-eye text-primary"></i>
                                            </a>
                                            <a href="{{ route('acp.system.document-archive.download', $doc) }}"
                                                class="btn btn-sm btn-light border border-start-0"
                                                title="{{ __('messages.download') }}">
                                                <i class="fas fa-download text-success"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fas fa-archive fa-2x mb-2"></i>
                                        <p class="mb-0">{{ __('messages.no_archived_documents_found') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($documents->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $documents->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection