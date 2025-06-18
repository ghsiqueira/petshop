@props(['service'])

<div class="service-reviews mb-4">
    <h5 class="text-primary mb-3">Avaliações</h5>
    
    @if($service->reviews->count() > 0)
        <div class="mb-3">
            <div class="d-flex align-items-center mb-2">
                @php
                    $averageRating = $service->reviews->avg('rating');
                @endphp
                <div class="me-2">
                    <h4 class="mb-0">{{ number_format($averageRating, 1) }}</h4>
                </div>
                <div class="ratings">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= round($averageRating))
                            <i class="fas fa-star text-warning"></i>
                        @else
                            <i class="far fa-star text-warning"></i>
                        @endif
                    @endfor
                </div>
                <div class="ms-2 text-muted">
                    ({{ $service->reviews->count() }} {{ $service->reviews->count() == 1 ? 'avaliação' : 'avaliações' }})
                </div>
            </div>
        </div>
        
        <div class="review-list">
            @foreach($service->reviews->take(3) as $review)
                <div class="card mb-2">
                    <div class="card-body py-2">
                        <div class="d-flex justify-content-between mb-1">
                            <div class="d-flex align-items-center">
                                <div class="ratings me-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <i class="fas fa-star text-warning"></i>
                                        @else
                                            <i class="far fa-star text-warning"></i>
                                        @endif
                                    @endfor
                                </div>
                                <strong>{{ $review->user->name }}</strong>
                            </div>
                            <small class="text-muted">{{ $review->created_at->format('d/m/Y') }}</small>
                        </div>
                        <p class="card-text">{{ $review->comment }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        
        @if($service->reviews->count() > 3)
            <div class="text-center">
                <a href="{{ route('services.show', $service->id) }}#review-form" class="btn btn-sm btn-outline-primary">
                    Ver todas as {{ $service->reviews->count() }} avaliações
                </a>
            </div>
        @endif
    @else
        <p class="text-muted">Ainda não há avaliações para este serviço.</p>
    @endif
</div>
```