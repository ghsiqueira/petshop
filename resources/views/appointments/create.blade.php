{{-- resources/views/appointments/create.blade.php --}}
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
            <h5 class="mb-0">
                <i class="fas fa-calendar-plus me-2"></i>
                Novo Agendamento
            </h5>
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
                                <a href="{{ route('pets.create') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-plus me-1"></i>Cadastre um pet primeiro
                                </a>
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
                
                {{-- Usando o componente de data e horário --}}
                @include('components.datetime-picker', [
                    'dateId' => 'appointment_date',
                    'timeId' => 'appointment_time',
                    'dateLabel' => 'Data do Agendamento',
                    'timeLabel' => 'Horário do Agendamento',
                    'dateValue' => old('appointment_date'),
                    'timeValue' => old('appointment_time'),
                    'required' => true
                ])
                
                <div class="mb-3">
                    <label for="notes" class="form-label">Observações</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                              id="notes" 
                              name="notes" 
                              rows="3" 
                              placeholder="Adicione informações especiais sobre o agendamento...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                {{-- Preview do agendamento --}}
                <div class="card bg-light mb-4" id="appointment-preview" style="display: none;">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-eye me-2"></i>
                            Resumo do Agendamento
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Pet:</strong>
                                <div id="preview-pet" class="text-muted">-</div>
                            </div>
                            <div class="col-md-3">
                                <strong>Serviço:</strong>
                                <div id="preview-service" class="text-muted">-</div>
                            </div>
                            <div class="col-md-3">
                                <strong>Data:</strong>
                                <div id="preview-date" class="text-muted">-</div>
                            </div>
                            <div class="col-md-3">
                                <strong>Horário:</strong>
                                <div id="preview-time" class="text-muted">-</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary me-md-2">
                        <i class="fas fa-arrow-left me-1"></i>Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        <i class="fas fa-calendar-check me-1"></i>Agendar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const petshopSelect = document.getElementById('petshop_id');
        const serviceSelect = document.getElementById('service_id');
        const employeeSelect = document.getElementById('employee_id');
        
        // Elements for preview
        const petSelect = document.getElementById('pet_id');
        const dateInput = document.getElementById('appointment_date');
        const timeInput = document.getElementById('appointment_time');
        const previewCard = document.getElementById('appointment-preview');
        const submitBtn = document.getElementById('submit-btn');
        
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
                serviceSelect.disabled = true;
                serviceSelect.innerHTML = '<option value="" selected disabled>Carregando...</option>';
                employeeSelect.innerHTML = '<option value="" selected disabled>Selecione o serviço primeiro...</option>';
                employeeSelect.disabled = true;
                
                fetch(`/api/petshops/${petshopId}/services`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
                            option.textContent = `${service.name} - R$ ${parseFloat(service.price).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).replace('.', ',')}`;
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
                
                fetch(`/api/petshops/${petshopId}/employees`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
        
        // Função para atualizar o preview
        function updatePreview() {
            const pet = petSelect.options[petSelect.selectedIndex]?.text || '-';
            const service = serviceSelect.options[serviceSelect.selectedIndex]?.text || '-';
            const date = dateInput.value || '-';
            const time = timeInput.value || '-';
            
            document.getElementById('preview-pet').textContent = pet !== 'Selecione seu pet...' ? pet : '-';
            document.getElementById('preview-service').textContent = service !== 'Selecione um serviço...' ? service : '-';
            document.getElementById('preview-date').textContent = date;
            document.getElementById('preview-time').textContent = time;
            
            // Mostrar preview se pelo menos um campo estiver preenchido
            const hasData = pet !== '-' && pet !== 'Selecione seu pet...' || 
                           service !== '-' && service !== 'Selecione um serviço...' ||
                           date !== '-' || time !== '-';
            
            previewCard.style.display = hasData ? 'block' : 'none';
            
            // Habilitar/desabilitar botão de submit
            const allFilled = petSelect.value && serviceSelect.value && employeeSelect.value && date !== '-' && time !== '-';
            submitBtn.disabled = !allFilled;
            
            if (allFilled) {
                submitBtn.classList.remove('btn-outline-primary');
                submitBtn.classList.add('btn-primary');
            } else {
                submitBtn.classList.remove('btn-primary');
                submitBtn.classList.add('btn-outline-primary');
            }
        }
        
        // Event listeners para o preview
        [petSelect, serviceSelect, employeeSelect, dateInput, timeInput].forEach(element => {
            if (element) {
                element.addEventListener('change', updatePreview);
            }
        });
        
        // Atualizar preview inicial
        updatePreview();
        
        // Adicionar loading ao submit
        document.querySelector('form').addEventListener('submit', function() {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Agendando...';
            submitBtn.disabled = true;
        });
    });
</script>
@endpush