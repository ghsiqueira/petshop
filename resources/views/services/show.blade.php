@extends('layouts.app')

@section('title', $service->name)

@section('content')
<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Início</a></li>
            <li class="breadcrumb-item"><a href="{{ route('services.index') }}">Serviços</a></li>
            <li class="breadcrumb-item active">{{ $service->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Imagem do Serviço -->
        <div class="col-lg-6">
            @if($service->image)
                <img src="{{ asset('storage/' . $service->image) }}" class="img-fluid rounded" alt="{{ $service->name }}">
            @else
                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 400px;">
                    <i class="fas fa-concierge-bell fa-5x text-muted"></i>
                </div>
            @endif
        </div>

        <!-- Informações do Serviço -->
        <div class="col-lg-6">
            <div class="service-info">
                <h1 class="mb-3">{{ $service->name }}</h1>
                
                <!-- Petshop -->
                @if($service->petshop)
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Oferecido por:</h6>
                        <a href="{{ route('petshops.show', $service->petshop->id) }}" class="text-decoration-none">
                            <strong>{{ $service->petshop->name }}</strong>
                        </a>
                        @if($service->petshop->city)
                            <small class="text-muted">- {{ $service->petshop->city }}</small>
                        @endif
                    </div>
                @endif

                <!-- Preço -->
                <div class="price-section mb-4">
                    <h2 class="text-primary">R$ {{ number_format($service->price, 2, ',', '.') }}</h2>
                </div>

                <!-- Duração -->
                @if(isset($service->duration_minutes) && $service->duration_minutes)
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Duração:</h6>
                        <span class="badge bg-info">
                            <i class="fas fa-clock me-1"></i>
                            @if($service->duration_minutes >= 60)
                                {{ intval($service->duration_minutes / 60) }}h{{ $service->duration_minutes % 60 > 0 ? ' ' . ($service->duration_minutes % 60) . 'min' : '' }}
                            @else
                                {{ $service->duration_minutes }}min
                            @endif
                        </span>
                    </div>
                @endif

                <!-- Avaliação -->
                @if(isset($service->avg_rating) && $service->avg_rating > 0)
                    <div class="rating mb-4">
                        <h6 class="text-muted mb-1">Avaliação:</h6>
                        <div class="stars">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $service->avg_rating ? 'text-warning' : 'text-muted' }}"></i>
                            @endfor
                        </div>
                        <span class="ms-2">
                            {{ number_format($service->avg_rating, 1) }}/5
                            @if(isset($service->total_reviews) && $service->total_reviews > 0)
                                ({{ $service->total_reviews }} avaliação{{ $service->total_reviews > 1 ? 'ões' : '' }})
                            @endif
                        </span>
                    </div>
                @endif

                <!-- Botões de Ação -->
                <div class="action-buttons">
                    @auth
                        @if(auth()->user()->hasRole('client'))
                            <a href="{{ route('appointments.create', ['service_id' => $service->id]) }}" class="btn btn-success btn-lg me-2">
                                <i class="fas fa-calendar-plus me-2"></i>Agendar Serviço
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-success btn-lg me-2">
                            <i class="fas fa-calendar-plus me-2"></i>Fazer Login para Agendar
                        </a>
                    @endauth

                    <!-- Botão Voltar -->
                    <a href="{{ route('services.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Voltar aos Serviços
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Descrição Detalhada -->
    @if($service->description)
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Descrição do Serviço</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $service->description }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Informações do Petshop -->
    @if($service->petshop)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Sobre o {{ $service->petshop->name }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                @if($service->petshop->description)
                                    <p>{{ Str::limit($service->petshop->description, 200) }}</p>
                                @endif
                                
                                @if($service->petshop->address)
                                    <p class="mb-1">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        {{ $service->petshop->address }}
                                        @if($service->petshop->city), {{ $service->petshop->city }}@endif
                                        @if($service->petshop->state), {{ $service->petshop->state }}@endif
                                    </p>
                                @endif
                                
                                @if($service->petshop->phone)
                                    <p class="mb-1">
                                        <i class="fas fa-phone me-2"></i>
                                        {{ $service->petshop->phone }}
                                    </p>
                                @endif
                            </div>
                            <div class="col-md-4 text-md-end">
                                <a href="{{ route('petshops.show', $service->petshop->id) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-store me-2"></i>Ver Pet Shop
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.stars {
    display: inline-flex;
    gap: 2px;
}

.stars i {
    font-size: 1.1rem;
}

.action-buttons .btn {
    margin-bottom: 0.5rem;
}

@media (max-width: 768px) {
    .action-buttons .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}
</style>
@endsection