@extends('layouts.app')

@section('title', 'Pet Shops')

@section('content')
<div class="container">
    <h1 class="mb-4">Pet Shops</h1>
    
    <div class="row mb-4">
        <div class="col-md-6">
            <form action="{{ route('petshops.index') }}" method="GET" class="d-flex">
                <input type="text" name="search" class="form-control me-2" placeholder="Buscar pet shops..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-outline-primary">Buscar</button>
            </form>
        </div>
    </div>
    
    <div class="row">
        @forelse($petshops as $petshop)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ $petshop->logo ? asset('storage/' . $petshop->logo) : asset('img/no-logo.jpg') }}" class="card-img-top" alt="{{ $petshop->name }}" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title">{{ $petshop->name }}</h5>
                        <p class="card-text">{{ Str::limit($petshop->description, 150) }}</p>
                        <p class="text-muted mb-2">
                            <i class="fas fa-map-marker-alt me-2"></i>{{ $petshop->address }}
                        </p>
                        <p class="text-muted mb-2">
                            <i class="fas fa-phone me-2"></i>{{ $petshop->phone }}
                        </p>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <a href="{{ route('petshops.show', $petshop->id) }}" class="btn btn-outline-primary w-100">Visitar</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">Nenhum pet shop encontrado.</div>
            </div>
        @endforelse
    </div>
    
    <div class="d-flex justify-content-center mt-4">
        {{ $petshops->links() }}
    </div>
</div>
@endsection