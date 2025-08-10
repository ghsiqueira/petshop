@extends('layouts.app')

@section('title', 'Dashboard Funcionário - ' . $employee->user->name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">👨‍💼 Dashboard Funcionário</h1>
                    <p class="text-muted">{{ $employee->user->name }} - {{ ucfirst($employee->position) }}</p>
                </div>
                <div class="d-flex gap-2">
                    {{-- Nota: Employee export pode ser adicionado futuramente --}}
                    {{-- @include('components.export-buttons', ['type' => 'employee']) --}}
                    <div class="badge bg-primary fs-6">
                        ⭐ {{ number_format($avgRating, 1) }} avaliação média
                    </div>
                    <a href="{{ route('employee.appointments') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list me-1"></i>Ver Todos Agendamentos
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de Métricas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 card-hover">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Agendamentos do Mês
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $monthlyStats['total_appointments'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
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
                                Serviços Concluídos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $monthlyStats['completed_appointments'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                Taxa de Conclusão
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $monthlyStats['total_appointments'] > 0 ? 
                                   number_format(($monthlyStats['completed_appointments'] / $monthlyStats['total_appointments']) * 100, 1) : 0 }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
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
                                Cancelamentos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $monthlyStats['cancelled_appointments'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico e Agendamentos de Hoje -->
    <div class="row mb-4">
        <!-- Gráfico Semanal -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">📊 Agendamentos da Semana</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="weeklyChart" width="100%" height="50"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Agendamentos de Hoje -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">📅 Agendamentos de Hoje</h6>
                    <span class="badge bg-info">{{ $todayAppointments->count() }}</span>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @forelse($todayAppointments as $appointment)
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                            <div class="me-3">
                                <div class="bg-{{ 
                                    $appointment->status == 'completed' ? 'success' : 
                                    ($appointment->status == 'confirmed' ? 'info' : 'warning') 
                                }} text-white rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px;">
                                    <i class="fas fa-paw"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="font-weight-bold">{{ $appointment->pet->name }}</div>
                                <div class="text-muted small">{{ $appointment->service->name }}</div>
                                <div class="text-primary small">
                                    {{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('H:i') }}
                                </div>
                                <span class="badge bg-{{ 
                                    $appointment->status == 'completed' ? 'success' : 
                                    ($appointment->status == 'confirmed' ? 'info' : 'warning') 
                                }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-calendar fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">Nenhum agendamento para hoje</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Próximos Agendamentos e Top Serviços -->
    <div class="row">
        <!-- Próximos Agendamentos -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">🔜 Próximos Agendamentos</h6>
                    <a href="{{ route('employee.appointments') }}" class="btn btn-sm btn-outline-primary">
                        Ver Todos
                    </a>
                </div>
                <div class="card-body">
                    @forelse($upcomingAppointments as $appointment)
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="flex-grow-1">
                                <div class="font-weight-bold">{{ $appointment->pet->name }}</div>
                                <div class="text-muted small">{{ $appointment->service->name }}</div>
                                <div class="text-muted small">Cliente: {{ $appointment->user->name }}</div>
                                <div class="text-primary small">
                                    {{ \Carbon\Carbon::parse($appointment->appointment_datetime)->format('d/m/Y H:i') }}
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-{{ 
                                    $appointment->status == 'confirmed' ? 'success' : 'warning' 
                                }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                                <br>
                                <small class="text-muted">
                                    R$ {{ number_format($appointment->service->price, 2, ',', '.') }}
                                </small>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-clock fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">Nenhum agendamento próximo</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Top Serviços -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">⭐ Meus Top Serviços (Este Mês)</h6>
                </div>
                <div class="card-body">
                    @forelse($topServices as $index => $service)
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <span class="badge bg-{{ 
                                    $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : 'primary') 
                                }} rounded-pill">
                                    #{{ $index + 1 }}
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="font-weight-bold">{{ $service->service->name }}</div>
                                <small class="text-muted">
                                    {{ $service->count }} {{ $service->count == 1 ? 'agendamento' : 'agendamentos' }}
                                </small>
                                <div class="progress mt-1" style="height: 6px;">
                                    <div class="progress-bar bg-{{ 
                                         $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : 'primary') 
                                     }}" 
                                     style="width: {{ $topServices->count() > 0 && $topServices->first()->count > 0 ? ($service->count / $topServices->first()->count) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="fas fa-clipboard-list fa-3x text-gray-300 mb-3"></i>
                        <p class="text-muted">Nenhum serviço realizado este mês</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.border-left-primary { border-left: 0.25rem solid #4e73df !important; }
.border-left-success { border-left: 0.25rem solid #1cc88a !important; }
.border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
.border-left-info { border-left: 0.25rem solid #36b9cc !important; }

.chart-area {
    position: relative;
    height: 400px; /* Aumentado para ser consistente */
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

@keyframes slideIn {
    from { 
        transform: translateX(-10px); 
        opacity: 0; 
    }
    to { 
        transform: translateX(0); 
        opacity: 1; 
    }
}

.list-group-item {
    animation: slideIn 0.3s ease-out;
}

/* Gap utility for older Bootstrap versions */
.d-flex.gap-2 > * + * {
    margin-left: 0.5rem;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dados do gráfico semanal
    const weeklyData = @json($weeklyChart);

    // Verificar se temos dados válidos
    if (!weeklyData || weeklyData.length === 0) {
        document.getElementById('weeklyChart').parentElement.innerHTML = 
            '<div class="d-flex align-items-center justify-content-center h-100">' +
            '<div class="text-center">' +
            '<i class="fas fa-chart-bar fa-3x text-gray-300 mb-3"></i>' +
            '<p class="text-muted">Nenhum agendamento nesta semana</p>' +
            '</div></div>';
        return;
    }

    // Gráfico de barras da semana
    const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
    new Chart(weeklyCtx, {
        type: 'bar',
        data: {
            labels: weeklyData.map(item => item.day + '\n' + item.date),
            datasets: [{
                label: 'Agendamentos',
                data: weeklyData.map(item => item.count),
                backgroundColor: 'rgba(78, 115, 223, 0.8)',
                borderColor: '#4e73df',
                borderWidth: 1,
                borderRadius: 4
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
                            return 'Agendamentos: ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    },
                    title: {
                        display: true,
                        text: 'Quantidade'
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 0
                    },
                    title: {
                        display: true,
                        text: 'Dia da Semana'
                    }
                }
            },
            elements: {
                bar: {
                    borderRadius: 4
                }
            }
        }
    });

    // Atualização automática a cada 5 minutos
    setInterval(() => {
        if (confirm('Atualizar dashboard com dados mais recentes?')) {
            location.reload();
        }
    }, 300000); // 5 minutos
});
</script>
@endpush
@endsection