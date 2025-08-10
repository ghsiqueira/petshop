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
                <div class="d-flex gap-2">
                    @include('components.export-buttons', ['type' => 'client'])
                    <a href="{{ route('pets.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Cadastrar Pet
                    </a>
                    <a href="{{ route('appointments.create') }}" class="btn btn-outline-primary">
                        <i class="fas fa-calendar-plus me-1"></i>Agendar Serviço
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de Métricas -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 card-hover">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Pedidos Este Ano
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $yearlyStats['total_orders'] ?? 0 }}
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
            <div class="card border-left-success shadow h-100 py-2 card-hover">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Gasto Total Este Ano
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                R$ {{ number_format($yearlyStats['total_spent'] ?? 0, 2, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2 card-hover">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Agendamentos Este Ano
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $yearlyStats['total_appointments'] ?? 0 }}
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

    <!-- Gráfico de Gastos e Próximos Agendamentos -->
    <div class="row mb-4">
        <!-- Gráfico de Gastos Mensais -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">💰 Meus Gastos nos Últimos 6 Meses</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="spendingChart" width="100%" height="50"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Próximos Agendamentos -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">📅 Próximos Agendamentos</h6>
                    <a href="{{ route('appointments.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i>
                    </a>
                </div>
                <div class="card-body">
                    @forelse($upcomingAppointments as $appointment)
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                            <div class="me-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-paw"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="font-weight-bold">{{ $appointment->pet->name ?? 'Pet' }}</div>
                                <div class="text-muted small">{{ $appointment->service->name ?? 'Serviço' }}</div>
                                <div class="text-primary small">
                                    {{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-calendar fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">Nenhum agendamento próximo</p>
                            <a href="{{ route('appointments.create') }}" class="btn btn-primary btn-sm">
                                Agendar Agora
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Meus Pets e Pedidos Recentes -->
    <div class="row">
        <!-- Meus Pets -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">🐕 Meus Pets</h6>
                    <a href="{{ route('pets.index') }}" class="btn btn-sm btn-outline-primary">
                        Ver Todos
                    </a>
                </div>
                <div class="card-body">
                    @forelse($userPets as $pet)
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    @if($pet->photo)
                                        <img src="{{ asset('storage/' . $pet->photo) }}" 
                                             alt="{{ $pet->name }}" 
                                             class="rounded-circle" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="fas fa-paw"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-weight-bold">{{ $pet->name }}</div>
                                    <small class="text-muted">
                                        {{ ucfirst($pet->species) }} • {{ $pet->breed }}
                                    </small>
                                    <br>
                                    <small class="text-primary">
                                        {{ $pet->appointments_count ?? 0 }} {{ ($pet->appointments_count ?? 0) == 1 ? 'agendamento' : 'agendamentos' }}
                                    </small>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('appointments.create', ['pet_id' => $pet->id]) }}" 
                                       class="btn btn-outline-primary btn-sm" 
                                       title="Agendar Serviço">
                                        <i class="fas fa-calendar-plus"></i>
                                    </a>
                                    <a href="{{ route('pets.show', $pet->id) }}" 
                                       class="btn btn-outline-info btn-sm" 
                                       title="Ver Detalhes">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-heart fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">Você ainda não cadastrou nenhum pet.</p>
                            <a href="{{ route('pets.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>Cadastrar Primeiro Pet
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Pedidos Recentes -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">🛍️ Pedidos Recentes</h6>
                    <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-primary">
                        Ver Todos
                    </a>
                </div>
                <div class="card-body">
                    @forelse($recentOrders as $order)
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="flex-grow-1">
                                <div class="font-weight-bold">Pedido #{{ $order->id }}</div>
                                <small class="text-muted">
                                    {{ $order->created_at->format('d/m/Y H:i') }}
                                </small>
                                <br>
                                <span class="badge bg-{{ 
                                    match($order->status) {
                                        'pending' => 'warning',
                                        'paid' => 'info',
                                        'shipped' => 'primary',
                                        'delivered' => 'success',
                                        'cancelled' => 'danger',
                                        default => 'secondary'
                                    }
                                }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                            <div class="text-end">
                                <div class="font-weight-bold text-success">
                                    R$ {{ number_format($order->total_amount, 2, ',', '.') }}
                                </div>
                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-primary btn-sm">
                                    Ver
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-cart fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">Nenhum pedido realizado ainda</p>
                            <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm">
                                Começar a Comprar
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Estatísticas dos Pets Favoritos -->
    @if(isset($favoritePets) && $favoritePets->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">⭐ Pets com Mais Agendamentos</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($favoritePets as $petStat)
                            <div class="col-md-4 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span class="badge bg-warning rounded-pill">#{{ $loop->iteration }}</span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="font-weight-bold">{{ $petStat->pet->name ?? 'Pet' }}</div>
                                        <small class="text-muted">
                                            {{ $petStat->appointments_count ?? 0 }} {{ ($petStat->appointments_count ?? 0) == 1 ? 'agendamento' : 'agendamentos' }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Seção Adicional de Insights -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">💡 Insights e Recomendações</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="text-center">
                                <i class="fas fa-award fa-3x text-warning mb-3"></i>
                                <h6>Pet Mais Ativo</h6>
                                @if(isset($favoritePets) && $favoritePets->count() > 0)
                                    <p class="text-muted">{{ $favoritePets->first()->pet->name ?? 'N/A' }}</p>
                                @else
                                    <p class="text-muted">Nenhum pet ainda</p>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="text-center">
                                <i class="fas fa-chart-line fa-3x text-success mb-3"></i>
                                <h6>Gasto Médio Mensal</h6>
                                <p class="text-muted">
                                    R$ {{ number_format(($yearlyStats['total_spent'] ?? 0) / 12, 2, ',', '.') }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="text-center">
                                <i class="fas fa-calendar-alt fa-3x text-info mb-3"></i>
                                <h6>Próximo Agendamento</h6>
                                @if(isset($upcomingAppointments) && $upcomingAppointments->count() > 0)
                                    <p class="text-muted">{{ \Carbon\Carbon::parse($upcomingAppointments->first()->appointment_datetime)->format('d/m') }}</p>
                                @else
                                    <p class="text-muted">Nenhum agendado</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ações Rápidas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">⚡ Ações Rápidas</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-lg w-100">
                                <i class="fas fa-shopping-bag fa-2x mb-2"></i>
                                <br>Comprar Produtos
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('appointments.create') }}" class="btn btn-outline-success btn-lg w-100">
                                <i class="fas fa-calendar-plus fa-2x mb-2"></i>
                                <br>Agendar Serviço
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('pets.create') }}" class="btn btn-outline-info btn-lg w-100">
                                <i class="fas fa-plus fa-2x mb-2"></i>
                                <br>Cadastrar Pet
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('orders.index') }}" class="btn btn-outline-warning btn-lg w-100">
                                <i class="fas fa-history fa-2x mb-2"></i>
                                <br>Ver Histórico
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.border-left-success { border-left: 0.25rem solid #1cc88a !important; }
.border-left-primary { border-left: 0.25rem solid #4e73df !important; }
.border-left-info { border-left: 0.25rem solid #36b9cc !important; }

.chart-area {
    position: relative;
    height: 400px;
    width: 100%;
}

.text-gray-800 { color: #5a5c69 !important; }
.text-gray-300 { color: #dddfeb !important; }

.card-hover {
    transition: all 0.3s;
}

.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
}

@keyframes fadeInUp {
    from { 
        opacity: 0; 
        transform: translateY(10px); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0); 
    }
}

.card {
    animation: fadeInUp 0.4s ease-out;
}

.btn-group .btn {
    border-radius: 0.25rem;
    margin-left: 2px;
}

/* Gap utility for older Bootstrap versions */
.d-flex.gap-2 > * + * {
    margin-left: 0.5rem;
}

.btn-lg {
    padding: 1rem 1.5rem;
    font-size: 0.9rem;
}

.btn-outline-primary:hover,
.btn-outline-success:hover,
.btn-outline-info:hover,
.btn-outline-warning:hover {
    transform: translateY(-2px);
    transition: all 0.3s ease;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dados do gráfico de gastos
    const spendingData = @json($spendingChart ?? []);
    
    // Debug: verificar os dados no console
    console.log('Spending Data:', spendingData);
    
    // Verificar se temos dados válidos
    if (!spendingData || spendingData.length === 0) {
        console.warn('Nenhum dado de gastos encontrado');
        document.getElementById('spendingChart').parentElement.innerHTML = 
            '<div class="d-flex align-items-center justify-content-center h-100">' +
            '<div class="text-center">' +
            '<i class="fas fa-chart-line fa-3x text-gray-300 mb-3"></i>' +
            '<p class="text-muted">Nenhum gasto registrado nos últimos 6 meses</p>' +
            '</div></div>';
        return;
    }

    // Gráfico de gastos mensais
    const spendingCtx = document.getElementById('spendingChart').getContext('2d');
    new Chart(spendingCtx, {
        type: 'line',
        data: {
            labels: spendingData.map(item => item.month),
            datasets: [{
                label: 'Gastos (R$)',
                data: spendingData.map(item => parseFloat(item.amount) || 0),
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#1cc88a',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Gastos: R$ ' + context.parsed.y.toLocaleString('pt-BR', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'R$ ' + value.toLocaleString('pt-BR', {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            });
                        }
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            elements: {
                line: {
                    tension: 0.4
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });

    // Animações de entrada para cards
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });

    // Hover effects para ações rápidas
    const quickActionBtns = document.querySelectorAll('.btn-outline-primary, .btn-outline-success, .btn-outline-info, .btn-outline-warning');
    quickActionBtns.forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>
@endpush