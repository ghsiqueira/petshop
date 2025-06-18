@extends('layouts.app')

@section('title', $petshop->name)

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Início</a></li>
            <li class="breadcrumb-item"><a href="{{ route('petshops.index') }}">Pet Shops</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $petshop->name }}</li>
        </ol>
    </nav>
    
    <div class="card mb-5">
        <div class="row g-0">
            <div class="col-md-4">
                <img src="{{ $petshop->logo ? asset('storage/' . $petshop->logo) : asset('img/no-logo.jpg') }}" class="img-fluid rounded-start" alt="{{ $petshop->name }}" style="height: 100%; object-fit: cover;">
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <h1 class="card-title">{{ $petshop->name }}</h1>
                    <p class="card-text">{{ $petshop->description }}</p>
                    
                    <div class="mb-3">
                        <h5><i class="fas fa-info-circle me-2 text-primary"></i>Informações</h5>
                        <p class="mb-2">
                            <i class="fas fa-map-marker-alt me-2"></i>{{ $petshop->address }}
                        </p>
                        <p class="mb-2">
                            <i class="fas fa-phone me-2"></i>{{ $petshop->phone }}
                        </p>
                        <p class="mb-2">
                            <i class="fas fa-envelope me-2"></i>{{ $petshop->email }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Produtos do Pet Shop -->
    <h2 class="mb-4">Produtos</h2>
    
    <div class="row">
        @forelse($products as $product)
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('img/no-image.jpg') }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text text-primary fw-bold">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                    </div>
                    <div class="card-footer bg-white border-top-0 d-flex justify-content-between">
                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-outline-primary">Ver Detalhes</a>
                        <form action="{{ route('cart.add', $product->id) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-cart-plus"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">Este pet shop ainda não possui produtos cadastrados.</div>
            </div>
        @endforelse
    </div>
    
    @if(count($products) > 8)
        <div class="text-center mb-5">
            <a href="{{ route('products.index', ['petshop_id' => $petshop->id]) }}" class="btn btn-primary">Ver Todos os Produtos</a>
        </div>
    @endif
    
    <!-- Serviços do Pet Shop -->
    <h2 class="mb-4">Serviços</h2>
    
    <div class="row">
        @forelse($services as $service)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $service->name }}</h5>
                        <p class="card-text">{{ Str::limit($service->description, 100) }}</p>
                        <p class="text-primary fw-bold">R$ {{ number_format($service->price, 2, ',', '.') }}</p>
                        <p class="card-text">
                            <small class="text-muted">Duração: {{ $service->duration_minutes }} minutos</small>
                        </p>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        @auth
                            @if(auth()->user()->hasRole('client'))
                                <a href="{{ route('appointments.create', ['service_id' => $service->id]) }}" class="btn btn-primary w-100">Agendar</a>
                            @else
                                <button class="btn btn-primary w-100" disabled>Agendar</button>
                                <small class="text-muted">Apenas clientes podem agendar serviços</small>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary w-100">Entre para Agendar</a>
                        @endauth
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">Este pet shop ainda não possui serviços cadastrados.</div>
            </div>
        @endforelse
    </div>
</div>
@endsection