@extends('layouts.app')

@section('title', 'Meus Agendamentos')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">📅 Meus Agendamentos</h1>
                    <p class="text-muted">{{ auth()->user()->name }} - Gerenciar agendamentos</p>
                </div>
                <div>
                    <a href="{{ route('analytics.employee') }}" class="btn btn-outline-primary">
                        <i class="fas fa-chart-line me-1"></i>Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('employee.appointments') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendente</option>
                                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmado</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Concluído</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="date_from" class="form-label">Data Inicial</label>
                                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="date_to" class="form-label">Data Final</label>
                                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i>Filtrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Estatísticas Rápidas -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-left-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Hoje
                            </div>
                            <div class="h5 mb-0 font-weight-bold">
                                {{ $appointments->where('appointment_datetime', '>=', now()->startOfDay())->where('appointment_datetime', '<=', now()->endOfDay())->count() }}
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-left-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Concluídos
                            </div>
                            <div class="h5 mb-0 font-weight-bold">
                                {{ $appointments->where('status', 'completed')->count() }}
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-left-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pendentes
                            </div>
                            <div class="h5 mb-0 font-weight-bold">
                                {{ $appointments->where('status', 'pending')->count() }}
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-left-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Próximos 7 dias
                            </div>
                            <div class="h5 mb-0 font-weight-bold">
                                {{ $appointments->where('appointment_datetime', '>=', now())->where('appointment_datetime', '<=', now()->addDays(7))->count() }}
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-calendar-week fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Agendamentos -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Lista de Agendamentos
                        <span class="badge bg-primary ms-2">{{ $appointments->count() }} total</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($appointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Data/Hora</th>
                                        <th>Cliente</th>
                                        <th>Pet</th>
                                        <th>Serviço</th>
                                        <th class="text-center">Status</th>
                                        <th>Observações</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($appointments as $appointment)
                                    <tr class="border-left-{{ $appointment->status == 'completed' ? 'success' : ($appointment->status == 'confirmed' ? 'info' : ($appointment->status == 'pending' ? 'warning' : 'danger')) }}">
                                        <td>
                                            <div>
                                                <div class="fw-bold">{{ $appointment->appointment_datetime->format('d/m/Y') }}</div>
                                                <small class="text-muted">{{ $appointment->appointment_datetime->format('H:i') }}</small>
                                                @if($appointment->appointment_datetime->isToday())
                                                    <span class="badge bg-primary ms-1">Hoje</span>
                                                @elseif($appointment->appointment_datetime->isTomorrow())
                                                    <span class="badge bg-info ms-1">Amanhã</span>
                                                @elseif($appointment->appointment_datetime->isPast())
                                                    <span class="badge bg-secondary ms-1">Passado</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $appointment->user->name }}</div>
                                                    <small class="text-muted">{{ $appointment->user->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                    <i class="fas fa-paw text-white"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $appointment->pet->name }}</div>
                                                    <small class="text-muted">{{ $appointment->pet->species }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <div class="fw-bold">{{ $appointment->service->name }}</div>
                                                <small class="text-muted">
                                                    {{ $appointment->service->duration_minutes }}min - 
                                                    R$ {{ number_format($appointment->service->price, 2, ',', '.') }}
                                                </small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $appointment->status == 'completed' ? 'success' : ($appointment->status == 'confirmed' ? 'info' : ($appointment->status == 'pending' ? 'warning' : 'danger')) }}">
                                                {{ $appointment->status == 'completed' ? 'Concluído' : ($appointment->status == 'confirmed' ? 'Confirmado' : ($appointment->status == 'pending' ? 'Pendente' : 'Cancelado')) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($appointment->notes)
                                                <span class="text-muted" data-bs-toggle="tooltip" title="{{ $appointment->notes }}">
                                                    {{ Str::limit($appointment->notes, 30) }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                @if(in_array($appointment->status, ['pending', 'confirmed']) && !$appointment->appointment_datetime->isPast())
                                                    <button type="button" class="btn btn-outline-success" onclick="updateStatus({{ $appointment->id }}, 'completed')" title="Marcar como Concluído">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif
                                                
                                                @if($appointment->status == 'pending')
                                                    <button type="button" class="btn btn-outline-info" onclick="updateStatus({{ $appointment->id }}, 'confirmed')" title="Confirmar">
                                                        <i class="fas fa-calendar-check"></i>
                                                    </button>
                                                @endif
                                                
                                                @if(in_array($appointment->status, ['pending', 'confirmed']))
                                                    <button type="button" class="btn btn-outline-danger" onclick="updateStatus({{ $appointment->id }}, 'cancelled')" title="Cancelar">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Paginação -->
                        <div class="card-footer">
                            {{ $appointments->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-muted">Nenhum agendamento encontrado</h5>
                            <p class="text-muted">Não há agendamentos com os filtros selecionados.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para atualizar status -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Ação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja alterar o status deste agendamento?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmStatusUpdate">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<!-- Forms para atualização de status -->
<form id="statusForm" method="POST" style="display: none;">
    @csrf
    @method('PUT')
    <input type="hidden" name="status" id="newStatus">
</form>

<script>
let currentAppointmentId = null;
let currentStatus = null;

function updateStatus(appointmentId, status) {
    currentAppointmentId = appointmentId;
    currentStatus = status;
    
    const modal = new bootstrap.Modal(document.getElementById('statusModal'));
    modal.show();
}

document.getElementById('confirmStatusUpdate').addEventListener('click', function() {
    if (currentAppointmentId && currentStatus) {
        const form = document.getElementById('statusForm');
        form.action = `/appointments/${currentAppointmentId}/status`;
        document.getElementById('newStatus').value = currentStatus;
        form.submit();
    }
});

// Auto-atualizar página a cada 5 minutos
setTimeout(function() {
    location.reload();
}, 300000);
</script>

<style>
.border-left-success { border-left: 4px solid #1cc88a !important; }
.border-left-primary { border-left: 4px solid #4e73df !important; }
.border-left-warning { border-left: 4px solid #f6c23e !important; }
.border-left-info { border-left: 4px solid #36b9cc !important; }
.border-left-danger { border-left: 4px solid #e74a3b !important; }

.text-gray-300 { color: #dddfeb !important; }

.card:hover {
    transition: all 0.3s;
}
</style>
@endsection