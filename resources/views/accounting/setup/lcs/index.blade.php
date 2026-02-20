@extends('layouts.app')

@section('title', __('messages.lcs') . ' - ' . __('messages.setup'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ __('messages.lcs') }}</h1>
        <div class="page-actions">
            @can('create lcs')
                <a href="{{ route('accounting.gl.setup.lcs.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> {{ __('messages.add_lc') }}
                </a>
            @endcan
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th width="100">{{ __('messages.code') }}</th>
                            <th>{{ __('messages.name_en') }}</th>
                            <th>{{ __('messages.name_ar') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th width="150">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td><span class="badge bg-secondary">{{ $item->code }}</span></td>
                                <td><div class="fw-bold">{{ $item->name_en }}</div></td>
                                <td><div class="fw-bold">{{ $item->name_ar }}</div></td>
                                <td>
                                    @if($item->is_active)
                                        <span class="badge bg-success">{{ __('messages.active') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('messages.inactive') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        @can('edit lcs')
                                            <a href="{{ route('accounting.gl.setup.lcs.edit', $item) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan
                                        @can('delete lcs')
                                            <form action="{{ route('accounting.gl.setup.lcs.destroy', $item) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('{{ __('messages.are_you_sure') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">{{ __('messages.no_lcs_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($items->hasPages())
            <div class="card-footer">
                {{ $items->links() }}
            </div>
        @endif
    </div>
@endsection
