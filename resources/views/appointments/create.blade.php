@extends('layouts.app')

@section('title', 'Novo Agendamento')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('appointments.index') }}">Agendamentos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Novo Agendamento</li>
        </ol>
    </nav>
    
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Novo Agendamento</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('appointments.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="pet_id" class="form-label">Pet*</label>
                        <select class="form-select @error('pet_id') is-invalid @enderror" id="pet_id" name="pet_id" required>
                            <option value="" selected disabled>Selecione seu pet...</option>
                            @foreach($pets as $pet)
                                <option value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'selected' : '' }}>
                                    {{ $pet->name }} ({{ ucfirst($pet->species) }})
                                </option>
                            @endforeach
                        </select>
                        @error('pet_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        @if($pets->isEmpty())
                            <div class="mt-2">
                                <a href="{{ route('pets.create') }}">Cadastre um pet primeiro</a>
                            </div>
                        @endif
                    </div>
                    
                    <div class="col-md-6">
                        <label for="petshop_id" class="form-label">Pet Shop*</label>
                        <select class="form-select @error('petshop_id') is-invalid @enderror" id="petshop_id" name="petshop_id" required>
                            <option value="" selected disabled>Selecione um pet shop...</option>
                            @foreach($petshops as $petshop)
                                <option value="{{ $petshop->id }}" {{ old('petshop_id') == $petshop->id ? 'selected' : '' }}>
                                    {{ $petshop->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('petshop_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="service_id" class="form-label">Serviço*</label>
                        <select class="form-select @error('service_id') is-invalid @enderror" id="service_id" name="service_id" required {{ $petshops->isEmpty() ? 'disabled' : '' }}>
                            <option value="" selected disabled>Selecione o pet shop primeiro...</option>
                        </select>
                        @error('service_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="employee_id" class="form-label">Funcionário*</label>
                        <select class="form-select @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id" required disabled>
                            <option value="" selected disabled>Selecione o serviço primeiro...</option>
                        </select>
                        @error('employee_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="appointment_date" class="form-label">Data*</label>
                        <input type="date" class="form-control @error('appointment_date') is-invalid @enderror" id="appointment_date" name="appointment_date" value="{{ old('appointment_date') }}" min="{{ date('Y-m-d') }}" required>
                        @error('appointment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="appointment_time" class="form-label">Horário*</label>
                        <input type="time" class="form-control @error('appointment_time') is-invalid @enderror" id="appointment_time" name="appointment_time" value="{{ old('appointment_time') }}" required>
                        @error('appointment_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="notes" class="form-label">Observações</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary me-md-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Agendar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const petshopSelect = document.getElementById('petshop_id');
        const serviceSelect = document.getElementById('service_id');
        const employeeSelect = document.getElementById('employee_id');
        
        // Função para mostrar erros no console e na interface
        function handleApiError(element, error, message) {
            console.error(message, error);
            element.innerHTML = `<option value="" selected disabled>${message}</option>`;
            element.disabled = false;
        }
        
        // Carregar serviços quando o petshop for selecionado
        petshopSelect.addEventListener('change', function() {
            const petshopId = this.value;
            
            if (petshopId) {
                // Limpar e desabilitar selects
                serviceSelect.disabled = true;
                serviceSelect.innerHTML = '<option value="" selected disabled>Carregando...</option>';
                employeeSelect.innerHTML = '<option value="" selected disabled>Selecione o serviço primeiro...</option>';
                employeeSelect.disabled = true;
                
                // Adicionar token CSRF ao cabeçalho
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                // Usar fetch API para buscar serviços
                fetch(`/api/petshops/${petshopId}/services`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Erro na resposta: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    serviceSelect.innerHTML = '<option value="" selected disabled>Selecione um serviço...</option>';
                    
                    if (data && data.length > 0) {
                        data.forEach(service => {
                            const option = document.createElement('option');
                            option.value = service.id;
                            option.textContent = `${service.name} - R$ ${parseFloat(service.price).toFixed(2).replace('.', ',')}`;
                            serviceSelect.appendChild(option);
                        });
                        
                        serviceSelect.disabled = false;
                    } else {
                        serviceSelect.innerHTML = '<option value="" selected disabled>Nenhum serviço disponível</option>';
                    }
                })
                .catch(error => {
                    handleApiError(serviceSelect, error, 'Erro ao carregar serviços');
                });
            } else {
                serviceSelect.innerHTML = '<option value="" selected disabled>Selecione o pet shop primeiro...</option>';
                serviceSelect.disabled = true;
                employeeSelect.innerHTML = '<option value="" selected disabled>Selecione o serviço primeiro...</option>';
                employeeSelect.disabled = true;
            }
        });
        
        // Carregar funcionários quando o serviço for selecionado
        serviceSelect.addEventListener('change', function() {
            const petshopId = petshopSelect.value;
            const serviceId = this.value;
            
            if (petshopId && serviceId) {
                employeeSelect.disabled = true;
                employeeSelect.innerHTML = '<option value="" selected disabled>Carregando...</option>';
                
                // Adicionar token CSRF ao cabeçalho
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                // Usar fetch API para buscar funcionários
                fetch(`/api/petshops/${petshopId}/employees`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Erro na resposta: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    employeeSelect.innerHTML = '<option value="" selected disabled>Selecione um funcionário...</option>';
                    
                    if (data && data.length > 0) {
                        data.forEach(employee => {
                            const option = document.createElement('option');
                            option.value = employee.id;
                            option.textContent = `${employee.user.name} - ${employee.position}`;
                            employeeSelect.appendChild(option);
                        });
                        
                        employeeSelect.disabled = false;
                    } else {
                        employeeSelect.innerHTML = '<option value="" selected disabled>Nenhum funcionário disponível</option>';
                    }
                })
                .catch(error => {
                    handleApiError(employeeSelect, error, 'Erro ao carregar funcionários');
                });
            } else {
                employeeSelect.innerHTML = '<option value="" selected disabled>Selecione o serviço primeiro...</option>';
                employeeSelect.disabled = true;
            }
        });
    });
</script>
@endsection