@extends('layouts.app')

@section('title', __('messages.sm_favorite_screens'))

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">{{ __('messages.sm_favorite_screens') }}</h1>
                <p class="text-muted">{{ __('messages.sm_pins_desc') }}</p>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFavoriteModal">
                <i class="fas fa-plus me-1"></i> {{ __('messages.add_favorite') }}
            </button>
        </div>

        <!-- Favorite Grid -->
        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-3 mb-5" id="sortable-favorites">
            @forelse($favorites as $fav)
                <div class="col" data-id="{{ $fav->id }}">
                    <div class="card h-100 shadow-sm border-0 favorite-card transition-base hover-shadow">
                        <div class="card-body d-flex align-items-center p-3">
                            <div class="bg-primary-soft p-3 rounded me-3" style="background: rgba(var(--bs-primary-rgb), 0.1);">
                                <i class="{{ $fav->icon }} text-primary fa-lg"></i>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <h6 class="mb-0 fw-bold text-truncate">{{ $fav->label }}</h6>
                                <div class="small text-muted text-truncate">{{ $fav->route_name }}</div>
                            </div>
                            <div class="ms-2 d-flex flex-column gap-1">
                                <form action="{{ route('acp.system.favorite-screens.destroy', $fav) }}" method="POST"
                                    onsubmit="return confirm('{{ __('messages.confirm_remove_favorite') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0"><i
                                            class="fas fa-times"></i></button>
                                </form>
                                <span class="btn btn-sm btn-link text-muted p-0 cursor-move drag-handle"><i
                                        class="fas fa-grip-vertical"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 bg-white rounded shadow-sm">
                    <i class="fas fa-star fa-3x text-light mb-3"></i>
                    <h5 class="text-muted">{{ __('messages.no_favorites_yet') }}</h5>
                    <p class="text-muted small">{{ __('messages.no_favorites_desc') }}</p>
                </div>
            @endforelse
        </div>

        <!-- Available Screens (Quick Add) -->
        <h5 class="fw-bold mb-4 border-bottom pb-2">{{ __('messages.quick_add_available_screens') }}</h5>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-3">
            @foreach($availableScreens as $screen)
                @php $isFav = $favorites->contains('route_name', $screen['route']); @endphp
                <div class="col">
                    <div class="card h-100 border transition-base {{ $isFav ? 'bg-light opacity-75' : 'hover-bg-light cursor-pointer quick-add-btn' }}"
                        @if(!$isFav) data-route="{{ $screen['route'] }}" data-label="{{ $screen['label'] }}"
                        data-icon="{{ $screen['icon'] }}" @endif>
                        <div class="card-body p-3 d-flex align-items-center">
                            <i class="{{ $screen['icon'] }} me-3 text-muted"></i>
                            <span class="fw-medium small">{{ $screen['label'] }}</span>
                            @if($isFav)
                                <i class="fas fa-check-circle ms-auto text-success"></i>
                            @else
                                <i class="fas fa-plus ms-auto text-primary-soft opacity-0 hover-opacity-100"></i>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Hidden Form for Quick Add -->
        <form id="quickAddForm" action="{{ route('acp.system.favorite-screens.store') }}" method="POST"
            class="d-none">
            @csrf
            <input type="hidden" name="route_name" id="qa_route">
            <input type="hidden" name="label" id="qa_label">
            <input type="hidden" name="icon" id="qa_icon">
        </form>

        <!-- Modal for Manual Add -->
        <div class="modal fade" id="addFavoriteModal" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ route('acp.system.favorite-screens.store') }}" method="POST"
                    class="modal-content border-0 shadow">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">{{ __('messages.add_to_favorites') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('messages.screen_label') }}</label>
                            <input type="text" name="label" class="form-control" placeholder="e.g. My Recent Sales"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('messages.route_name') }}</label>
                            <input type="text" name="route_name" class="form-control"
                                placeholder="e.g. sales.invoices.index" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold">{{ __('messages.icon_class') }}</label>
                            <input type="text" name="icon" class="form-control" placeholder="e.g. fas fa-chart-line"
                                value="fas fa-star">
                            <div class="form-text small">{{ __('messages.font_awesome_hint') }}</div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn btn-primary px-4">{{ __('messages.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Quick Add Logic
                document.querySelectorAll('.quick-add-btn').forEach(btn => {
                    btn.addEventListener('click', function () {
                        document.getElementById('qa_route').value = this.dataset.route;
                        document.getElementById('qa_label').value = this.dataset.label;
                        document.getElementById('qa_icon').value = this.dataset.icon;
                        document.getElementById('quickAddForm').submit();
                    });
                });

                // Sorting Logic
                const el = document.getElementById('sortable-favorites');
                if (el) {
                    Sortable.create(el, {
                        handle: '.drag-handle',
                        animation: 150,
                        onEnd: function () {
                            const order = Array.from(el.children).map(child => child.dataset.id);
                            fetch('{{ route('acp.system.favorite-screens.reorder') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ order: order })
                            });
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection