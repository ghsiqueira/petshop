@extends('layouts.app')

@section('title', 'Dashboard de Administrador')

@section('content')
<div class="container">
    <h1 class="mb-4">Painel Administrativo</h1>
    
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x mb-3"></i>
                    <h2 class="display-4">{{ $userCount }}</h2>
                    <h5 class="card-title">Usuários</h5>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-light btn-sm w-100">Gerenciar Usuários</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body text-center">
                    <i class="fas fa-store fa-3x mb-3"></i>
                    <h2 class="display-4">{{ $petshopCount }}</h2>
                    <h5 class="card-title">Pet Shops</h5>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('admin.petshops.index') }}" class="btn btn-outline-light btn-sm w-100">Gerenciar Pet Shops</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body text-center">
                    <i class="fas fa-paw fa-3x mb-3"></i>
                    <h2 class="display-4">{{ $petCount }}</h2>
                    <h5 class="card-title">Pets</h5>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="#" class="btn btn-outline-light btn-sm w-100">Ver Detalhes</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white h-100">
                <div class="card-body text-center">
                    <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                    <h2 class="display-4">{{ $orderCount }}</h2>
                    <h5 class="card-title">Pedidos</h5>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="#" class="btn btn-outline-light btn-sm w-100">Ver Detalhes</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Usuários Recentes</h5>
                </div>
                <div class="card-body p-0">
                    @if(count($recentUsers) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Papel</th>
                                        <th>Cadastro</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentUsers as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                @foreach($user->roles as $role)
                                                    <span class="badge bg-primary">{{ $role->name }}</span>
                                                @endforeach
                                            </td>
                                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center py-3">Nenhum usuário recente.</p>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary btn-sm">Ver Todos</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Pet Shops Recentes</h5>
                </div>
                <div class="card-body p-0">
                    @if(count($recentPetshops) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Nome</th>
                                        <th>Proprietário</th>
                                        <th>Status</th>
                                        <th>Cadastro</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentPetshops as $petshop)
                                        <tr>
                                            <td>{{ $petshop->name }}</td>
                                            <td>{{ $petshop->user->name }}</td>
                                            <td>
                                                <span class="badge bg-{{ $petshop->is_active ? 'success' : 'danger' }}">
                                                    {{ $petshop->is_active ? 'Ativo' : 'Inativo' }}
                                                </span>
                                            </td>
                                            <td>{{ $petshop->created_at->format('d/m/Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center py-3">Nenhum pet shop recente.</p>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.petshops.index') }}" class="btn btn-outline-primary btn-sm">Ver Todos</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection