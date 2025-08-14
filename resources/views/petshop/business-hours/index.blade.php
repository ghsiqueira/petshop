@extends('layouts.app')

@section('title', 'Horários de Funcionamento')

@push('styles')
<style>
.day-config {
    transition: all 0.3s ease;
    background: #f8f9fc;
}

.day-config:hover {
    background: #e3e6f0;
}

.day-config.enabled {
    background: #d1ecf1;
    border-color: #17a2b8 !important;
}

.time-input:disabled {
    background-color: #e9ecef;
    opacity: 0.6;
}

.quick-actions .btn {
    margin-right: 0.5rem;
    margin-bottom: 0.5rem;
}

.config-card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.day-status-icon {
    font-size: 1.2rem;
    transition: all 0.3s ease;
}

.lunch-break-info {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    border: 1px solid #ffeaa7;
    border-radius: 8px;
    padding: 1rem;
}

@media (max-width: 768px) {
    .day-config .row > div {
        margin-bottom: 1rem;
    }
    
    .quick-actions {
        text-align: center;
    }
    
    .quick-actions .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}
</style>
@endpush

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">
                <i class="fas fa-clock me-2 text-primary"></i>
                Horários de Funcionamento
            </h1>
            <p class="text-muted mb-0">Configure os horários de atendimento do seu pet shop</p>
        </div>
        <div>
            <a href="{{ route('petshop.dashboard') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i>Voltar
            </a>
            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#testModal">
                <i class="fas fa-flask me-1"></i>Testar
            </button>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <h6><i class="fas fa-exclamation-triangle me-2"></i>Erro na validação:</h6>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('petshop.business-hours.update') }}" method="POST" id="businessHoursForm">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Horários por Dia da Semana -->
            <div class="col-lg-8">
                <div class="card config-card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-week me-2"></i>
                            Horários por Dia da Semana
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                            $daysConfig = [
                                'monday' => ['name' => 'Segunda-feira', 'icon' => 'fa-briefcase', 'color' => 'primary'],
                                'tuesday' => ['name' => 'Terça-feira', 'icon' => 'fa-briefcase', 'color' => 'primary'],
                                'wednesday' => ['name' => 'Quarta-feira', 'icon' => 'fa-briefcase', 'color' => 'primary'],
                                'thursday' => ['name' => 'Quinta-feira', 'icon' => 'fa-briefcase', 'color' => 'primary'],
                                'friday' => ['name' => 'Sexta-feira', 'icon' => 'fa-briefcase', 'color' => 'primary'],
                                'saturday' => ['name' => 'Sábado', 'icon' => 'fa-calendar-day', 'color' => 'info'],
                                'sunday' => ['name' => 'Domingo', 'icon' => 'fa-home', 'color' => 'warning']
                            ];
                        @endphp

                        @foreach($daysConfig as $day => $config)
                            <div class="day-config border rounded p-3 mb-3 {{ $businessHours[$day]['enabled'] ? 'enabled' : '' }}" 
                                 data-day="{{ $day }}">
                                <div class="row align-items-center">
                                    <div class="col-md-3 col-sm-12">
                                        <div class="form-check">
                                            <input class="form-check-input day-toggle" 
                                                   type="checkbox" 
                                                   id="{{ $day }}_enabled" 
                                                   name="{{ $day }}_enabled"
                                                   {{ $businessHours[$day]['enabled'] ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="{{ $day }}_enabled">
                                                <i class="fas {{ $config['icon'] }} me-2 text-{{ $config['color'] }}"></i>
                                                {{ $config['name'] }}
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3 col-sm-6">
                                        <label class="form-label small text-muted">
                                            <i class="fas fa-sun me-1"></i>Abertura
                                        </label>
                                        <input type="time" 
                                               class="form-control time-input @error($day.'_open') is-invalid @enderror" 
                                               name="{{ $day }}_open" 
                                               value="{{ $businessHours[$day]['open'] ?? '08:00' }}"
                                               {{ !$businessHours[$day]['enabled'] ? 'disabled' : '' }}>
                                        @error($day.'_open')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-3 col-sm-6">
                                        <label class="form-label small text-muted">
                                            <i class="fas fa-moon me-1"></i>Fechamento
                                        </label>
                                        <input type="time" 
                                               class="form-control time-input @error($day.'_close') is-invalid @enderror" 
                                               name="{{ $day }}_close" 
                                               value="{{ $businessHours[$day]['close'] ?? '18:00' }}"
                                               {{ !$businessHours[$day]['enabled'] ? 'disabled' : '' }}>
                                        @error($day.'_close')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 col-sm-12 text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="day-status-icon fas {{ $businessHours[$day]['enabled'] ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }}" 
                                               title="{{ $businessHours[$day]['enabled'] ? 'Ativo' : 'Fechado' }}"></i>
                                            <span class="ms-2 small text-muted status-text">
                                                {{ $businessHours[$day]['enabled'] ? 'Aberto' : 'Fechado' }}
                                            </span>
                                        </div>
                                        @if($businessHours[$day]['enabled'])
                                            <small class="text-muted d-block">
                                                {{ $businessHours[$day]['open'] }} - {{ $businessHours[$day]['close'] }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Ações Rápidas -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="bg-light rounded p-3">
                                    <h6 class="text-muted mb-3">
                                        <i class="fas fa-magic me-2"></i>Ações Rápidas:
                                    </h6>
                                    <div class="quick-actions">
                                        <button type="button" class="btn btn-outline-primary btn-sm" id="enable-weekdays">
                                            <i class="fas fa-business-time me-1"></i>Seg-Sex 8h-18h
                                        </button>
                                        <button type="button" class="btn btn-outline-info btn-sm" id="enable-weekend">
                                            <i class="fas fa-calendar-weekend me-1"></i>Fins de Semana
                                        </button>
                                        <button type="button" class="btn btn-outline-success btn-sm" id="enable-all">
                                            <i class="fas fa-check-double me-1"></i>Todos os Dias
                                        </button>
                                        <button type="button" class="btn btn-outline-warning btn-sm" id="disable-all">
                                            <i class="fas fa-times me-1"></i>Desabilitar Todos
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configurações Gerais -->
            <div class="col-lg-4">
                <!-- Configurações de Agendamento -->
                <div class="card config-card mb-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-cogs me-2"></i>
                            Configurações de Agendamento
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="slot_duration" class="form-label">
                                <i class="fas fa-clock me-1 text-primary"></i>Duração do Slot
                            </label>
                            <select class="form-select @error('slot_duration') is-invalid @enderror" 
                                    id="slot_duration" name="slot_duration">
                                <option value="15" {{ ($petshop->slot_duration ?? 30) == 15 ? 'selected' : '' }}>15 minutos</option>
                                <option value="30" {{ ($petshop->slot_duration ?? 30) == 30 ? 'selected' : '' }}>30 minutos</option>
                                <option value="60" {{ ($petshop->slot_duration ?? 30) == 60 ? 'selected' : '' }}>60 minutos</option>
                            </select>
                            @error('slot_duration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Intervalo entre horários disponíveis</small>
                        </div>

                        <div class="mb-3">
                            <label for="advance_booking_days" class="form-label">
                                <i class="fas fa-calendar-plus me-1 text-info"></i>Antecedência Máxima
                            </label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control @error('advance_booking_days') is-invalid @enderror" 
                                       id="advance_booking_days" 
                                       name="advance_booking_days" 
                                       value="{{ $petshop->advance_booking_days ?? 30 }}"
                                       min="1" max="365">
                                <span class="input-group-text">dias</span>
                                @error('advance_booking_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Quantos dias à frente pode agendar</small>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="allow_weekend_booking" 
                                   name="allow_weekend_booking"
                                   {{ ($petshop->allow_weekend_booking ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="allow_weekend_booking">
                                <i class="fas fa-calendar-weekend me-1 text-info"></i>
                                Permitir agendamento em fins de semana
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Horário de Almoço -->
                <div class="card config-card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">
                            <i class="fas fa-utensils me-2"></i>
                            Horário de Almoço
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="lunch-break-info mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-info-circle me-2 text-warning"></i>
                                <strong>Pausa para Almoço</strong>
                            </div>
                            <small class="text-muted">
                                Durante este período não haverá agendamentos disponíveis em nenhum dia.
                                Deixe em branco se não houver pausa.
                            </small>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <label for="lunch_break_start" class="form-label">
                                    <i class="fas fa-play me-1"></i>Início
                                </label>
                                <input type="time" 
                                       class="form-control @error('lunch_break_start') is-invalid @enderror" 
                                       id="lunch_break_start" 
                                       name="lunch_break_start" 
                                       value="{{ $petshop->lunch_break_start }}">
                                @error('lunch_break_start')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-6">
                                <label for="lunch_break_end" class="form-label">
                                    <i class="fas fa-stop me-1"></i>Fim
                                </label>
                                <input type="time" 
                                       class="form-control @error('lunch_break_end') is-invalid @enderror" 
                                       id="lunch_break_end" 
                                       name="lunch_break_end" 
                                       value="{{ $petshop->lunch_break_end }}">
                                @error('lunch_break_end')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        @if($petshop->lunch_break_start && $petshop->lunch_break_end)
                            <div class="mt-3 p-2 bg-light rounded">
                                <small class="text-success">
                                    <i class="fas fa-check me-1"></i>
                                    Pausa configurada: {{ $petshop->lunch_break_start }} às {{ $petshop->lunch_break_end }}
                                </small>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Resumo -->
                <div class="card config-card">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-chart-pie me-2"></i>
                            Resumo
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h4 class="text-success mb-1" id="active-days-count">
                                        {{ collect($businessHours)->where('enabled', true)->count() }}
                                    </h4>
                                    <small class="text-muted">Dias Ativos</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="text-primary mb-1" id="total-hours-week">--</h4>
                                <small class="text-muted">Horas/Semana</small>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="small">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Slot Duration:</span>
                                <span class="fw-bold">{{ $petshop->slot_duration ?? 30 }}min</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Max Antecedência:</span>
                                <span class="fw-bold">{{ $petshop->advance_booking_days ?? 30 }} dias</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Fins de Semana:</span>
                                <span class="fw-bold {{ ($petshop->allow_weekend_booking ?? true) ? 'text-success' : 'text-danger' }}">
                                    {{ ($petshop->allow_weekend_booking ?? true) ? 'Permitido' : 'Bloqueado' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botões de Ação -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card config-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <button type="button" class="btn btn-outline-info" id="preview-changes">
                                    <i class="fas fa-eye me-1"></i>Visualizar Mudanças
                                </button>
                            </div>
                            <div>
                                <a href="{{ route('petshop.dashboard') }}" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-times me-1"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary" id="save-btn">
                                    <i class="fas fa-save me-1"></i>Salvar Horários
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal de Teste -->
<div class="modal fade" id="testModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-flask me-2"></i>Testar Configuração
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Data para Teste:</label>
                        <input type="date" class="form-control" id="test-date" 
                               value="{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}" 
                               min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="button" class="btn btn-primary" id="run-test">
                            <i class="fas fa-play me-1"></i>Executar Teste
                        </button>
                    </div>
                </div>
                <div id="test-results"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle de dias da semana
    document.querySelectorAll('.day-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const dayConfig = this.closest('.day-config');
            const dayInputs = dayConfig.querySelectorAll('.time-input');
            const statusIcon = dayConfig.querySelector('.day-status-icon');
            const statusText = dayConfig.querySelector('.status-text');
            
            dayInputs.forEach(input => {
                input.disabled = !this.checked;
                if (!this.checked) {
                    input.value = '';
                }
            });

            // Atualizar visual
            if (this.checked) {
                dayConfig.classList.add('enabled');
                statusIcon.className = 'day-status-icon fas fa-check-circle text-success';
                statusIcon.title = 'Ativo';
                statusText.textContent = 'Aberto';
                
                // Definir horários padrão se vazios
                if (!dayInputs[0].value) dayInputs[0].value = '08:00';
                if (!dayInputs[1].value) dayInputs[1].value = '18:00';
            } else {
                dayConfig.classList.remove('enabled');
                statusIcon.className = 'day-status-icon fas fa-times-circle text-danger';
                statusIcon.title = 'Fechado';
                statusText.textContent = 'Fechado';
            }
            
            updateSummary();
        });
    });

    // Ações rápidas
    document.getElementById('enable-weekdays').addEventListener('click', function() {
        const weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        weekdays.forEach(day => {
            const toggle = document.getElementById(day + '_enabled');
            const openInput = document.querySelector(`input[name="${day}_open"]`);
            const closeInput = document.querySelector(`input[name="${day}_close"]`);
            
            toggle.checked = true;
            toggle.dispatchEvent(new Event('change'));
            openInput.value = '08:00';
            closeInput.value = '18:00';
        });
    });

    document.getElementById('enable-weekend').addEventListener('click', function() {
        const weekend = ['saturday', 'sunday'];
        weekend.forEach(day => {
            const toggle = document.getElementById(day + '_enabled');
            const openInput = document.querySelector(`input[name="${day}_open"]`);
            const closeInput = document.querySelector(`input[name="${day}_close"]`);
            
            toggle.checked = true;
            toggle.dispatchEvent(new Event('change'));
            openInput.value = day === 'saturday' ? '08:00' : '09:00';
            closeInput.value = day === 'saturday' ? '16:00' : '15:00';
        });
    });

    document.getElementById('enable-all').addEventListener('click', function() {
        document.querySelectorAll('.day-toggle').forEach(toggle => {
            if (!toggle.checked) {
                toggle.checked = true;
                toggle.dispatchEvent(new Event('change'));
            }
        });
    });

    document.getElementById('disable-all').addEventListener('click', function() {
        if (confirm('Tem certeza que deseja desabilitar todos os dias?')) {
            document.querySelectorAll('.day-toggle').forEach(toggle => {
                toggle.checked = false;
                toggle.dispatchEvent(new Event('change'));
            });
        }
    });

    // Validação de horários
    document.querySelectorAll('input[type="time"]').forEach(input => {
        input.addEventListener('change', function() {
            const dayConfig = this.closest('.day-config');
            if (dayConfig) {
                const openInput = dayConfig.querySelector('input[name$="_open"]');
                const closeInput = dayConfig.querySelector('input[name$="_close"]');
                
                if (openInput && closeInput && openInput.value && closeInput.value) {
                    if (openInput.value >= closeInput.value) {
                        alert('O horário de fechamento deve ser depois do horário de abertura!');
                        this.focus();
                        return;
                    }
                }
            }
            updateSummary();
        });
    });

    // Atualizar resumo
    function updateSummary() {
        const activeDays = document.querySelectorAll('.day-toggle:checked').length;
        document.getElementById('active-days-count').textContent = activeDays;
        
        // Calcular total de horas por semana
        let totalHours = 0;
        document.querySelectorAll('.day-toggle:checked').forEach(toggle => {
            const dayConfig = toggle.closest('.day-config');
            const openInput = dayConfig.querySelector('input[name$="_open"]');
            const closeInput = dayConfig.querySelector('input[name$="_close"]');
            
            if (openInput.value && closeInput.value) {
                const open = new Date('2000-01-01 ' + openInput.value);
                const close = new Date('2000-01-01 ' + closeInput.value);
                const hours = (close - open) / (1000 * 60 * 60);
                totalHours += hours;
            }
        });
        
        document.getElementById('total-hours-week').textContent = totalHours.toFixed(1) + 'h';
    }

    // Teste de configuração
    document.getElementById('run-test').addEventListener('click', function() {
        const testDate = document.getElementById('test-date').value;
        const resultsDiv = document.getElementById('test-results');
        
        resultsDiv.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Testando...</div>';
        
        fetch(`/petshop/business-hours/test?date=${testDate}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            let html = `<h6>Resultado para ${testDate}:</h6>`;
            
            if (data.services && data.services.length > 0) {
                html += '<div class="row">';
                data.services.forEach(service => {
                    html += `
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">${service.service_name}</h6>
                                    <p class="card-text">
                                        <span class="badge bg-${service.available_slots > 0 ? 'success' : 'danger'}">
                                            ${service.available_slots} horários disponíveis
                                        </span>
                                    </p>
                                    ${service.slots.length > 0 ? 
                                        '<small class="text-muted">' + service.slots.join(', ') + '</small>' : 
                                        '<small class="text-danger">Nenhum horário disponível</small>'
                                    }
                                </div>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
            } else {
                html += '<div class="alert alert-warning">Nenhum serviço encontrado ou pet shop fechado nesta data.</div>';
            }
            
            resultsDiv.innerHTML = html;
        })
        .catch(error => {
            resultsDiv.innerHTML = '<div class="alert alert-danger">Erro ao executar teste: ' + error.message + '</div>';
        });
    });

    // Preview de mudanças
    document.getElementById('preview-changes').addEventListener('click', function() {
        const changes = [];
        
        document.querySelectorAll('.day-toggle').forEach(toggle => {
            const day = toggle.id.replace('_enabled', '');
            const dayName = toggle.closest('.day-config').querySelector('label').textContent.trim();
            const isEnabled = toggle.checked;
            const openInput = document.querySelector(`input[name="${day}_open"]`);
            const closeInput = document.querySelector(`input[name="${day}_close"]`);
            
            changes.push({
                day: dayName,
                enabled: isEnabled,
                open: openInput ? openInput.value : '',
                close: closeInput ? closeInput.value : ''
            });
        });
        
        let preview = 'Resumo das alterações:\n\n';
        changes.forEach(change => {
            if (change.enabled) {
                preview += `✅ ${change.day}: ${change.open} - ${change.close}\n`;
            } else {
                preview += `❌ ${change.day}: Fechado\n`;
            }
        });
        
        alert(preview);
    });

    // Submit com loading
    document.getElementById('businessHoursForm').addEventListener('submit', function() {
        const saveBtn = document.getElementById('save-btn');
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Salvando...';
        saveBtn.disabled = true;
    });

    // Inicializar resumo
    updateSummary();
});
</script>
@endpush