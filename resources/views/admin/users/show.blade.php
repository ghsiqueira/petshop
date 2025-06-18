@extends('layouts.app')

@section('title', 'Detalhes do Usuário')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Usuários</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detalhes do Usuário</li>
        </ol>
    </nav>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Informações do Usuário</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center mb-4 mb-md-0">
                    <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('img/no-profile-picture.jpg') }}" class="img-thumbnail rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;" alt="Foto de perfil">
                    
                    <div>
                        @foreach($user->roles as $role)
                            <span class="badge bg-primary">{{ ucfirst($role->name) }}</span>
                        @endforeach
                    </div>
                </div>
                
                <div class="col-md-9">
                    <h3>{{ $user->name }}</h3>
                    
                    <div class="row mt-4">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Email:</h6>
                            <p>{{ $user->email }}</p>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Data de Cadastro:</h6>
                            <p>{{ $user->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        
                        @if($user->phone)
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted">Telefone:</h6>
                                <p>{{ $user->phone }}</p>
                            </div>
                        @endif
                        
                        @if($user->address)
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted">Endereço:</h6>
                                <p>{{ $user->address }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary me-2">Voltar</a>
                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary">Editar</a>
            </div>
        </div>
    </div>
    
    @if($user->hasRole('client') && $user->pets->count() > 0)
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Pets</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Espécie</th>
                                <th>Raça</th>
                                <th>Gênero</th>
                                <th>Data de Nascimento</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->pets as $pet)
                                <tr>
                                    <td>{{ $pet->name }}</td>
                                    <td>{{ ucfirst($pet->species) }}</td>
                                    <td>{{ $pet->breed ?? 'N/A' }}</td>
                                    <td>{{ $pet->gender == 'male' ? 'Macho' : ($pet->gender == 'female' ? 'Fêmea' : 'Desconhecido') }}</td>
                                    <td>{{ $pet->birth_date ? $pet->birth_date->format('d/m/Y') : 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
    
    @if($user->hasRole('petshop') && $user->petshop)
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Informações do Pet Shop</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center mb-4 mb-md-0">
                        <img src="{{ $user->petshop->logo ? asset('storage/' . $user->petshop->logo) : asset('img/no-logo.jpg') }}" class="img-thumbnail mb-3" style="max-height: 150px;" alt="Logo do Pet Shop">
                    </div>
                    
                    <div class="col-md-9">
                        <h4>{{ $user->petshop->name }}</h4>
                        <p>{{ $user->petshop->description }}</p>
                        
                        <div class="row mt-3">
                            <div class="col-md-6 mb-2">
                                <h6 class="text-muted">Endereço:</h6>
                                <p>{{ $user->petshop->address }}</p>
                            </div>
                            
                            <div class="col-md-6 mb-2">
                                <h6 class="text-muted">Telefone:</h6>
                                <p>{{ $user->petshop->phone }}</p>
                            </div>
                            
                            <div class="col-md-6 mb-2">
                                <h6 class="text-muted">Email:</h6>
                                <p>{{ $user->petshop->email }}</p>
                            </div>
                            
                            <div class="col-md-6 mb-2">
                                <h6 class="text-muted">Status:</h6>
                                <p>
                                    <span class="badge bg-{{ $user->petshop->is_active ? 'success' : 'danger' }}">
                                        {{ $user->petshop->is_active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.petshops.show', $user->petshop->id) }}" class="btn btn-outline-primary">Ver Detalhes do Pet Shop</a>
            </div>
        </div>
    @endif
</div>
@endsection