@extends('layouts.app')

@section('title', 'Meus Agendamentos')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Meus Agendamentos</h1>
        <a href="{{ route('appointments.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Novo Agendamento
        </a>
    </div>
    
    <div class="card mb-4">
        <div class="card-header bg-light">
            <ul class="nav nav-tabs card-header-tabs" id="appointmentTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button" role="tab" aria-controls="upcoming" aria-selected="true">Próximos</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="past-tab" data-bs-toggle="tab" data-bs-target="#past" type="button" role="tab" aria-controls="past" aria-selected="false">Anteriores</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="cancelled-tab" data-bs-toggle="tab" data-bs-target="#cancelled" type="button" role="tab" aria-controls="cancelled" aria-selected="false">Cancelados</button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="appointmentTabsContent">
                <!-- PRÓXIMOS AGENDAMENTOS -->
                <div class="tab-pane fade show active" id="upcoming" role="tabpanel" aria-labelledby="upcoming-tab">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Data e Hora</th>
                                    <th>Pet</th>
                                    <th>Serviço</th>
                                    <th>Pet Shop</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $activeUpcoming = $upcomingAppointments->where('status', '!=', 'cancelled');
                                @endphp
                                
                                @forelse($activeUpcoming as $appointment)
                                    <tr>
                                        <td>{{ $appointment->appointment_datetime->format('d/m/Y H:i') }}</td>
                                        <td>{{ $appointment->pet->name }}</td>
                                        <td>{{ $appointment->service->name }}</td>
                                        <td>{{ $appointment->service->petshop->name }}</td>
                                        <td>
                                            <span class="badge bg-{{ $appointment->status == 'pending' ? 'warning' : ($appointment->status == 'confirmed' ? 'success' : 'info') }}">
                                                {{ $appointment->status == 'pending' ? 'Pendente' : ($appointment->status == 'confirmed' ? 'Confirmado' : ucfirst($appointment->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('appointments.show', $appointment->id) }}" class="btn btn-sm btn-outline-primary">Ver</a>
                                            
                                            @if($appointment->status == 'pending')
                                                <a href="{{ route('appointments.edit', $appointment->id) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                                                
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
                                                    <form action="{{ route('appointments.destroy', $appointment->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Sim, Cancelar</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Nenhum agendamento próximo encontrado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- AGENDAMENTOS ANTERIORES -->
                <div class="tab-pane fade" id="past" role="tabpanel" aria-labelledby="past-tab">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Data e Hora</th>
                                    <th>Pet</th>
                                    <th>Serviço</th>
                                    <th>Pet Shop</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $activePrevious = $previousAppointments->where('status', '!=', 'cancelled');
                                @endphp
                                
                                @forelse($activePrevious as $appointment)
                                    <tr>
                                        <td>{{ $appointment->appointment_datetime->format('d/m/Y H:i') }}</td>
                                        <td>{{ $appointment->pet->name }}</td>
                                        <td>{{ $appointment->service->name }}</td>
                                        <td>{{ $appointment->service->petshop->name }}</td>
                                        <td>
                                            <span class="badge bg-{{ $appointment->status == 'completed' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($appointment->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('appointments.show', $appointment->id) }}" class="btn btn-sm btn-outline-primary">Ver</a>
                                            
                                            @if($appointment->status == 'completed' && isset($appointment->service->reviews) && !$appointment->service->reviews()->where('user_id', auth()->id())->exists())
                                                <a href="{{ route('services.show', $appointment->service->id) }}#review-form" class="btn btn-sm btn-outline-warning">Avaliar</a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Nenhum agendamento anterior encontrado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- AGENDAMENTOS CANCELADOS -->
                <div class="tab-pane fade" id="cancelled" role="tabpanel" aria-labelledby="cancelled-tab">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Data e Hora</th>
                                    <th>Pet</th>
                                    <th>Serviço</th>
                                    <th>Pet Shop</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $cancelledUpcoming = $upcomingAppointments->where('status', 'cancelled');
                                    $cancelledPrevious = $previousAppointments->where('status', 'cancelled');
                                    $cancelledAppointments = $cancelledUpcoming->merge($cancelledPrevious);
                                @endphp
                                
                                @forelse($cancelledAppointments as $appointment)
                                    <tr>
                                        <td>{{ $appointment->appointment_datetime->format('d/m/Y H:i') }}</td>
                                        <td>{{ $appointment->pet->name }}</td>
                                        <td>{{ $appointment->service->name }}</td>
                                        <td>{{ $appointment->service->petshop->name }}</td>
                                        <td>
                                            <span class="badge bg-danger">
                                                Cancelado
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('appointments.show', $appointment->id) }}" class="btn btn-sm btn-outline-primary">Ver</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Nenhum agendamento cancelado encontrado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection