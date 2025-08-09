@extends('layouts.app')

@section('title', 'Serviços')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Serviços Disponíveis</h1>
                
                <!-- Filtros Rápidos -->
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-filter me-2"></i>Filtrar
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('services.index') }}">Todos</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('services.index', ['sort' => 'price_asc']) }}">Menor preço</a></li>
                        <li><a class="dropdown-item" href="{{ route('services.index', ['sort' => 'price_desc']) }}">Maior preço</a></li>
                        <li><a class="dropdown-item" href="{{ route('services.index', ['sort' => 'name']) }}">Nome A-Z</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @forelse($services as $service)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 service-card">
                    @if($service->image)
                        <img src="{{ asset('storage/' . $service->image) }}" class="card-img-top" alt="{{ $service->name }}" style="height: 200px; object-fit: cover;">
                    @else
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="fas fa-concierge-bell fa-3x text-muted"></i>
                        </div>
                    @endif
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $service->name }}</h5>
                        
                        @if($service->description)
                            <p class="card-text">{{ Str::limit($service->description, 100) }}</p>
                        @endif
                        
                        <div class="service-info mb-3">
                            @if(isset($service->duration_minutes) && $service->duration_minutes)
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    @if($service->duration_minutes >= 60)
                                        {{ intval($service->duration_minutes / 60) }}h{{ $service->duration_minutes % 60 > 0 ? ' ' . ($service->duration_minutes % 60) . 'min' : '' }}
                                    @else
                                        {{ $service->duration_minutes }}min
                                    @endif
                                </small>
                                <br>
                            @endif
                            
                            @if($service->petshop)
                                <small class="text-muted">
                                    <i class="fas fa-store me-1"></i>{{ $service->petshop->name }}
                                </small>
                            @endif
                        </div>
                        
                        <!-- Preço -->
                        <div class="price-section mb-3">
                            <h4 class="text-primary mb-0">R$ {{ number_format($service->price, 2, ',', '.') }}</h4>
                        </div>
                        
                        <!-- Avaliação (se existir) -->
                        @if(isset($service->avg_rating) && $service->avg_rating > 0)
                            <div class="rating mb-3">
                                <div class="stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $service->avg_rating ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                </div>
                                <small class="text-muted ms-2">
                                    ({{ $service->avg_rating }}/5)
                                    @if(isset($service->total_reviews) && $service->total_reviews > 0)
                                        - {{ $service->total_reviews }} avaliação(ões)
                                    @endif
                                </small>
                            </div>
                        @endif
                        
                        <!-- Botões -->
                        <div class="mt-auto">
                            <div class="d-grid gap-2">
                                <a href="{{ route('services.show', $service->id) }}" class="btn btn-primary">
                                    <i class="fas fa-eye me-2"></i>Ver Detalhes
                                </a>
                                
                                @auth
                                    @if(auth()->user()->hasRole('client'))
                                        <a href="{{ route('appointments.create', ['service_id' => $service->id]) }}" class="btn btn-success">
                                            <i class="fas fa-calendar-plus me-2"></i>Agendar
                                        </a>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-outline-success">
                                        <i class="fas fa-calendar-plus me-2"></i>Fazer Login para Agendar
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-concierge-bell fa-4x text-muted mb-3"></i>
                    <h4>Nenhum serviço encontrado</h4>
                    <p class="text-muted">Não há serviços disponíveis no momento.</p>
                </div>
            </div>
        @endforelse
    </div>
    
    <!-- Paginação -->
    @if($services->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $services->appends(request()->query())->links() }}
        </div>
    @endif
</div>

<style>
.service-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border: 1px solid #e3e6f0;
}

.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.stars {
    display: inline-flex;
    gap: 2px;
}

.stars i {
    font-size: 0.9rem;
}
</style>
@endsection