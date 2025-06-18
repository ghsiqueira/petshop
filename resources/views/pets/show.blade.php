@extends('layouts.app')

@section('title', $pet->name)

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pets.index') }}">Meus Pets</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $pet->name }}</li>
        </ol>
    </nav>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Informações do Pet</h5>
            <a href="{{ route('pets.edit', $pet->id) }}" class="btn btn-light btn-sm">
                <i class="fas fa-edit me-1"></i>Editar
            </a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 text-center mb-4">
                    <img src="{{ $pet->photo ? asset('storage/' . $pet->photo) : asset('img/no-pet-image.jpg') }}" 
                         class="img-fluid rounded-circle mb-3" 
                         style="width: 200px; height: 200px; object-fit: cover;" 
                         alt="{{ $pet->name }}">
                    <h4>{{ $pet->name }}</h4>
                    <span class="badge bg-primary">{{ ucfirst($pet->species) }}</span>
                    @if($pet->breed)
                        <span class="badge bg-secondary">{{ $pet->breed }}</span>
                    @endif
                </div>
                
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Gênero:</h6>
                            <p>{{ $pet->gender == 'male' ? 'Macho' : ($pet->gender == 'female' ? 'Fêmea' : 'Desconhecido') }}</p>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Idade:</h6>
                            <p>{{ $pet->birth_date ? $pet->formatted_age : 'Não informada' }}</p>
                        </div>
                        
                        @if($pet->birth_date)
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Data de Nascimento:</h6>
                            <p>{{ $pet->birth_date->format('d/m/Y') }}</p>
                        </div>
                        @endif
                        
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Cadastrado em:</h6>
                            <p>{{ $pet->created_at->format('d/m/Y') }}</p>
                        </div>
                        
                        @if($pet->medical_information)
                        <div class="col-12 mb-3">
                            <h6 class="text-muted">Informações Médicas:</h6>
                            <div class="p-3">
                                <p class="mb-0">{{ $pet->medical_information }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(count($appointments) > 0)
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Histórico de Agendamentos</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Data e Hora</th>
                            <th>Serviço</th>
                            <th>Pet Shop</th>
                            <th>Funcionário</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appointments as $appointment)
                        <tr>
                            <td>{{ $appointment->appointment_datetime->format('d/m/Y H:i') }}</td>
                            <td>{{ $appointment->service->name }}</td>
                            <td>{{ $appointment->service->petshop->name }}</td>
                            <td>{{ $appointment->employee->user->name }}</td>
                            <td>
                                <span class="badge bg-{{ $appointment->status == 'pending' ? 'warning' : ($appointment->status == 'confirmed' ? 'success' : ($appointment->status == 'completed' ? 'primary' : 'danger')) }}">
                                    {{ $appointment->status == 'pending' ? 'Pendente' : ($appointment->status == 'confirmed' ? 'Confirmado' : ($appointment->status == 'completed' ? 'Concluído' : 'Cancelado')) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <a href="{{ route('appointments.create') }}" class="btn btn-primary">
                <i class="fas fa-calendar-plus me-1"></i>Agendar Novo Serviço
            </a>
        </div>
    </div>
    @else
    <div class="alert alert-info">
        Este pet ainda não possui agendamentos. <a href="{{ route('appointments.create') }}" class="btn btn-sm btn-primary ms-2">Agende um serviço</a> para ele agora!
    </div>
    @endif
</div>
@endsection