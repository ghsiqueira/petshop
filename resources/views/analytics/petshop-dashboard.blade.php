@extends('layouts.app')

@section('title', 'Dashboard Analytics - ' . $petshop->name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">🏪 Dashboard Analytics</h1>
                    <p class="text-muted">{{ $petshop->name }}</p>
                </div>
                <div class="text-end">
                    <div class="badge bg-primary fs-6 me-2">
                        ⭐ {{ number_format(($avgProductRating + $avgServiceRating) / 2, 1) }} avaliação média
                    </div>
                    <a href="{{ route('petshop.dashboard') }}" class="btn btn-outline-primary">
                        <i class="fas fa-tachometer-alt me-1"></i>Dashboard Simples
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de Métricas Principais -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 card-hover">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Receita Mensal
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                R$ {{ number_format($monthlyRevenue, 2, ',', '.') }}
                            </div>
                            @if($growthPercentage > 0)
                                <small class="text-success">
                                    <i class="fas fa-arrow-up"></i> +{{ number_format($growthPercentage, 1) }}%
                                </small>
                            @elseif($growthPercentage < 0)
                                <small class="text-danger">
                                    <i class="fas fa-arrow-down"></i> {{ number_format($growthPercentage, 1) }}%
                                </small>
                            @else
                                <small class="text-muted">Sem variação</small>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 card-hover">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Clientes Únicos (30 dias)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $uniqueCustomers }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2 card-hover">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total de Produtos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $totalProducts }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2 card-hover">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total de Serviços
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $totalServices }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cut fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos e Análises -->
    <div class="row">
        <!-- Gráfico de Vendas -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">📊 Vendas dos Últimos 6 Meses</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="salesChart" width="100%" height="40"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status dos Agendamentos -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">📅 Agendamentos (30 dias)</h6>
                </div>
                <div class="card-body">
                    @if(!empty($appointmentStats))
                        @foreach($appointmentStats as $status => $count)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-capitalize">{{ $status }}</span>
                                    <span class="font-weight-bold">{{ $count }}</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    @php
                                        $total = array_sum($appointmentStats);
                                        $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                                        $color = match($status) {
                                            'completed' => 'success',
                                            'confirmed' => 'info',
                                            'pending' => 'warning',
                                            'cancelled' => 'danger',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <div class="progress-bar bg-{{ $color }}" 
                                         role="progressbar" 
                                         style="width: {{ $percentage }}%">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">Nenhum agendamento nos últimos 30 dias.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Produtos e Serviços Mais Populares -->
    <div class="row">
        <!-- Top Produtos -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">🏆 Top Produtos (30 dias)</h6>
                </div>
                <div class="card-body">
                    @if($topProducts->count() > 0)
                        @foreach($topProducts as $index => $item)
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <span class="badge bg-primary rounded-pill">#{{ $index + 1 }}</span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="font-weight-bold">{{ $item->product->name }}</div>
                                    <small class="text-muted">
                                        {{ $item->total_sold }} vendidos • 
                                        R$ {{ number_format($item->total_revenue, 2, ',', '.') }}
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">Nenhuma venda nos últimos 30 dias.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Top Serviços -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">⭐ Top Serviços (30 dias)</h6>
                </div>
                <div class="card-body">
                    @if($topServices->count() > 0)
                        @foreach($topServices as $index => $item)
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <span class="badge bg-success rounded-pill">#{{ $index + 1 }}</span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="font-weight-bold">{{ $item->service->name }}</div>
                                    <small class="text-muted">
                                        {{ $item->total_appointments }} agendamentos
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">Nenhum agendamento nos últimos 30 dias.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Informações Adicionais -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">📈 Resumo do Negócio</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="border-right pb-3">
                                <div class="h4 font-weight-bold text-primary">{{ $totalEmployees }}</div>
                                <div class="text-muted">Funcionários</div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="border-right pb-3">
                                <div class="h4 font-weight-bold text-success">{{ number_format($avgProductRating, 1) }}</div>
                                <div class="text-muted">Avaliação Produtos</div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="border-right pb-3">
                                <div class="h4 font-weight-bold text-info">{{ number_format($avgServiceRating, 1) }}</div>
                                <div class="text-muted">Avaliação Serviços</div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="pb-3">
                                <div class="h4 font-weight-bold text-warning">
                                    {{ $petshop->is_active ? 'Ativo' : 'Inativo' }}
                                </div>
                                <div class="text-muted">Status da Loja</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.card-hover {
    transition: transform 0.2s;
}
.card-hover:hover {
    transform: translateY(-2px);
}
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.text-gray-800 {
    color: #5a5c69 !important;
}
.text-gray-300 {
    color: #dddfeb !important;
}
.chart-area {
    position: relative;
    height: 450px; /* Aumentado de 300px para 450px */
    width: 100%;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dados do gráfico vindos do PHP
    const salesData = @json($salesChart);
    
    // Configuração do gráfico de vendas
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: salesData.labels,
            datasets: [{
                label: 'Vendas (R$)',
                data: salesData.data,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
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
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Vendas: R$ ' + context.parsed.y.toLocaleString('pt-BR', {
                                minimumFractionDigits: 2
                            });
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection