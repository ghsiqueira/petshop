@extends('layouts.app')

@section('title', 'Dashboard Administrativo')

@section('styles')
<style>
.dashboard-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.dashboard-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.chart-container {
    position: relative;
    height: 400px;
}

.small-chart-container {
    position: relative;
    height: 300px;
}

.stats-icon {
    font-size: 2.5rem;
    opacity: 0.7;
}

.growth-indicator {
    font-size: 0.875rem;
    font-weight: 600;
}

.growth-positive {
    color: #28a745;
}

.growth-negative {
    color: #dc3545;
}

.loading-spinner {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 200px;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header com filtros -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0">
                <i class="fas fa-chart-line me-2 text-primary"></i>
                Dashboard Administrativo
            </h1>
            <p class="text-muted mb-0">Visão geral do sistema - Últimos {{ $period }} dias</p>
        </div>
        
        <div class="d-flex gap-2">
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-calendar me-2"></i>Período
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item {{ $period == '7' ? 'active' : '' }}" href="?period=7">Últimos 7 dias</a></li>
                    <li><a class="dropdown-item {{ $period == '30' ? 'active' : '' }}" href="?period=30">Últimos 30 dias</a></li>
                    <li><a class="dropdown-item {{ $period == '90' ? 'active' : '' }}" href="?period=90">Últimos 90 dias</a></li>
                    <li><a class="dropdown-item {{ $period == '365' ? 'active' : '' }}" href="?period=365">Último ano</a></li>
                </ul>
            </div>
            
            <button class="btn btn-outline-secondary" onclick="location.reload()">
                <i class="fas fa-sync-alt me-2"></i>Atualizar
            </button>
        </div>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="row mb-4">
        <!-- Total de Usuários -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card dashboard-card border-left-primary h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total de Usuários
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalUsers) }}</div>
                            <div class="growth-indicator {{ $newUsersGrowth >= 0 ? 'growth-positive' : 'growth-negative' }}">
                                <i class="fas fa-arrow-{{ $newUsersGrowth >= 0 ? 'up' : 'down' }} me-1"></i>
                                {{ abs($newUsersGrowth) }}% vs período anterior
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users stats-icon text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Receita Total -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card dashboard-card border-left-success h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Receita ({{ $period }} dias)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                R$ {{ number_format($periodRevenue, 2, ',', '.') }}
                            </div>
                            <div class="growth-indicator {{ $revenueGrowth >= 0 ? 'growth-positive' : 'growth-negative' }}">
                                <i class="fas fa-arrow-{{ $revenueGrowth >= 0 ? 'up' : 'down' }} me-1"></i>
                                {{ abs($revenueGrowth) }}% vs período anterior
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign stats-icon text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total de Pedidos -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card dashboard-card border-left-info h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Pedidos ({{ $period }} dias)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($periodOrders) }}</div>
                            <div class="growth-indicator {{ $ordersGrowth >= 0 ? 'growth-positive' : 'growth-negative' }}">
                                <i class="fas fa-arrow-{{ $ordersGrowth >= 0 ? 'up' : 'down' }} me-1"></i>
                                {{ abs($ordersGrowth) }}% vs período anterior
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart stats-icon text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cupons Utilizados -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card dashboard-card border-left-warning h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Cupons Usados
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($couponsUsed) }}</div>
                            <div class="growth-indicator {{ $couponsGrowth >= 0 ? 'growth-positive' : 'growth-negative' }}">
                                <i class="fas fa-arrow-{{ $couponsGrowth >= 0 ? 'up' : 'down' }} me-1"></i>
                                {{ abs($couponsGrowth) }}% vs período anterior
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags stats-icon text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos Principais -->
    <div class="row mb-4">
        <!-- Vendas por Dia -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-area me-2"></i>
                        Vendas por Dia - Últimos {{ $period }} dias
                    </h6>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="downloadChart('salesChart')"><i class="fas fa-download me-2"></i>Download PNG</a></li>
                            <li><a class="dropdown-item" href="#" onclick="printChart('salesChart')"><i class="fas fa-print me-2"></i>Imprimir</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="salesChart"></canvas>
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
                    <div class="small-chart-container">
                        <canvas id="usersChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Receita Mensal -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>
                        Receita Mensal - Últimos 12 Meses
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="monthlyRevenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabelas e Listas -->
    <div class="row">
        <!-- Produtos Mais Vendidos -->
        <div class="col-xl-4 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-trophy me-2"></i>
                        Top 10 Produtos
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th class="text-center">Vendidos</th>
                                    <th class="text-end">Receita</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts as $index => $product)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                @if($index < 3)
                                                    <i class="fas fa-medal text-{{ $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : 'danger') }}"></i>
                                                @else
                                                    <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ Str::limit($product['name'], 20) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $product['sold'] }}</td>
                                    <td class="text-end">R$ {{ number_format($product['revenue'], 2, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">
                                        Nenhum produto vendido ainda
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Pet Shop</th>
                                    <th class="text-end">Receita</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topPetshops as $index => $petshop)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                @if($index < 3)
                                                    <i class="fas fa-medal text-{{ $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : 'danger') }}"></i>
                                                @else
                                                    <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                                                @endif
                                            </div>
                                            <div>{{ Str::limit($petshop->name, 25) }}</div>
                                        </div>
                                    </td>
                                    <td class="text-end">R$ {{ number_format($petshop->total_revenue, 2, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-3">
                                        Nenhuma venda registrada
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pedidos Recentes -->
        <div class="col-xl-4 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clock me-2"></i>
                        Pedidos Recentes
                    </h6>
                </div>
                <div class="card-body">
                    @forelse($recentOrders as $order)
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-shopping-bag"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold">#{{ $order->id }} - {{ $order->user->name }}</div>
                            <div class="text-muted small">
                                R$ {{ number_format($order->total_amount, 2, ',', '.') }} • {{ $order->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <div>
                            <span class="badge bg-{{ $order->status == 'completed' ? 'success' : ($order->status == 'pending' ? 'warning' : 'info') }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-muted py-3">Nenhum pedido recente</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configurações globais do Chart.js
    Chart.defaults.font.family = '"Nunito", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
    Chart.defaults.color = '#858796';

    // Cores do tema
    const colors = {
        primary: '#4e73df',
        success: '#1cc88a',
        info: '#36b9cc',
        warning: '#f6c23e',
        danger: '#e74a3b'
    };

    // 1. Gráfico de Vendas por Dia
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: @json($salesByDay['labels']),
            datasets: [{
                label: 'Pedidos',
                data: @json($salesByDay['orders']),
                borderColor: colors.primary,
                backgroundColor: colors.primary + '20',
                borderWidth: 3,
                fill: true,
                tension: 0.3,
                yAxisID: 'y'
            }, {
                label: 'Receita (R$)',
                data: @json($salesByDay['revenue']),
                borderColor: colors.success,
                backgroundColor: colors.success + '20',
                borderWidth: 3,
                fill: true,
                tension: 0.3,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (context.datasetIndex === 1) {
                                return context.dataset.label + ': R$ ' + context.parsed.y.toLocaleString('pt-BR', {minimumFractionDigits: 2});
                            }
                            return context.dataset.label + ': ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Data'
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Pedidos'
                    },
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Receita (R$)'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                    ticks: {
                        callback: function(value) {
                            return 'R$ ' + value.toLocaleString('pt-BR');
                        }
                    }
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeInOutQuart'
            }
        }
    });

    // 2. Gráfico de Usuários por Tipo (Donut)
    const usersCtx = document.getElementById('usersChart').getContext('2d');
    new Chart(usersCtx, {
        type: 'doughnut',
        data: {
            labels: @json($usersByRole['labels']),
            datasets: [{
                data: @json($usersByRole['data']),
                backgroundColor: [
                    colors.danger,
                    colors.success,
                    colors.primary,
                    colors.warning
                ],
                borderColor: '#ffffff',
                borderWidth: 2,
                hoverBorderWidth: 3
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
                            const percentage = ((context.parsed * 100) / total).toFixed(1);
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            },
            cutout: '60%',
            animation: {
                animateRotate: true,
                duration: 1000
            }
        }
    });

    // 3. Gráfico de Receita Mensal
    const monthlyCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: @json($monthlyRevenue['labels']),
            datasets: [{
                label: 'Receita (R$)',
                data: @json($monthlyRevenue['revenue']),
                backgroundColor: colors.success + '80',
                borderColor: colors.success,
                borderWidth: 1,
                borderRadius: 4,
                borderSkipped: false,
            }, {
                label: 'Pedidos',
                data: @json($monthlyRevenue['orders']),
                backgroundColor: colors.info + '80',
                borderColor: colors.info,
                borderWidth: 1,
                borderRadius: 4,
                borderSkipped: false,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (context.datasetIndex === 0) {
                                return context.dataset.label + ': R$ ' + context.parsed.y.toLocaleString('pt-BR', {minimumFractionDigits: 2});
                            }
                            return context.dataset.label + ': ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Mês'
                    }
                },
                y: {
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
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeOutBounce'
            }
        }
    });

    // Funções de utilidade
    window.downloadChart = function(chartId) {
        const canvas = document.getElementById(chartId);
        const url = canvas.toDataURL('image/png');
        const link = document.createElement('a');
        link.download = chartId + '_' + new Date().toISOString().slice(0,10) + '.png';
        link.href = url;
        link.click();
    };

    window.printChart = function(chartId) {
        const canvas = document.getElementById(chartId);
        const dataURL = canvas.toDataURL('image/png');
        
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Gráfico - Dashboard</title>
                    <style>
                        body { margin: 0; padding: 20px; text-align: center; }
                        img { max-width: 100%; height: auto; }
                        .header { margin-bottom: 20px; font-family: Arial, sans-serif; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h2>Dashboard Administrativo</h2>
                        <p>Gerado em: ${new Date().toLocaleString('pt-BR')}</p>
                    </div>
                    <img src="${dataURL}" alt="Gráfico">
                </body>
            </html>
        `);
        
        printWindow.document.close();
        printWindow.focus();
        
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 250);
    };

    // Atualização automática a cada 5 minutos
    setInterval(function() {
        const now = new Date();
        const lastUpdate = document.querySelector('.text-muted');
        if (lastUpdate) {
            lastUpdate.innerHTML = lastUpdate.innerHTML.replace(/\d{2}:\d{2}/, now.toLocaleTimeString('pt-BR', {hour: '2-digit', minute:'2-digit'}));
        }
    }, 300000); // 5 minutos

    // Animação de entrada dos cards
    const cards = document.querySelectorAll('.dashboard-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});

// CSS adicional para bordas coloridas nos cards
const style = document.createElement('style');
style.textContent = `
    .border-left-primary {
        border-left: 4px solid #4e73df !important;
    }
    .border-left-success {
        border-left: 4px solid #1cc88a !important;
    }
    .border-left-info {
        border-left: 4px solid #36b9cc !important;
    }
    .border-left-warning {
        border-left: 4px solid #f6c23e !important;
    }
`;
document.head.appendChild(style);
</script>
@endsection