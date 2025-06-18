@extends('layouts.app')

@section('title', 'Dashboard do Cliente')

@section('content')
<div class="container">
    <h1 class="mb-4">Meu Painel</h1>
    
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-dog me-2 text-primary"></i>Meus Pets</h5>
                    <p class="card-text">Gerencie seus pets e veja seus históricos.</p>
                    <a href="{{ route('pets.index') }}" class="btn btn-outline-primary">Ver Pets</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-calendar-alt me-2 text-primary"></i>Agendamentos</h5>
                    <p class="card-text">Acompanhe e crie novos agendamentos para seus pets.</p>
                    <a href="{{ route('appointments.index') }}" class="btn btn-outline-primary">Ver Agendamentos</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-shopping-bag me-2 text-primary"></i>Pedidos</h5>
                    <p class="card-text">Acompanhe o status dos seus pedidos.</p>
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-primary">Ver Pedidos</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Próximos Agendamentos</h5>
                </div>
                <div class="card-body">
                    @if($upcomingAppointments->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($upcomingAppointments as $appointment)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $appointment->service->name }}</strong> para {{ $appointment->pet->name }}
                                            <br>
                                            <small class="text-muted">
                                                {{ $appointment->appointment_datetime->format('d/m/Y H:i') }} - 
                                                {{ $appointment->service->petshop->name }}
                                            </small>
                                        </div>
                                        <span class="badge bg-{{ $appointment->status == 'pending' ? 'warning' : ($appointment->status == 'confirmed' ? 'success' : 'secondary') }}">
                                            {{ $appointment->status == 'pending' ? 'Pendente' : ($appointment->status == 'confirmed' ? 'Confirmado' : 'Cancelado') }}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-center mb-0">Nenhum agendamento próximo.</p>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('appointments.create') }}" class="btn btn-primary">Novo Agendamento</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Últimos Pedidos</h5>
                </div>
                <div class="card-body">
                    @if($recentOrders->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($recentOrders as $order)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>Pedido #{{ $order->id }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $order->created_at->format('d/m/Y') }} - R$ {{ number_format($order->total_amount, 2, ',', '.') }}</small>
                                        </div>
                                        <span class="badge bg-{{ $order->status == 'pending' ? 'warning' : ($order->status == 'paid' ? 'info' : ($order->status == 'delivered' ? 'success' : 'secondary')) }}">
                                            {{ $order->status == 'pending' ? 'Pendente' : ($order->status == 'paid' ? 'Pago' : ($order->status == 'delivered' ? 'Entregue' : ucfirst($order->status))) }}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-center mb-0">Nenhum pedido recente.</p>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('products.index') }}" class="btn btn-primary">Comprar Produtos</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection