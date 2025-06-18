@extends('layouts.app')

@section('title', 'Dashboard do Funcionário')

@section('content')
<div class="container">
    <h1 class="mb-4">Dashboard do Funcionário</h1>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Agendamentos de Hoje</h5>
        </div>
        <div class="card-body p-0">
            @if($todayAppointments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Horário</th>
                                <th>Cliente</th>
                                <th>Pet</th>
                                <th>Serviço</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($todayAppointments as $appointment)
                                <tr>
                                    <td>{{ $appointment->appointment_datetime->format('H:i') }}</td>
                                    <td>{{ $appointment->user->name }}</td>
                                    <td>{{ $appointment->pet->name }} ({{ ucfirst($appointment->pet->species) }})</td>
                                    <td>{{ $appointment->service->name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $appointment->status == 'pending' ? 'warning' : ($appointment->status == 'confirmed' ? 'success' : ($appointment->status == 'completed' ? 'primary' : 'danger')) }}">
                                            {{ $appointment->status == 'pending' ? 'Pendente' : ($appointment->status == 'confirmed' ? 'Confirmado' : ($appointment->status == 'completed' ? 'Concluído' : 'Cancelado')) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($appointment->status == 'pending')
                                            <form action="{{ route('appointments.updateStatus', $appointment->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="confirmed">
                                                <button type="submit" class="btn btn-sm btn-success">Confirmar</button>
                                            </form>
                                        @elseif($appointment->status == 'confirmed')
                                            <form action="{{ route('appointments.updateStatus', $appointment->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="btn btn-sm btn-primary">Concluir</button>
                                            </form>
                                        @endif
                                        
                                        @if($appointment->status != 'completed' && $appointment->status != 'cancelled')
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelAppointmentModal{{ $appointment->id }}">
                                                Cancelar
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                
                                <!-- Modal de cancelamento -->
                                <div class="modal fade" id="cancelAppointmentModal{{ $appointment->id }}" tabindex="-1" aria-labelledby="cancelAppointmentModalLabel{{ $appointment->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="cancelAppointmentModalLabel{{ $appointment->id }}">Confirmar Cancelamento</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Tem certeza que deseja cancelar este agendamento?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Não</button>
                                                <form action="{{ route('appointments.updateStatus', $appointment->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="cancelled">
                                                    <button type="submit" class="btn btn-danger">Sim, Cancelar</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
            <div class="text-center p-4">
                    <p class="mb-0">Nenhum agendamento para hoje.</p>
                </div>
            @endif
        </div>
    </div>
    
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Próximos Agendamentos</h5>
        </div>
        <div class="card-body p-0">
            @if($upcomingAppointments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Data</th>
                                <th>Horário</th>
                                <th>Cliente</th>
                                <th>Pet</th>
                                <th>Serviço</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($upcomingAppointments as $appointment)
                                <tr>
                                    <td>{{ $appointment->appointment_datetime->format('d/m/Y') }}</td>
                                    <td>{{ $appointment->appointment_datetime->format('H:i') }}</td>
                                    <td>{{ $appointment->user->name }}</td>
                                    <td>{{ $appointment->pet->name }} ({{ ucfirst($appointment->pet->species) }})</td>
                                    <td>{{ $appointment->service->name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $appointment->status == 'pending' ? 'warning' : ($appointment->status == 'confirmed' ? 'success' : 'danger') }}">
                                            {{ $appointment->status == 'pending' ? 'Pendente' : ($appointment->status == 'confirmed' ? 'Confirmado' : 'Cancelado') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($appointment->status == 'pending')
                                            <form action="{{ route('appointments.updateStatus', $appointment->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="confirmed">
                                                <button type="submit" class="btn btn-sm btn-success">Confirmar</button>
                                            </form>
                                        @endif
                                        
                                        @if($appointment->status != 'cancelled')
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelUpcomingAppointmentModal{{ $appointment->id }}">
                                                Cancelar
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                
                                <!-- Modal de cancelamento -->
                                <div class="modal fade" id="cancelUpcomingAppointmentModal{{ $appointment->id }}" tabindex="-1" aria-labelledby="cancelUpcomingAppointmentModalLabel{{ $appointment->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="cancelUpcomingAppointmentModalLabel{{ $appointment->id }}">Confirmar Cancelamento</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Tem certeza que deseja cancelar este agendamento?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Não</button>
                                                <form action="{{ route('appointments.updateStatus', $appointment->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="cancelled">
                                                    <button type="submit" class="btn btn-danger">Sim, Cancelar</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center p-4">
                    <p class="mb-0">Nenhum agendamento futuro.</p>
                </div>
            @endif
        </div>
        <div class="card-footer">
            <a href="{{ route('employee.appointments') }}" class="btn btn-outline-primary btn-sm">Ver Todos os Agendamentos</a>
        </div>
    </div>
</div>
@endsection