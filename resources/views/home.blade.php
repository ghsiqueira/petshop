@extends('layouts.app')

@section('title', 'Início')

@section('content')
<!-- Hero Section -->
<div class="hero-section mb-5">
    <div class="p-5 text-center bg-image rounded-3" style="
        background-image: url('{{ asset('img/hero-bg.jpg') }}');
        height: 400px;
        background-size: cover;
        background-position: center;
        position: relative;
    ">
        <div class="mask" style="background-color: rgba(0, 0, 0, 0.6); position: absolute; top: 0; left: 0; right: 0; bottom: 0; display: flex; align-items: center; justify-content: center;">
            <div class="text-white">
                <h1 class="mb-3">PetShop Online</h1>
                <h4 class="mb-3">Encontre tudo para o seu pet em um só lugar</h4>
                <a class="btn btn-primary" href="{{ route('products.index') }}" role="button">Ver Produtos</a>
            </div>
        </div>
    </div>
</div>

<!-- Featured Products Section -->
<section class="featured-products mb-5">
    <h2 class="text-center mb-4">Produtos em Destaque</h2>
    
    <div class="row">
        @forelse($featuredProducts as $product)
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('img/no-image.jpg') }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text text-muted">{{ $product->petshop->name }}</p>
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
                <div class="alert alert-info">Nenhum produto em destaque no momento.</div>
            </div>
        @endforelse
    </div>
    
    <div class="text-center mt-3">
        <a href="{{ route('products.index') }}" class="btn btn-primary">Ver Todos os Produtos</a>
    </div>
</section>

<!-- Featured PetShops Section -->
<section class="featured-petshops mb-5">
    <h2 class="text-center mb-4">Pet Shops em Destaque</h2>
    
    <div class="row">
        @forelse($featuredPetshops as $petshop)
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <img src="{{ $petshop->logo ? asset('storage/' . $petshop->logo) : asset('img/no-logo.jpg') }}" class="card-img-top" alt="{{ $petshop->name }}" style="height: 150px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title">{{ $petshop->name }}</h5>
                        <p class="card-text">{{ Str::limit($petshop->description, 100) }}</p>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <a href="{{ route('petshops.show', $petshop->id) }}" class="btn btn-outline-primary w-100">Visitar</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">Nenhum pet shop em destaque no momento.</div>
            </div>
        @endforelse
    </div>
    
    <div class="text-center mt-3">
        <a href="{{ route('petshops.index') }}" class="btn btn-primary">Ver Todos os Pet Shops</a>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="why-choose-us mb-5">
    <h2 class="text-center mb-4">Por que escolher o PetShop Online?</h2>
    
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100 text-center">
                <div class="card-body">
                    <i class="fas fa-shipping-fast fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Entrega Rápida</h5>
                    <p class="card-text">Receba produtos para seu pet no conforto da sua casa em até 48 horas.</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100 text-center">
                <div class="card-body">
                    <i class="fas fa-calendar-check fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Agendamento Online</h5>
                    <p class="card-text">Agende serviços para seu pet de forma rápida e fácil pelo nosso site.</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100 text-center">
                <div class="card-body">
                    <i class="fas fa-star fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Avaliações Reais</h5>
                    <p class="card-text">Confie em avaliações autênticas de outros donos de pets.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection