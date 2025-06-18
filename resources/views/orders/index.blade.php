@extends('layouts.app')

@section('title', 'Meus Pedidos')

@section('content')
<div class="container">
    <h1 class="mb-4">Meus Pedidos</h1>
    
    <div class="card">
        <div class="card-header bg-light">
            <ul class="nav nav-tabs card-header-tabs" id="orderTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">Todos</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="false">Pendentes</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab" aria-controls="completed" aria-selected="false">Concluídos</button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="orderTabsContent">
                <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Pedido #</th>
                                    <th>Data</th>
                                    <th>Valor Total</th>
                                    <th>Status</th>
                                    <th>Método de Pagamento</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr>
                                        <td>{{ $order->id }}</td>
                                        <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                        <td>R$ {{ number_format($order->total_amount, 2, ',', '.') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $order->status == 'pending' ? 'warning' : ($order->status == 'paid' ? 'info' : ($order->status == 'delivered' ? 'success' : 'secondary')) }}">
                                                {{ $order->status == 'pending' ? 'Pendente' : ($order->status == 'paid' ? 'Pago' : ($order->status == 'delivered' ? 'Entregue' : ucfirst($order->status))) }}
                                            </span>
                                        </td>
                                        <td>{{ $order->payment_method == 'credit_card' ? 'Cartão de Crédito' : ($order->payment_method == 'bank_slip' ? 'Boleto' : 'Pix') }}</td>
                                        <td>
                                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">Detalhes</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Nenhum pedido encontrado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Pedido #</th>
                                    <th>Data</th>
                                    <th>Valor Total</th>
                                    <th>Status</th>
                                    <th>Método de Pagamento</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $pendingOrders = $orders->whereIn('status', ['pending', 'paid', 'shipped']);
                                @endphp
                                
                                @forelse($pendingOrders as $order)
                                    <tr>
                                        <td>{{ $order->id }}</td>
                                        <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                        <td>R$ {{ number_format($order->total_amount, 2, ',', '.') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $order->status == 'pending' ? 'warning' : ($order->status == 'paid' ? 'info' : 'primary') }}">
                                                {{ $order->status == 'pending' ? 'Pendente' : ($order->status == 'paid' ? 'Pago' : 'Enviado') }}
                                            </span>
                                        </td>
                                        <td>{{ $order->payment_method == 'credit_card' ? 'Cartão de Crédito' : ($order->payment_method == 'bank_slip' ? 'Boleto' : 'Pix') }}</td>
                                        <td>
                                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">Detalhes</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Nenhum pedido pendente encontrado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Pedido #</th>
                                    <th>Data</th>
                                    <th>Valor Total</th>
                                    <th>Status</th>
                                    <th>Método de Pagamento</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $completedOrders = $orders->whereIn('status', ['delivered', 'cancelled']);
                                @endphp
                                
                                @forelse($completedOrders as $order)
                                    <tr>
                                        <td>{{ $order->id }}</td>
                                        <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                        <td>R$ {{ number_format($order->total_amount, 2, ',', '.') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $order->status == 'delivered' ? 'success' : 'danger' }}">
                                                {{ $order->status == 'delivered' ? 'Entregue' : 'Cancelado' }}
                                            </span>
                                        </td>
                                        <td>{{ $order->payment_method == 'credit_card' ? 'Cartão de Crédito' : ($order->payment_method == 'bank_slip' ? 'Boleto' : 'Pix') }}</td>
                                        <td>
                                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">Detalhes</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Nenhum pedido concluído encontrado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-center mt-4">
        {{ $orders->links() }}
    </div>
</div>
@endsection