@extends('layouts.app')

@section('title', 'Produtos')

@section('content')
<div class="container">
    <h1 class="mb-4">Produtos</h1>
    
    <div class="row mb-4">
        <div class="col-md-6">
            <form action="{{ route('products.index') }}" method="GET" class="d-flex">
                <input type="text" name="search" class="form-control me-2" placeholder="Buscar produtos..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-outline-primary">Buscar</button>
            </form>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-end">
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown">
                        Categorias
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('products.index') }}">Todas</a></li>
                        <li><a class="dropdown-item" href="{{ route('products.index', ['category' => 'food']) }}">Alimentação</a></li>
                        <li><a class="dropdown-item" href="{{ route('products.index', ['category' => 'toys']) }}">Brinquedos</a></li>
                        <li><a class="dropdown-item" href="{{ route('products.index', ['category' => 'accessories']) }}">Acessórios</a></li>
                        <li><a class="dropdown-item" href="{{ route('products.index', ['category' => 'health']) }}">Saúde</a></li>
                    </ul>
                </div>
                
                <div class="dropdown ms-2">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown">
                        Ordenar
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('products.index', ['sort' => 'newest']) }}">Mais recentes</a></li>
                        <li><a class="dropdown-item" href="{{ route('products.index', ['sort' => 'price_asc']) }}">Menor preço</a></li>
                        <li><a class="dropdown-item" href="{{ route('products.index', ['sort' => 'price_desc']) }}">Maior preço</a></li>
                        <li><a class="dropdown-item" href="{{ route('products.index', ['sort' => 'name_asc']) }}">Nome (A-Z)</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        @forelse($products as $product)
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
                <div class="alert alert-info">Nenhum produto encontrado.</div>
            </div>
        @endforelse
    </div>
    
    <div class="d-flex justify-content-center mt-4">
        {{ $products->links() }}
    </div>
</div>
@endsection