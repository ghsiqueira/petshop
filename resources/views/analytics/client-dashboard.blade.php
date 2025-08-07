@extends('layouts.app')

@section('title', 'Meu Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">🐾 Meu Dashboard</h1>
                    <p class="text-muted">{{ auth()->user()->name }} - Resumo das suas atividades</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de Métricas -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Pedidos Este Ano
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $yearlyStats['total_orders'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Agendamentos Este Ano
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $yearlyStats['total_appointments'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico + Próximos Agendamentos -->
    <div class="row mb-4">
        <!-- Gráfico de Gastos -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">📊 Meus Gastos Mensais</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="spendingChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Próximos Agendamentos -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">📅 Próximos Agendamentos</h6>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @forelse($upcomingAppointments as $appointment)
                    <div class="d-flex align-items-center mb-3 p-2 bg-light rounded">
                        <div class="flex-shrink-0">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-paw text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fw-bold text-primary">{{ $appointment->service->name }}</div>
                            <div class="text-dark">{{ $appointment->pet->name }}</div>
                            <small class="text-muted">
                                {{ $appointment->appointment_datetime->format('d/m/Y H:i') }}
                                <br>{{ $appointment->service->petshop->name }}
                            </small>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="badge bg-{{ $appointment->status == 'confirmed' ? 'success' : 'warning' }}">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-plus fa-3x text-gray-300 mb-3"></i>
                        <p class="text-muted">Nenhum agendamento próximo</p>
                        <a href="{{ route('appointments.create') }}" class="btn btn-primary btn-sm">
                            Agendar Serviço
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Tabelas -->
    <div class="row">
        <!-- Pedidos Recentes -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">🛍️ Meus Pedidos Recentes</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Pedido</th>
                                    <th>Data</th>
                                    <th>Produtos</th>
                                    <th class="text-end">Valor</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('orders.show', $order->id) }}" class="text-decoration-none">
                                            <div class="fw-bold">#{{ $order->id }}</div>
                                        </a>
                                    </td>
                                    <td>
                                        <div>{{ $order->created_at->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                @if($order->items->first() && $order->items->first()->product->image)
                                                    <img src="{{ asset('storage/' . $order->items->first()->product->image) }}" 
                                                         class="rounded" width="32" height="32" style="object-fit: cover;">
                                                @else
                                                    <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                        <i class="fas fa-box text-white"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $order->items->count() }} {{ $order->items->count() == 1 ? 'produto' : 'produtos' }}</div>
                                                <small class="text-muted">{{ $order->items->first()->product->name ?? 'Produto' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end fw-bold">
                                        R$ {{ number_format($order->total_amount, 2, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $order->status == 'delivered' ? 'success' : ($order->status == 'paid' ? 'info' : ($order->status == 'pending' ? 'warning' : 'secondary')) }}">
                                            {{ $order->status == 'delivered' ? 'Entregue' : ($order->status == 'paid' ? 'Pago' : ($order->status == 'pending' ? 'Pendente' : ucfirst($order->status))) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <i class="fas fa-shopping-cart fa-3x text-gray-300 mb-3"></i>
                                        <p class="text-muted">Nenhum pedido realizado ainda</p>
                                        <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm">
                                            Explorar Produtos
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Meus Pets Favoritos -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">🐾 Meus Pets Favoritos</h6>
                </div>
                <div class="card-body">
                    @forelse($favoritePets as $petStat)
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            @if($petStat->pet->photo)
                                <img src="{{ asset('storage/' . $petStat->pet->photo) }}" 
                                     class="rounded-circle" width="48" height="48" style="object-fit: cover;">
                            @else
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                    <i class="fas fa-paw text-white"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fw-bold">{{ $petStat->pet->name }}</div>
                            <small class="text-muted">
                                {{ $petStat->pet->species }} • {{ $petStat->appointments_count }} {{ $petStat->appointments_count == 1 ? 'agendamento' : 'agendamentos' }}
                            </small>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="{{ route('appointments.create', ['pet_id' => $petStat->pet->id]) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-calendar-plus"></i>
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="fas fa-heart fa-3x text-gray-300 mb-3"></i>
                        <p class="text-muted">Cadastre seus pets e comece a agendar serviços!</p>
                        <a href="{{ route('pets.create') }}" class="btn btn-primary btn-sm">
                            Cadastrar Pet
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
// Dados do gráfico de gastos
const spendingData = @json($spendingChart);

// Gráfico de gastos mensais
const spendingCtx = document.getElementById('spendingChart').getContext('2d');
const spendingChart = new Chart(spendingCtx, {
    type: 'line',
    data: {
        labels: spendingData.map(item => item.month),
        datasets: [{
            label: 'Gastos (R$)',
            data: spendingData.map(item => item.amount),
            borderColor: '#1cc88a',
            backgroundColor: 'rgba(28, 200, 138, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'R$ ' + value.toLocaleString('pt-BR');
                    }
                }
            }
        },
        elements: {
            point: {
                radius: 6,
                hoverRadius: 8,
                backgroundColor: '#1cc88a',
                borderColor: '#fff',
                borderWidth: 2
            }
        }
    }
});
</script>

<style>
.border-left-success { border-left: 0.25rem solid #1cc88a !important; }
.border-left-primary { border-left: 0.25rem solid #4e73df !important; }
.border-left-info { border-left: 0.25rem solid #36b9cc !important; }

.chart-area {
    position: relative;
    height: 320px;
}

.text-gray-800 { color: #5a5c69 !important; }
.text-gray-300 { color: #dddfeb !important; }

.card:hover {
    transform: translateY(-2px);
    transition: all 0.3s;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.card {
    animation: fadeInUp 0.4s ease-out;
}
</style>
@endsection