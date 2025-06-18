@extends('layouts.app')

@section('title', 'Detalhes do Funcionário')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('petshop.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('petshop.employees.index') }}">Funcionários</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $employee->user->name }}</li>
        </ol>
    </nav>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Informações do Funcionário</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center mb-4 mb-md-0">
                    <img src="{{ $employee->user->profile_picture ? asset('storage/' . $employee->user->profile_picture) : asset('img/no-profile-picture.jpg') }}" class="img-thumbnail rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;" alt="Foto de perfil">
                    
                    <h5>{{ $employee->position }}</h5>
                </div>
                
                <div class="col-md-9">
                    <h3>{{ $employee->user->name }}</h3>
                    
                    <div class="row mt-4">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Email:</h6>
                            <p>{{ $employee->user->email }}</p>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Data de Admissão:</h6>
                            <p>{{ $employee->created_at->format('d/m/Y') }}</p>
                        </div>
                        
                        @if($employee->bio)
                            <div class="col-12 mb-3">
                                <h6 class="text-muted">Biografia:</h6>
                                <p>{{ $employee->bio }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-end">
                <a href="{{ route('petshop.employees.index') }}" class="btn btn-outline-secondary me-2">Voltar</a>
                <a href="{{ route('petshop.employees.edit', $employee->id) }}" class="btn btn-primary">Editar</a>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">Próximos Agendamentos</h5>
        </div>
        <div class="card-body p-0">
            @php
                $upcomingAppointments = $employee->appointments()
                    ->whereDate('appointment_datetime', '>=', now())
                    ->where('status', '!=', 'cancelled')
                    ->orderBy('appointment_datetime')
                    ->limit(5)
                    ->get();
            @endphp
            
            @if($upcomingAppointments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Data e Hora</th>
                                <th>Cliente</th>
                                <th>Pet</th>
                                <th>Serviço</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($upcomingAppointments as $appointment)
                                <tr>
                                    <td>{{ $appointment->appointment_datetime->format('d/m/Y H:i') }}</td>
                                    <td>{{ $appointment->user->name }}</td>
                                    <td>{{ $appointment->pet->name }}</td>
                                    <td>{{ $appointment->service->name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $appointment->status == 'pending' ? 'warning' : ($appointment->status == 'confirmed' ? 'success' : 'secondary') }}">
                                            {{ $appointment->status == 'pending' ? 'Pendente' : ($appointment->status == 'confirmed' ? 'Confirmado' : ucfirst($appointment->status)) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <p class="mb-0">Nenhum agendamento próximo encontrado.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection