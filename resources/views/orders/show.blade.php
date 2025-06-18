@extends('layouts.app')

@section('title', 'Detalhes do Pedido #' . $order->id)

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Meus Pedidos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pedido #{{ $order->id }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detalhes do Pedido #{{ $order->id }}</h5>
                    <span class="badge bg-{{ $order->status == 'pending' ? 'warning' : ($order->status == 'paid' ? 'info' : ($order->status == 'shipped' ? 'primary' : ($order->status == 'delivered' ? 'success' : 'danger'))) }} fs-6">
                        {{ $order->status == 'pending' ? 'Pendente' : ($order->status == 'paid' ? 'Pago' : ($order->status == 'shipped' ? 'Enviado' : ($order->status == 'delivered' ? 'Entregue' : 'Cancelado'))) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th class="text-center">Quantidade</th>
                                    <th class="text-end">Preço Un.</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $item->product->image ? asset('storage/' . $item->product->image) : asset('img/no-image.jpg') }}" 
                                                 class="img-thumbnail me-3" style="width: 60px; height: 60px; object-fit: cover;" 
                                                 alt="{{ $item->product->name }}">
                                            <div>
                                                <h6 class="mb-0">{{ $item->product->name }}</h6>
                                                <small class="text-muted">{{ $item->product->petshop->name }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">R$ {{ number_format($item->price, 2, ',', '.') }}</td>
                                    <td class="text-end">R$ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}</td>
                                </tr>
                                @endforeach
                                <tr class="table-light">
                                    <td colspan="3" class="text-end fw-bold">Total:</td>
                                    <td class="text-end fw-bold">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($order->status == 'delivered')
            <div class="card border-success mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Pedido Entregue</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">Seu pedido foi entregue com sucesso! Esperamos que esteja satisfeito com seus produtos.</p>
                </div>
            </div>
            @endif

            @if($order->status == 'cancelled')
            <div class="card border-danger mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Pedido Cancelado</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">Este pedido foi cancelado. Entre em contato conosco se tiver dúvidas.</p>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Informações do Pedido</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Data do Pedido:</span>
                            <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Status:</span>
                            <span class="badge bg-{{ $order->status == 'pending' ? 'warning' : ($order->status == 'paid' ? 'info' : ($order->status == 'shipped' ? 'primary' : ($order->status == 'delivered' ? 'success' : 'danger'))) }}">
                                {{ $order->status == 'pending' ? 'Pendente' : ($order->status == 'paid' ? 'Pago' : ($order->status == 'shipped' ? 'Enviado' : ($order->status == 'delivered' ? 'Entregue' : 'Cancelado'))) }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Total:</span>
                            <span class="fw-bold">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Pagamento e Entrega</h5>
                </div>
                <div class="card-body">
                    <h6 class="mb-2">Método de Pagamento</h6>
                    <p class="mb-3">
                        <i class="fas {{ $order->payment_method == 'credit_card' ? 'fa-credit-card' : ($order->payment_method == 'bank_slip' ? 'fa-file-invoice' : 'fa-qrcode') }} me-2"></i>
                        {{ $order->payment_method == 'credit_card' ? 'Cartão de Crédito' : ($order->payment_method == 'bank_slip' ? 'Boleto Bancário' : 'PIX') }}
                    </p>

                    <h6 class="mb-2">Endereço de Entrega</h6>
                    <p class="mb-0">
                        <i class="fas fa-map-marker-alt me-2 text-danger"></i>
                        {{ $order->shipping_address }}
                    </p>
                </div>
            </div>

            @if($order->status == 'pending')
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Aguardando Pagamento</h5>
                </div>
                <div class="card-body">
                    <p>Seu pedido está aguardando confirmação de pagamento.</p>
                    @if($order->payment_method == 'bank_slip')
                    <button class="btn btn-outline-primary w-100 mb-2">
                        <i class="fas fa-download me-2"></i>Baixar Boleto
                    </button>
                    @elseif($order->payment_method == 'pix')
                    <div class="text-center mb-3">
                        <div style="background-color: #f5f5f5; padding: 10px; display: inline-block;">
                            <i class="fas fa-qrcode fa-5x"></i>
                        </div>
                    </div>
                    <button class="btn btn-outline-primary w-100 mb-2">
                        <i class="fas fa-copy me-2"></i>Copiar Código PIX
                    </button>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Voltar para Meus Pedidos
        </a>
    </div>
</div>
@endsection