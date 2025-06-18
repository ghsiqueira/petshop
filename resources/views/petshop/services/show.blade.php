@extends('layouts.app')

@section('title', $service->name)

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            
            @if(auth()->user()->hasRole('petshop'))
                <li class="breadcrumb-item"><a href="{{ route('petshop.services.index') }}">Meus Serviços</a></li>
            @else
                <li class="breadcrumb-item"><a href="{{ route('services.index') }}">Serviços</a></li>
            @endif
            
            <li class="breadcrumb-item active" aria-current="page">{{ $service->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $service->name }}</h5>
                    
                    @if(auth()->user()->hasRole('petshop') && auth()->id() == $service->petshop->user_id)
                        <div>
                            <a href="{{ route('petshop.services.edit', $service->id) }}" class="btn btn-light btn-sm me-2">
                                <i class="fas fa-edit me-1"></i>Editar
                            </a>
                            
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteServiceModal">
                                <i class="fas fa-trash me-1"></i>Excluir
                            </button>
                        </div>
                    @endif
                </div>
                
                <div class="card-body">
                    <h5 class="text-primary mb-3">Descrição</h5>
                    <p>{{ $service->description }}</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="text-muted">Preço:</h6>
                                <h4 class="text-success">R$ {{ number_format($service->price, 2, ',', '.') }}</h4>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="text-muted">Duração:</h6>
                                <h5>{{ $service->duration_minutes }} minutos</h5>
                            </div>
                        </div>
                    </div>

                    @if(!auth()->user()->hasRole('petshop'))
                        <div class="mt-4">
                            <a href="{{ route('appointments.create', ['service_id' => $service->id]) }}" class="btn btn-primary">
                                <i class="fas fa-calendar-plus me-2"></i>Agendar Serviço
                            </a>
                        </div>
                    @endif
                    
                    @if(auth()->user()->hasRole('petshop') && auth()->id() == $service->petshop->user_id)
                        <div class="mt-4">
                            <h5 class="text-primary mb-3">Status</h5>
                            <p>
                                <span class="badge bg-{{ $service->is_active ? 'success' : 'danger' }}">
                                    {{ $service->is_active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </p>
                            
                            <form action="{{ route('petshop.services.toggle-status', $service->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-{{ $service->is_active ? 'warning' : 'success' }}">
                                    <i class="fas fa-toggle-{{ $service->is_active ? 'off' : 'on' }} me-1"></i>
                                    {{ $service->is_active ? 'Desativar' : 'Ativar' }} Serviço
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Avaliações -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Avaliações</h5>
                </div>
                <div class="card-body">
                    @if($service->reviews->count() > 0)
                        <div class="mb-4">
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
                            @foreach($service->reviews as $review)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-2">
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
                    @else
                        <p class="text-muted">Ainda não há avaliações para este serviço.</p>
                    @endif
                    
                    @if(!auth()->user()->hasRole('petshop') && 
                        auth()->user()->appointments()
                               ->where('service_id', $service->id)
                               ->where('status', 'completed')
                               ->exists() && 
                        !$service->reviews()->where('user_id', auth()->id())->exists())
                        <div class="mt-4" id="review-form">
                            <h5 class="mb-3">Deixe sua avaliação</h5>
                            <form action="{{ route('reviews.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="reviewable_type" value="App\Models\Service">
                                <input type="hidden" name="reviewable_id" value="{{ $service->id }}">
                                
                                <div class="mb-3">
                                    <label for="rating" class="form-label">Avaliação*</label>
                                    <div class="rating-input">
                                        <div class="btn-group" role="group" aria-label="Rating">
                                            @for($i = 5; $i >= 1; $i--)
                                                <input type="radio" class="btn-check" name="rating" id="rating{{ $i }}" value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }} required>
                                                <label class="btn btn-outline-warning" for="rating{{ $i }}">
                                                    {{ $i }} <i class="fas fa-star"></i>
                                                </label>
                                            @endfor
                                        </div>
                                    </div>
                                    @error('rating')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="comment" class="form-label">Comentário*</label>
                                    <textarea class="form-control @error('comment') is-invalid @enderror" id="comment" name="comment" rows="3" required>{{ old('comment') }}</textarea>
                                    @error('comment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Enviar Avaliação</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Sobre o Pet Shop</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ $service->petshop->logo ? asset('storage/' . $service->petshop->logo) : asset('img/no-logo.jpg') }}" 
                            class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;" 
                            alt="{{ $service->petshop->name }}">
                        <h5 class="mb-0">{{ $service->petshop->name }}</h5>
                    </div>
                    
                    <p>{{ $service->petshop->description }}</p>
                    
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-map-marker-alt text-danger me-2"></i>
                            {{ $service->petshop->address }}
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-phone text-success me-2"></i>
                            {{ $service->petshop->phone }}
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-clock text-primary me-2"></i>
                            {{ $service->petshop->opening_hours }}
                        </li>
                    </ul>
                    
                    <a href="{{ route('petshops.show', $service->petshop->id) }}" class="btn btn-outline-primary">
                        Ver mais sobre o Pet Shop
                    </a>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Outros Serviços</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @php
                            $otherServices = $service->petshop->services()
                                            ->where('id', '!=', $service->id)
                                            ->where('is_active', true)
                                            ->limit(5)
                                            ->get();
                        @endphp
                        
                        @forelse($otherServices as $otherService)
                            <li class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ route('services.show', $otherService->id) }}" class="text-decoration-none">
                                        {{ $otherService->name }}
                                    </a>
                                    <span class="badge bg-primary rounded-pill">
                                        R$ {{ number_format($otherService->price, 2, ',', '.') }}
                                    </span>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item px-0">Nenhum outro serviço disponível.</li>
                        @endforelse
                    </ul>
                    
                    <div class="mt-3">
                        <a href="{{ route('petshops.show', $service->petshop->id) }}" class="btn btn-sm btn-outline-secondary w-100">
                            Ver todos os serviços
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de confirmação para excluir serviço -->
    @if(auth()->user()->hasRole('petshop') && auth()->id() == $service->petshop->user_id)
        <div class="modal fade" id="deleteServiceModal" tabindex="-1" aria-labelledby="deleteServiceModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteServiceModalLabel">Confirmar Exclusão</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Tem certeza que deseja excluir o serviço <strong>{{ $service->name }}</strong>?</p>
                        <p class="text-danger"><strong>Atenção:</strong> Esta ação não pode ser desfeita e todos os agendamentos relacionados serão perdidos.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <form action="{{ route('petshop.services.destroy', $service->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Excluir Serviço</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection