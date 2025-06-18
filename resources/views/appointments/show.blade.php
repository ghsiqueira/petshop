@extends('layouts.app')

@section('title', 'Detalhes do Agendamento')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('appointments.index') }}">Agendamentos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detalhes do Agendamento</li>
        </ol>
    </nav>
    
    <div class="row">
        <!-- Coluna principal com detalhes -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detalhes do Agendamento</h5>
                    <div>
                        @if($appointment->status == 'pending')
                            <a href="{{ route('appointments.edit', $appointment->id) }}" class="btn btn-light btn-sm me-2">
                                <i class="fas fa-edit me-1"></i>Editar
                            </a>
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelAppointmentModal">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Data e Hora</h6>
                            <p class="fs-5">
                                <i class="far fa-calendar-alt me-2 text-primary"></i>
                                {{ $appointment->appointment_datetime->format('d/m/Y') }}
                            </p>
                            <p class="fs-5">
                                <i class="far fa-clock me-2 text-primary"></i>
                                {{ $appointment->appointment_datetime->format('H:i') }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Status</h6>
                            <span class="badge fs-6 bg-{{ $appointment->status == 'pending' ? 'warning' : ($appointment->status == 'confirmed' ? 'success' : ($appointment->status == 'completed' ? 'primary' : 'danger')) }} py-2 px-3">
                                {{ $appointment->status == 'pending' ? 'Pendente' : ($appointment->status == 'confirmed' ? 'Confirmado' : ($appointment->status == 'completed' ? 'Concluído' : 'Cancelado')) }}
                            </span>
                            
                            @if($appointment->status == 'pending')
                                <p class="text-muted mt-2 small">Aguardando confirmação pelo pet shop</p>
                            @elseif($appointment->status == 'confirmed')
                                <p class="text-muted mt-2 small">Confirmado pelo pet shop</p>
                            @elseif($appointment->status == 'completed')
                                <p class="text-muted mt-2 small">Serviço concluído em {{ $appointment->updated_at->format('d/m/Y') }}</p>
                            @else
                                <p class="text-muted mt-2 small">Cancelado em {{ $appointment->updated_at->format('d/m/Y') }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Serviço</h6>
                            <p class="fs-5">{{ $appointment->service->name }}</p>
                            <p class="badge bg-secondary">R$ {{ number_format($appointment->service->price, 2, ',', '.') }}</p>
                        </div>
                        @if(isset($appointment->service->duration_minutes) && $appointment->service->duration_minutes)
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Duração</h6>
                                <p>{{ $appointment->service->duration_minutes }} minutos</p>
                            </div>
                        @endif
                    </div>
                    
                    @if($appointment->notes)
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Observações</h6>
                        <div class="p-3 bg-light rounded">
                            <p class="mb-0">{{ $appointment->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            @if($appointment->status == 'completed')
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Serviço Concluído</h5>
                    </div>
                    <div class="card-body">
                        <p>Este serviço foi concluído com sucesso. Obrigado por utilizar nossos serviços!</p>
                        
                        @if(isset($appointment->service->reviews) && !$appointment->service->reviews()->where('user_id', auth()->id())->exists())
                            <a href="{{ route('services.show', $appointment->service->id) }}#review-form" class="btn btn-warning">
                                <i class="fas fa-star me-2"></i>Avaliar Serviço
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Coluna lateral com informações complementares -->
        <div class="col-md-4">
            <!-- Pet -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Pet</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ $appointment->pet->photo ? asset('storage/' . $appointment->pet->photo) : asset('img/no-pet-image.jpg') }}" 
                             class="rounded-circle me-3" 
                             style="width: 60px; height: 60px; object-fit: cover;" 
                             alt="{{ $appointment->pet->name }}">
                        <div>
                            <h5 class="mb-0">{{ $appointment->pet->name }}</h5>
                            <p class="text-muted mb-0">{{ ucfirst($appointment->pet->species) }}</p>
                        </div>
                    </div>
                    
                    @if($appointment->pet->breed)
                        <p class="mb-2"><strong>Raça:</strong> {{ $appointment->pet->breed }}</p>
                    @endif
                    
                    <p class="mb-0">
                        <a href="{{ route('pets.show', $appointment->pet->id) }}" class="btn btn-sm btn-outline-primary">Ver Detalhes do Pet</a>
                    </p>
                </div>
            </div>
            
            <!-- Pet Shop -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Pet Shop</h5>
                </div>
                <div class="card-body">
                    <h5 class="mb-1">{{ $appointment->service->petshop->name }}</h5>
                    <p class="mb-2"><i class="fas fa-map-marker-alt me-2 text-danger"></i>{{ $appointment->service->petshop->address }}</p>
                    <p class="mb-2"><i class="fas fa-phone me-2 text-success"></i>{{ $appointment->service->petshop->phone }}</p>
                    <p class="mb-3">
                        <i class="fas fa-clock me-2 text-primary"></i>
                        <small>{{ $appointment->service->petshop->opening_hours }}</small>
                    </p>
                    
                    <a href="{{ route('petshops.show', $appointment->service->petshop->id) }}" class="btn btn-sm btn-outline-primary">
                        Ver Pet Shop
                    </a>
                </div>
            </div>
            
            <!-- Funcionário -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Funcionário</h5>
                </div>
                <div class="card-body">
                    <h5 class="mb-1">{{ $appointment->employee->user->name }}</h5>
                    <p class="mb-2">
                        <span class="badge bg-info">{{ ucfirst($appointment->employee->role) }}</span>
                    </p>
                    @if($appointment->employee->bio)
                        <p class="small text-muted">{{ $appointment->employee->bio }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de Cancelamento -->
    <div class="modal fade" id="cancelAppointmentModal" tabindex="-1" aria-labelledby="cancelAppointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelAppointmentModalLabel">Confirmar Cancelamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja cancelar este agendamento?</p>
                    <p class="text-danger"><strong>Atenção:</strong> Esta ação não pode ser desfeita.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Não, Manter Agendamento</button>
                    <form action="{{ route('appointments.destroy', $appointment->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Sim, Cancelar Agendamento</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection