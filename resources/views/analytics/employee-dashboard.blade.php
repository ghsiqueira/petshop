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
                <div class="text-end">
                    <div class="badge bg-primary fs-6">
                        ⭐ {{ number_format($avgRating, 1) }} avaliação média
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de Métricas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
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
            <div class="card border-left-success shadow h-100 py-2">
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
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Taxa de Conclusão
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $monthlyStats['total_appointments'] > 0 ? number_format(($monthlyStats['completed_appointments'] / $monthlyStats['total_appointments']) * 100, 1) : 0 }}%
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
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Agendamentos Hoje
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $todayAppointments->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico + Agendamentos de Hoje -->
    <div class="row mb-4">
        <!-- Gráfico da Semana -->
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">📊 Agendamentos da Semana</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="weeklyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Agendamentos de Hoje -->
        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">🗓️ Agenda de Hoje</h6>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @forelse($todayAppointments as $appointment)
                    <div class="d-flex align-items-center mb-3 p-2 border-left-{{ $appointment->status == 'completed' ? 'success' : ($appointment->status == 'confirmed' ? 'info' : 'warning') }} bg-light rounded">
                        <div class="flex-shrink-0">
                            <div class="bg-{{ $appointment->status == 'completed' ? 'success' : ($appointment->status == 'confirmed' ? 'info' : 'warning') }} rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-{{ $appointment->status == 'completed' ? 'check' : ($appointment->status == 'confirmed' ? 'clock' : 'calendar') }} text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fw-bold">{{ $appointment->appointment_datetime->format('H:i') }}</div>
                            <div class="text-primary fw-bold">{{ $appointment->service->name }}</div>
                            <small class="text-muted">
                                {{ $appointment->pet->name }} ({{ $appointment->user->name }})
                            </small>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="badge bg-{{ $appointment->status == 'completed' ? 'success' : ($appointment->status == 'confirmed' ? 'info' : 'warning') }}">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-check fa-3x text-gray-300 mb-3"></i>
                        <p class="text-muted">Nenhum agendamento para hoje</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Tabelas -->
    <div class="row">
        <!-- Próximos Agendamentos -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">📅 Próximos Agendamentos</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Data/Hora</th>
                                    <th>Serviço</th>
                                    <th>Pet</th>
                                    <th>Cliente</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($upcomingAppointments as $appointment)
                                <tr>
                                    <td>
                                        <div>
                                            <div class="fw-bold">{{ $appointment->appointment_datetime->format('d/m/Y') }}</div>
                                            <small class="text-muted">{{ $appointment->appointment_datetime->format('H:i') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-bold">{{ $appointment->service->name }}</div>
                                            <small class="text-muted">{{ $appointment->service->duration_minutes }}min</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                <i class="fas fa-paw text-white"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $appointment->pet->name }}</div>
                                                <small class="text-muted">{{ $appointment->pet->species }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $appointment->user->name }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $appointment->status == 'confirmed' ? 'info' : ($appointment->status == 'pending' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        Nenhum agendamento próximo
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Serviços Mais Realizados -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">🏆 Meus Serviços Top</h6>
                </div>
                <div class="card-body">
                    @forelse($topServices as $index => $service)
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <span class="badge bg-{{ $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : 'primary') }} fs-6">
                                {{ $index + 1 }}
                            </span>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fw-bold">{{ $service->service->name }}</div>
                            <small class="text-muted">{{ $service->count }} realizados este mês</small>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="progress" style="width: 60px; height: 8px;">
                                <div class="progress-bar bg-{{ $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : 'primary') }}" 
                                     style="width: {{ ($service->count / $topServices->first()->count) * 100 }}%"></div>
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

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
// Dados do gráfico semanal
const weeklyData = @json($weeklyChart);

// Gráfico de barras da semana
const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
const weeklyChart = new Chart(weeklyCtx, {
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
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            },
            x: {
                ticks: {
                    maxRotation: 0
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
    location.reload();
}, 300000); // 5 minutos
</script>

<style>
.border-left-primary { border-left: 0.25rem solid #4e73df !important; }
.border-left-success { border-left: 0.25rem solid #1cc88a !important; }
.border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
.border-left-info { border-left: 0.25rem solid #36b9cc !important; }

.chart-area {
    position: relative;
    height: 300px;
}

.text-gray-800 { color: #5a5c69 !important; }
.text-gray-300 { color: #dddfeb !important; }

.card:hover {
    transform: translateY(-2px);
    transition: all 0.3s;
}

@keyframes slideIn {
    from { transform: translateX(-10px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

.list-group-item {
    animation: slideIn 0.3s ease-out;
}
</style>
@endsection