@extends('layouts.app')

@section('title', 'Dashboard Administrativo')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">📊 Dashboard Administrativo</h1>
            <p class="text-muted">Visão geral do sistema - Últimos 30 dias</p>
        </div>
        <div class="d-flex gap-2">
            @include('components.export-buttons', ['type' => 'admin'])
            <div class="btn-group">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    ⏱ Período
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="?period=today">Hoje</a></li>
                    <li><a class="dropdown-item" href="?period=week">Esta Semana</a></li>
                    <li><a class="dropdown-item" href="?period=month">Este Mês</a></li>
                    <li><a class="dropdown-item" href="?period=year">Este Ano</a></li>
                </ul>
            </div>
            <button class="btn btn-success" onclick="window.location.reload()">
                🔄 Atualizar
            </button>
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
                                Total de Usuários
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $totalUsers ?? 0 }}
                            </div>
                            <div class="text-success small">
                                <i class="fas fa-arrow-up"></i> 100% vs período anterior
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
            <div class="card border-left-success shadow h-100 py-2 card-hover">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Receita (30 dias)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                R$ {{ number_format($monthlyRevenue ?? 0, 2, ',', '.') }}
                            </div>
                            <div class="text-success small">
                                <i class="fas fa-arrow-up"></i> 100% vs período anterior
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
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
                                Pedidos (30 dias)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $monthlyOrders ?? 0 }}
                            </div>
                            <div class="text-info small">
                                <i class="fas fa-arrow-up"></i> 100% vs período anterior
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
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
                                Cupons Usados
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $couponsUsed ?? 0 }}
                            </div>
                            <div class="text-warning small">
                                <i class="fas fa-minus"></i> 0% vs período anterior
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row mb-4">
        <!-- Vendas por Dia -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line me-2"></i>
                        Vendas por Dia - Últimos 30 dias
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                            <div class="dropdown-header">Ações do Gráfico:</div>
                            <a class="dropdown-item" href="#" onclick="downloadChart('salesChart')">
                                <i class="fas fa-download fa-sm fa-fw me-2 text-gray-400"></i>Download
                            </a>
                            <a class="dropdown-item" href="#" onclick="printChart('salesChart')">
                                <i class="fas fa-print fa-sm fa-fw me-2 text-gray-400"></i>Imprimir
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="salesChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Usuários por Tipo -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>
                        Usuários por Tipo
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="usersChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Receita Mensal -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>
                        Receita Mensal - Últimos 12 Meses
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                            <a class="dropdown-item" href="#" onclick="downloadChart('monthlyRevenueChart')">
                                <i class="fas fa-download fa-sm fa-fw me-2"></i>Download
                            </a>
                            <a class="dropdown-item" href="#" onclick="printChart('monthlyRevenueChart')">
                                <i class="fas fa-print fa-sm fa-fw me-2"></i>Imprimir
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="monthlyRevenueChart" style="height: 400px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabelas de Dados -->
    <div class="row">
        <!-- Top 10 Produtos -->
        <div class="col-xl-4 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-trophy me-2"></i>
                        Top 10 Produtos
                    </h6>
                </div>
                <div class="card-body">
                    @if(isset($topProducts) && count($topProducts) > 0)
                        @foreach($topProducts as $product)
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span class="badge bg-primary rounded-pill">#{{ $loop->iteration }}</span>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold">{{ $product->name ?? 'Produto' }}</div>
                                        <small class="text-muted">{{ $product->total_sold ?? 0 }} vendas</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="text-success font-weight-bold">
                                        R$ {{ number_format($product->total_revenue ?? 0, 2, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-box fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">Nenhum produto vendido ainda</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Top Pet Shops -->
        <div class="col-xl-4 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-store me-2"></i>
                        Top Pet Shops
                    </h6>
                </div>
                <div class="card-body">
                    @if(isset($topPetshops) && count($topPetshops) > 0)
                        @foreach($topPetshops as $petshop)
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span class="badge bg-success rounded-pill">#{{ $loop->iteration }}</span>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold">{{ $petshop->name ?? 'Pet Shop' }}</div>
                                        <small class="text-muted">{{ $petshop->total_sales ?? 0 }} vendas</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="text-success font-weight-bold">
                                        R$ {{ number_format($petshop->revenue ?? 0, 2, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-store fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">Dados não disponíveis</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Pedidos Recentes -->
        <div class="col-xl-4 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clipboard-list me-2"></i>
                        Pedidos Recentes
                    </h6>
                    <a href="#" class="btn btn-sm btn-primary">
                        Ver Todos
                    </a>
                </div>
                <div class="card-body">
                    @if(isset($recentOrders) && count($recentOrders) > 0)
                        @foreach($recentOrders->take(5) as $order)
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div>
                                    <div class="font-weight-bold">#{{ $order->id ?? 'N/A' }}</div>
                                    <small class="text-muted">
                                        {{ $order->user->name ?? 'Cliente' }}
                                    </small>
                                    <br>
                                    @php
                                        $statusColor = match($order->status ?? 'pending') {
                                            'pending' => 'warning',
                                            'paid' => 'info',
                                            'shipped' => 'primary',
                                            'delivered' => 'success',
                                            'cancelled' => 'danger',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }}">
                                        {{ ucfirst($order->status ?? 'Pendente') }}
                                    </span>
                                </div>
                                <div class="text-end">
                                    <div class="font-weight-bold text-success">
                                        R$ {{ number_format($order->total_amount ?? 0, 2, ',', '.') }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $order->created_at ? $order->created_at->format('d/m/Y') : 'N/A' }}
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-cart fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">Nenhum pedido recente</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.border-left-primary { border-left: 0.25rem solid #4e73df !important; }
.border-left-success { border-left: 0.25rem solid #1cc88a !important; }
.border-left-info { border-left: 0.25rem solid #36b9cc !important; }
.border-left-warning { border-left: 0.25rem solid #f6c23e !important; }

.text-gray-800 { color: #5a5c69 !important; }
.text-gray-300 { color: #dddfeb !important; }

.chart-container {
    position: relative;
    width: 100%;
    min-height: 300px;
}

.card-hover {
    transition: all 0.3s;
}

.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
}

.d-flex.gap-2 > * + * {
    margin-left: 0.5rem;
}

/* Animações */
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

.dropdown-menu {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border: none;
}

.badge {
    font-size: 0.8rem;
}

.chart-container canvas {
    max-height: 400px;
}

/* Loading states */
.chart-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 300px;
    background: #f8f9fa;
    border-radius: 0.375rem;
}

.chart-loading .spinner-border {
    color: #4e73df;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuração padrão dos gráficos
    Chart.defaults.font.family = '"Nunito", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
    Chart.defaults.color = '#858796';

    // Dados dos gráficos (vindos do controller)
    const salesData = @json($salesChart ?? []);
    const usersData = @json($usersChart ?? []);
    const monthlyData = @json($monthlyChart ?? []);

    // Verificar se temos dados válidos para vendas
    if (salesData && salesData.length > 0) {
        // Gráfico de Vendas por Dia
        const salesCtx = document.getElementById('salesChart');
        if (salesCtx) {
            new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: salesData.map(item => item.day || item.date),
                    datasets: [{
                        label: 'Receita (R$)',
                        data: salesData.map(item => item.revenue || 0),
                        borderColor: '#4e73df',
                        backgroundColor: 'rgba(78, 115, 223, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#4e73df',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        yAxisID: 'y'
                    }, {
                        label: 'Pedidos',
                        data: salesData.map(item => item.orders || 0),
                        borderColor: '#1cc88a',
                        backgroundColor: 'rgba(28, 200, 138, 0.1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4,
                        pointBackgroundColor: '#1cc88a',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    if (context.datasetIndex === 0) {
                                        return 'Receita: R$ ' + context.parsed.y.toLocaleString('pt-BR', {
                                            minimumFractionDigits: 2
                                        });
                                    } else {
                                        return 'Pedidos: ' + context.parsed.y;
                                    }
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Receita (R$)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return 'R$ ' + value.toLocaleString('pt-BR');
                                }
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.1)'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Pedidos'
                            },
                            grid: {
                                drawOnChartArea: false,
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeOutBounce'
                    }
                }
            });
        }
    } else {
        // Mostrar placeholder quando não há dados
        const salesCanvas = document.getElementById('salesChart');
        if (salesCanvas) {
            salesCanvas.parentElement.innerHTML = `
                <div class="chart-loading">
                    <div class="text-center">
                        <i class="fas fa-chart-line fa-3x text-gray-300 mb-3"></i>
                        <p class="text-muted">Nenhum dado de vendas disponível</p>
                    </div>
                </div>
            `;
        }
    }

    // Verificar se temos dados válidos para usuários
    if (usersData && usersData.length > 0) {
        // Gráfico de Usuários por Tipo
        const usersCtx = document.getElementById('usersChart');
        if (usersCtx) {
            new Chart(usersCtx, {
                type: 'doughnut',
                data: {
                    labels: usersData.map(item => item.type),
                    datasets: [{
                        data: usersData.map(item => item.count),
                        backgroundColor: usersData.map(item => item.color),
                        borderWidth: 2,
                        borderColor: '#fff',
                        hoverBorderWidth: 4,
                        hoverBorderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                                    return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                }
                            }
                        }
                    },
                    cutout: '50%',
                    animation: {
                        animateRotate: true,
                        duration: 1000
                    }
                }
            });
        }
    } else {
        // Mostrar placeholder quando não há dados
        const usersCanvas = document.getElementById('usersChart');
        if (usersCanvas) {
            usersCanvas.parentElement.innerHTML = `
                <div class="chart-loading">
                    <div class="text-center">
                        <i class="fas fa-chart-pie fa-3x text-gray-300 mb-3"></i>
                        <p class="text-muted">Nenhum dado de usuários disponível</p>
                    </div>
                </div>
            `;
        }
    }

    // Verificar se temos dados válidos para dados mensais
    if (monthlyData && monthlyData.length > 0) {
        // Gráfico de Receita Mensal
        const monthlyCtx = document.getElementById('monthlyRevenueChart');
        if (monthlyCtx) {
            new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: monthlyData.map(item => item.month),
                    datasets: [{
                        label: 'Receita (R$)',
                        data: monthlyData.map(item => item.revenue || 0),
                        backgroundColor: 'rgba(78, 115, 223, 0.8)',
                        borderColor: '#4e73df',
                        borderWidth: 1,
                        borderRadius: 4,
                        borderSkipped: false,
                        yAxisID: 'y'
                    }, {
                        label: 'Pedidos',
                        data: monthlyData.map(item => item.orders || 0),
                        type: 'line',
                        borderColor: '#1cc88a',
                        backgroundColor: 'rgba(28, 200, 138, 0.1)',
                        borderWidth: 3,
                        fill: false,
                        tension: 0.4,
                        pointBackgroundColor: '#1cc88a',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    if (context.datasetIndex === 0) {
                                        return 'Receita: R$ ' + context.parsed.y.toLocaleString('pt-BR', {
                                            minimumFractionDigits: 2
                                        });
                                    } else {
                                        return 'Pedidos: ' + context.parsed.y;
                                    }
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Receita (R$)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return 'R$ ' + value.toLocaleString('pt-BR');
                                }
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.1)'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Pedidos'
                            },
                            grid: {
                                drawOnChartArea: false,
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeOutBounce'
                    }
                }
            });
        }
    } else {
        // Mostrar placeholder quando não há dados
        const monthlyCanvas = document.getElementById('monthlyRevenueChart');
        if (monthlyCanvas) {
            monthlyCanvas.parentElement.innerHTML = `
                <div class="chart-loading">
                    <div class="text-center">
                        <i class="fas fa-chart-bar fa-3x text-gray-300 mb-3"></i>
                        <p class="text-muted">Nenhum dado mensal disponível</p>
                    </div>
                </div>
            `;
        }
    }

    // Funções de utilidade
    window.downloadChart = function(chartId) {
        const canvas = document.getElementById(chartId);
        if (canvas) {
            const url = canvas.toDataURL('image/png');
            const link = document.createElement('a');
            link.download = chartId + '_' + new Date().toISOString().slice(0,10) + '.png';
            link.href = url;
            link.click();
        }
    };

    window.printChart = function(chartId) {
        const canvas = document.getElementById(chartId);
        if (canvas) {
            const dataURL = canvas.toDataURL('image/png');
            
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Gráfico - Dashboard Administrativo</title>
                        <style>
                            body { margin: 0; padding: 20px; text-align: center; }
                            img { max-width: 100%; height: auto; }
                            .header { margin-bottom: 20px; font-family: Arial, sans-serif; }
                        </style>
                    </head>
                    <body>
                        <div class="header">
                            <h1>Dashboard Administrativo</h1>
                            <p>Gráfico gerado em ${new Date().toLocaleDateString('pt-BR')}</p>
                        </div>
                        <img src="${dataURL}" alt="Gráfico">
                    </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        }
    };

    // Animações de entrada para cards
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });

    // Auto-refresh dos dados a cada 5 minutos
    setInterval(() => {
        const refreshBtn = document.querySelector('.btn-success');
        if (refreshBtn) {
            refreshBtn.innerHTML = '🔄 Atualizando...';
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    }, 300000); // 5 minutos

    // Loading states para gráficos
    function showChartLoading(canvasId) {
        const canvas = document.getElementById(canvasId);
        if (canvas) {
            const container = canvas.parentElement;
            container.innerHTML = `
                <div class="chart-loading">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <span class="ms-2">Carregando gráfico...</span>
                </div>
            `;
        }
    }

    // Tooltips personalizados
    const tooltipElements = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        tooltipElements.forEach(el => new bootstrap.Tooltip(el));
    }
});
</script>
@endpush