@extends('layouts.app')

@section('title', 'Dashboard do Petshop')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Dashboard do Petshop</h1>
        <div>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPetshopModal">
                <i class="fas fa-edit me-2"></i>Editar Informações
            </button>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-3 mb-4">
            <div class="card h-100 text-center">
                <div class="card-body">
                    <i class="fas fa-box fa-3x text-primary mb-3"></i>
                    <h2 class="display-4">{{ $productCount }}</h2>
                    <h5 class="card-title">Produtos</h5>
                </div>
                <div class="card-footer bg-white border-top-0">
                    <a href="{{ route('petshop.products.index') }}" class="btn btn-outline-primary btn-sm">Ver Todos</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card h-100 text-center">
                <div class="card-body">
                    <i class="fas fa-scissors fa-3x text-primary mb-3"></i>
                    <h2 class="display-4">{{ $serviceCount }}</h2>
                    <h5 class="card-title">Serviços</h5>
                </div>
                <div class="card-footer bg-white border-top-0">
                    <a href="{{ route('petshop.services.index') }}" class="btn btn-outline-primary btn-sm">Ver Todos</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card h-100 text-center">
                <div class="card-body">
                    <i class="fas fa-user-tie fa-3x text-primary mb-3"></i>
                    <h2 class="display-4">{{ $employeeCount }}</h2>
                    <h5 class="card-title">Funcionários</h5>
                </div>
                <div class="card-footer bg-white border-top-0">
                    <a href="{{ route('petshop.employees.index') }}" class="btn btn-outline-primary btn-sm">Ver Todos</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card h-100 text-center">
                <div class="card-body">
                    <i class="fas fa-calendar-alt fa-3x text-warning mb-3"></i>
                    <h2 class="display-4">{{ $pendingAppointments }}</h2>
                    <h5 class="card-title">Agendamentos Pendentes</h5>
                </div>
                <div class="card-footer bg-white border-top-0">
                    <a href="{{ route('petshop.appointments') }}" class="btn btn-outline-warning btn-sm">Ver Todos</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Pedidos Recentes</h5>
                </div>
                <div class="card-body p-0">
                    @if($recentOrders->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentOrders as $order)
                                <a href="{{ route('petshop.orders') }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">Pedido #{{ $order->id }}</h6>
                                        <small>{{ $order->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                    <p class="mb-1">Cliente: {{ $order->user->name }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</small>
                                        <span class="badge bg-{{ $order->status == 'pending' ? 'warning' : ($order->status == 'paid' ? 'info' : ($order->status == 'delivered' ? 'success' : 'secondary')) }}">
                                            {{ $order->status == 'pending' ? 'Pendente' : ($order->status == 'paid' ? 'Pago' : ($order->status == 'delivered' ? 'Entregue' : ucfirst($order->status))) }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center p-4">
                            <p class="mb-0">Nenhum pedido recente.</p>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-white">
                    <a href="{{ route('petshop.orders') }}" class="btn btn-outline-primary btn-sm">Ver Todos os Pedidos</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Ações Rápidas</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('petshop.products.create') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column justify-content-center align-items-center p-4">
                                <i class="fas fa-plus-circle fa-2x mb-2"></i>
                                <span>Adicionar Produto</span>
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('petshop.services.create') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column justify-content-center align-items-center p-4">
                                <i class="fas fa-plus-circle fa-2x mb-2"></i>
                                <span>Adicionar Serviço</span>
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('petshop.employees.create') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column justify-content-center align-items-center p-4">
                                <i class="fas fa-user-plus fa-2x mb-2"></i>
                                <span>Adicionar Funcionário</span>
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('petshop.appointments') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column justify-content-center align-items-center p-4">
                                <i class="fas fa-calendar-check fa-2x mb-2"></i>
                                <span>Ver Agendamentos</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar informações do petshop -->
<div class="modal fade" id="editPetshopModal" tabindex="-1" aria-labelledby="editPetshopModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('petshop.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="modal-header">
                    <h5 class="modal-title" id="editPetshopModalLabel">Editar Informações do Pet Shop</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nome*</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $petshop->name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="logo" class="form-label">Logo</label>
                            <input type="file" class="form-control" id="logo" name="logo">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ $petshop->description }}</textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="address" class="form-label">Endereço*</label>
                            <input type="text" class="form-control" id="address" name="address" value="{{ $petshop->address }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Telefone*</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ $petshop->phone }}" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email*</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ $petshop->email }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection