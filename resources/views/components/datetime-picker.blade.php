@props([
    'dateId' => 'date_field',
    'timeId' => 'time_field',
    'dateLabel' => 'Data',
    'timeLabel' => 'Horário',
    'dateValue' => '',
    'timeValue' => '',
    'required' => false,
    'disabled' => false
])

<div class="row mb-3">
    <!-- Data -->
    <div class="col-md-6">
        <label for="{{ $dateId }}" class="form-label">
            {{ $dateLabel }}{{ $required ? '*' : '' }}
        </label>
        <div class="custom-date-container" data-date-id="{{ $dateId }}">
            <input type="text" 
                   class="form-control custom-date-input @error($dateId) is-invalid @enderror" 
                   id="{{ $dateId }}" 
                   name="{{ $dateId }}_display"
                   value="{{ $dateValue }}"
                   placeholder="Clique para selecionar data..."
                   {{ $required ? 'required' : '' }}
                   {{ $disabled ? 'disabled' : '' }}
                   autocomplete="off"
                   readonly>
            
            <!-- Input hidden com formato ISO para o Laravel -->
            <input type="hidden" 
                   name="{{ $dateId }}" 
                   id="{{ $dateId }}_hidden"
                   value="">
            
            <i class="fas fa-calendar-alt date-icon"></i>
            
            <div class="custom-calendar-popup" id="cal_{{ $dateId }}">
                <div class="calendar-header">
                    <button type="button" class="nav-btn prev-btn">‹</button>
                    <span class="month-year"></span>
                    <button type="button" class="nav-btn next-btn">›</button>
                </div>
                <div class="calendar-weekdays">
                    <span>Dom</span><span>Seg</span><span>Ter</span><span>Qua</span><span>Qui</span><span>Sex</span><span>Sáb</span>
                </div>
                <div class="calendar-grid"></div>
                <div class="calendar-footer">
                    <button type="button" class="btn-today">Hoje</button>
                    <button type="button" class="btn-clear">Limpar</button>
                </div>
            </div>
        </div>
        @error($dateId)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Horário -->
    <div class="col-md-6">
        <label for="{{ $timeId }}" class="form-label">
            {{ $timeLabel }}{{ $required ? '*' : '' }}
        </label>
        <div class="custom-time-container" data-time-id="{{ $timeId }}">
            <input type="text" 
                   class="form-control custom-time-input @error($timeId) is-invalid @enderror" 
                   id="{{ $timeId }}" 
                   name="{{ $timeId }}"
                   value="{{ $timeValue }}"
                   placeholder="Selecione uma data primeiro..."
                   {{ $required ? 'required' : '' }}
                   {{ $disabled ? 'disabled' : '' }}
                   autocomplete="off"
                   readonly>
            <i class="fas fa-clock time-icon"></i>
            
            <div class="custom-time-popup" id="time_{{ $timeId }}">
                <div class="time-header">
                    <i class="fas fa-clock me-2"></i>Horários Disponíveis
                </div>
                <div class="time-grid">
                    <div class="p-3 text-center text-muted">
                        <i class="fas fa-calendar-day fa-2x mb-2 d-block text-info"></i>
                        <strong>Selecione uma data primeiro</strong><br>
                        <small>Os horários serão carregados automaticamente</small>
                    </div>
                </div>
            </div>
        </div>
        @error($timeId)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

@once
@push('styles')
<style>
/* === DATE/TIME PICKER STYLES === */
.custom-date-container,
.custom-time-container {
    position: relative;
}

.custom-date-input,
.custom-time-input {
    cursor: pointer !important;
    background: white !important;
    border: 2px solid #e3e6f0;
    border-radius: 8px;
    padding: 12px 45px 12px 15px;
    font-size: 14px;
    transition: all 0.2s ease;
}

.custom-date-input:focus,
.custom-time-input:focus,
.custom-date-input:hover,
.custom-time-input:hover {
    border-color: #4e73df !important;
    box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.1) !important;
    background: white !important;
    outline: none !important;
}

.date-icon,
.time-icon {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #858796;
    pointer-events: none;
    font-size: 16px;
}

/* CALENDAR POPUP */
.custom-calendar-popup {
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    background: white;
    border: 2px solid #e3e6f0;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    z-index: 1050;
    padding: 20px;
    min-width: 300px;
    display: none;
}

.custom-calendar-popup.show {
    display: block;
    animation: popIn 0.2s ease;
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.nav-btn {
    background: none;
    border: none;
    font-size: 18px;
    font-weight: bold;
    color: #4e73df;
    cursor: pointer;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.nav-btn:hover {
    background: #4e73df;
    color: white;
}

.month-year {
    font-weight: 600;
    font-size: 16px;
    color: #5a5c69;
    text-transform: capitalize;
}

.calendar-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 5px;
    margin-bottom: 10px;
}

.calendar-weekdays span {
    text-align: center;
    font-weight: 600;
    color: #858796;
    font-size: 12px;
    padding: 8px 4px;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 3px;
}

.calendar-day {
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    border: none;
    background: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s ease;
    color: #2c3e50 !important;
}

.calendar-day:hover:not(.disabled):not(.other-month) {
    background: #f8f9fc !important;
    transform: scale(1.1);
    color: #4e73df !important;
}

.calendar-day.today {
    background: #1cc88a !important;
    color: white !important;
    font-weight: 600;
}

.calendar-day.selected {
    background: #4e73df !important;
    color: white !important;
    font-weight: 600;
}

.calendar-day.disabled {
    color: #bdc3c7 !important;
    cursor: not-allowed;
    opacity: 0.5;
}

.calendar-day.other-month {
    color: #bdc3c7 !important;
    opacity: 0.6;
}

.calendar-footer {
    display: flex;
    justify-content: space-between;
    margin-top: 15px;
    padding-top: 10px;
    border-top: 1px solid #eee;
}

.btn-today,
.btn-clear {
    background: white !important;
    border: 1px solid #e3e6f0 !important;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    color: #5a5c69 !important;
    font-weight: 500;
}

.btn-today:hover {
    background: #1cc88a !important;
    color: white !important;
    border-color: #1cc88a !important;
}

.btn-clear:hover {
    background: #e74a3b !important;
    color: white !important;
    border-color: #e74a3b !important;
}

/* TIME POPUP */
.custom-time-popup {
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    right: 0;
    background: white;
    border: 2px solid #e3e6f0;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    z-index: 1050;
    max-height: 320px;
    overflow: hidden;
    display: none;
}

.custom-time-popup.show {
    display: block;
    animation: popIn 0.2s ease;
}

.time-header {
    padding: 15px;
    background: #f8f9fc;
    border-bottom: 1px solid #eee;
    font-weight: 600;
    font-size: 14px;
    color: #5a5c69 !important;
}

.time-grid {
    max-height: 250px;
    overflow-y: auto;
}

.time-option {
    display: block;
    width: 100%;
    padding: 12px 20px;
    border: none;
    background: none;
    text-align: left;
    cursor: pointer;
    font-size: 14px;
    border-bottom: 1px solid #f8f9fc;
    transition: all 0.2s ease;
    color: #2c3e50 !important;
    font-weight: 500;
}

.time-option:hover {
    background: #4e73df !important;
    color: white !important;
}

.time-option.selected {
    background: #4e73df !important;
    color: white !important;
    font-weight: 600;
}

.time-option.selected::after {
    content: ' ✓';
    float: right;
    color: white !important;
}

.time-loading,
.time-empty,
.time-error {
    padding: 20px;
    text-align: center;
}

.time-loading {
    color: #858796;
}

.time-empty {
    color: #6c757d;
}

.time-error {
    color: #e74a3b;
}

/* ANIMATIONS */
@keyframes popIn {
    from {
        opacity: 0;
        transform: translateY(-10px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* SCROLL CUSTOMIZADO */
.time-grid::-webkit-scrollbar {
    width: 6px;
}

.time-grid::-webkit-scrollbar-track {
    background: #f8f9fc;
}

.time-grid::-webkit-scrollbar-thumb {
    background: #d1d3e2;
    border-radius: 3px;
}

.time-grid::-webkit-scrollbar-thumb:hover {
    background: #858796;
}

/* MOBILE */
@media (max-width: 768px) {
    .custom-calendar-popup,
    .custom-time-popup {
        position: fixed !important;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        width: 90% !important;
        max-width: 320px !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
// === IMPROVED DATE TIME PICKER WITH DYNAMIC HOURS ===
class DynamicDateTimePicker {
    constructor() {
        this.currentMonth = new Date().getMonth();
        this.currentYear = new Date().getFullYear();
        this.selectedDate = null;
        this.selectedTime = null;
        this.activeCalendar = null;
        this.activeTimeContainer = null;
        
        this.monthNames = [
            'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
            'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
        ];
        
        this.init();
    }

    init() {
        console.log('🕐 Inicializando DynamicDateTimePicker...');
        this.setupDatePickers();
        this.setupTimePickers();
        this.setupClickOutside();
        this.setupServiceWatchers();
        console.log('✅ DynamicDateTimePicker inicializado!');
    }

    setupDatePickers() {
        const containers = document.querySelectorAll('.custom-date-container');
        console.log('📅 Encontrados', containers.length, 'date containers');
        
        containers.forEach(container => {
            const input = container.querySelector('.custom-date-input');
            const popup = container.querySelector('.custom-calendar-popup');
            
            if (input && popup) {
                input.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.toggleCalendar(container, popup);
                });

                this.setupCalendarNavigation(popup);
                console.log('📅 Date picker configurado para', input.id);
            }
        });
    }

    setupTimePickers() {
        const containers = document.querySelectorAll('.custom-time-container');
        console.log('🕐 Encontrados', containers.length, 'time containers');
        
        containers.forEach(container => {
            const input = container.querySelector('.custom-time-input');
            const popup = container.querySelector('.custom-time-popup');
            
            if (input && popup) {
                input.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.toggleTimePicker(container, popup);
                });

                console.log('🕐 Time picker configurado para', input.id);
            }
        });
    }

    setupServiceWatchers() {
        // Observar mudanças no serviço e petshop para atualizar horários
        const serviceSelect = document.getElementById('service_id');
        const petshopSelect = document.getElementById('petshop_id');
        const dateInput = document.querySelector('.custom-date-input');

        if (serviceSelect) {
            serviceSelect.addEventListener('change', () => {
                this.resetTimeField();
                console.log('🔄 Serviço alterado, horários resetados');
            });
        }

        if (petshopSelect) {
            petshopSelect.addEventListener('change', () => {
                this.resetTimeField();
                console.log('🔄 Pet shop alterado, horários resetados');
            });
        }

        if (dateInput) {
            dateInput.addEventListener('change', () => {
                this.resetTimeField();
                console.log('🔄 Data alterada, horários serão recarregados');
            });
        }
    }

    setupCalendarNavigation(popup) {
        const prevBtn = popup.querySelector('.prev-btn');
        const nextBtn = popup.querySelector('.next-btn');
        const todayBtn = popup.querySelector('.btn-today');
        const clearBtn = popup.querySelector('.btn-clear');

        if (prevBtn) {
            prevBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.changeMonth(-1);
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.changeMonth(1);
            });
        }

        if (todayBtn) {
            todayBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.selectToday();
            });
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.clearDate();
            });
        }
    }

    toggleCalendar(container, popup) {
        this.closeAllPopups();
        
        if (popup.classList.contains('show')) {
            popup.classList.remove('show');
            this.activeCalendar = null;
        } else {
            popup.classList.add('show');
            this.activeCalendar = popup;
            this.renderCalendar(popup);
        }
    }

    toggleTimePicker(container, popup) {
        this.closeAllPopups();
        
        if (popup.classList.contains('show')) {
            popup.classList.remove('show');
            this.activeTimeContainer = null;
        } else {
            popup.classList.add('show');
            this.activeTimeContainer = container;
            this.loadAvailableHours(popup);
        }
    }

    closeAllPopups() {
        document.querySelectorAll('.custom-calendar-popup, .custom-time-popup').forEach(popup => {
            popup.classList.remove('show');
        });
        this.activeCalendar = null;
        this.activeTimeContainer = null;
    }

    setupClickOutside() {
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.custom-date-container') && 
                !e.target.closest('.custom-time-container')) {
                this.closeAllPopups();
            }
        });
    }

    changeMonth(direction) {
        this.currentMonth += direction;
        
        if (this.currentMonth > 11) {
            this.currentMonth = 0;
            this.currentYear++;
        } else if (this.currentMonth < 0) {
            this.currentMonth = 11;
            this.currentYear--;
        }
        
        if (this.activeCalendar) {
            this.renderCalendar(this.activeCalendar);
        }
    }

    renderCalendar(popup) {
        const monthYear = popup.querySelector('.month-year');
        const grid = popup.querySelector('.calendar-grid');
        
        if (!monthYear || !grid) {
            console.error('❌ Elementos do calendário não encontrados');
            return;
        }

        monthYear.textContent = `${this.monthNames[this.currentMonth]} ${this.currentYear}`;
        grid.innerHTML = '';

        const firstDay = new Date(this.currentYear, this.currentMonth, 1);
        const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);
        const daysInMonth = lastDay.getDate();
        const startingDay = firstDay.getDay();

        // Dias do mês anterior
        const prevMonth = new Date(this.currentYear, this.currentMonth - 1, 0);
        for (let i = startingDay - 1; i >= 0; i--) {
            const day = prevMonth.getDate() - i;
            const button = this.createDayButton(day, true, false);
            button.classList.add('other-month');
            grid.appendChild(button);
        }

        // Dias do mês atual
        const today = new Date();
        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(this.currentYear, this.currentMonth, day);
            const isToday = this.isSameDate(date, today);
            const isPast = date < today && !isToday;
            
            const button = this.createDayButton(day, false, isPast);
            
            if (isToday) {
                button.classList.add('today');
            }
            
            if (!isPast) {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.selectDate(date);
                });
            }
            
            grid.appendChild(button);
        }

        // Dias do próximo mês
        const totalCells = grid.children.length;
        const remainingCells = 42 - totalCells;
        
        for (let day = 1; day <= remainingCells; day++) {
            const button = this.createDayButton(day, true, false);
            button.classList.add('other-month');
            grid.appendChild(button);
        }

        console.log('📅 Calendário renderizado para', this.monthNames[this.currentMonth], this.currentYear);
    }

    createDayButton(day, isOtherMonth, isDisabled) {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'calendar-day';
        button.textContent = day;
        
        if (isDisabled) {
            button.classList.add('disabled');
            button.disabled = true;
        }
        
        return button;
    }

    selectDate(date) {
        if (!this.activeCalendar) return;
        
        const container = this.activeCalendar.closest('.custom-date-container');
        const input = container.querySelector('.custom-date-input');
        const hiddenInput = container.querySelector('input[type="hidden"]');
        
        if (input) {
            const displayDate = this.formatDate(date);
            const isoDate = this.formatDateISO(date);
            
            input.value = displayDate;
            if (hiddenInput) {
                hiddenInput.value = isoDate;
            }
            
            // Atualizar visual do calendário
            this.activeCalendar.querySelectorAll('.calendar-day').forEach(btn => {
                btn.classList.remove('selected');
            });
            
            const dayButtons = this.activeCalendar.querySelectorAll('.calendar-day');
            dayButtons.forEach(btn => {
                if (btn.textContent == date.getDate() && !btn.classList.contains('other-month')) {
                    btn.classList.add('selected');
                }
            });
            
            this.closeAllPopups();
            input.dispatchEvent(new Event('change', { bubbles: true }));
            
            // Resetar campo de horário para forçar novo carregamento
            this.resetTimeField();
            
            console.log('📅 Data selecionada:', displayDate, '(ISO:', isoDate, ')');
        }
    }

    selectToday() {
        const today = new Date();
        this.currentMonth = today.getMonth();
        this.currentYear = today.getFullYear();
        this.selectDate(today);
    }

    clearDate() {
        if (!this.activeCalendar) return;
        
        const container = this.activeCalendar.closest('.custom-date-container');
        const input = container.querySelector('.custom-date-input');
        const hiddenInput = container.querySelector('input[type="hidden"]');
        
        if (input) {
            input.value = '';
            if (hiddenInput) {
                hiddenInput.value = '';
            }
            input.dispatchEvent(new Event('change', { bubbles: true }));
            this.closeAllPopups();
            this.resetTimeField();
            console.log('📅 Data limpa');
        }
    }

    loadAvailableHours(popup) {
        const grid = popup.querySelector('.time-grid');
        if (!grid) return;

        const serviceSelect = document.getElementById('service_id');
        const petshopSelect = document.getElementById('petshop_id');
        const dateInput = document.querySelector('input[type="hidden"][name*="date"]');
        
        if (!serviceSelect?.value || !petshopSelect?.value) {
            grid.innerHTML = this.getTimeMessage('info', 'Selecione um serviço primeiro', 'fa-hand-point-up');
            return;
        }

        if (!dateInput?.value) {
            grid.innerHTML = this.getTimeMessage('warning', 'Selecione uma data primeiro', 'fa-calendar-day');
            return;
        }

        // Mostrar loading
        grid.innerHTML = this.getTimeMessage('loading', 'Carregando horários disponíveis...', 'fa-spinner fa-spin');

        const url = `/api/petshops/${petshopSelect.value}/available-slots?service_id=${serviceSelect.value}&date=${dateInput.value}`;
        
        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Erro ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.slots && data.slots.length > 0) {
                grid.innerHTML = data.slots.map(time => 
                    `<button type="button" class="time-option" data-time="${time}">
                        <i class="fas fa-clock me-2"></i>${time}
                    </button>`
                ).join('');

                grid.querySelectorAll('.time-option').forEach(option => {
                    option.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        this.selectTime(option.dataset.time, popup);
                    });
                });

                console.log('🕐', data.slots.length, 'horários disponíveis carregados para', data.service);
            } else {
                grid.innerHTML = this.getTimeMessage('danger', 'Nenhum horário disponível', 'fa-calendar-times', 'Tente outra data ou entre em contato');
            }
        })
        .catch(error => {
            console.error('❌ Erro ao carregar horários:', error);
            grid.innerHTML = this.getTimeMessage('danger', 'Erro ao carregar horários', 'fa-exclamation-triangle', 'Tente novamente em alguns instantes');
        });
    }

    getTimeMessage(type, title, icon, subtitle = '') {
        const colors = {
            loading: 'text-info',
            info: 'text-info', 
            warning: 'text-warning',
            danger: 'text-danger'
        };

        return `
            <div class="time-${type} ${colors[type]}">
                <i class="fas ${icon} fa-2x mb-2 d-block"></i>
                <strong>${title}</strong>
                ${subtitle ? `<br><small>${subtitle}</small>` : ''}
            </div>
        `;
    }

    selectTime(time, popup) {
        const container = popup.closest('.custom-time-container');
        const input = container.querySelector('.custom-time-input');
        
        if (input) {
            input.value = time;
            
            popup.querySelectorAll('.time-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            
            popup.querySelector(`[data-time="${time}"]`).classList.add('selected');
            this.closeAllPopups();
            input.dispatchEvent(new Event('change', { bubbles: true }));
            
            console.log('🕐 Horário selecionado:', time);
        }
    }

    resetTimeField() {
        const timeInput = document.querySelector('.custom-time-input');
        if (timeInput) {
            timeInput.value = '';
            timeInput.placeholder = 'Selecione uma data primeiro...';
        }
    }

    formatDate(date) {
        const day = date.getDate().toString().padStart(2, '0');
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const year = date.getFullYear();
        return `${day}/${month}/${year}`;
    }

    formatDateISO(date) {
        const year = date.getFullYear();
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const day = date.getDate().toString().padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    isSameDate(date1, date2) {
        return date1.getDate() === date2.getDate() &&
               date1.getMonth() === date2.getMonth() &&
               date1.getFullYear() === date2.getFullYear();
    }
}

// INICIALIZAÇÃO
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOM carregado, inicializando dynamic date/time picker...');
    
    setTimeout(() => {
        window.dynamicDateTimePicker = new DynamicDateTimePicker();
    }, 150);
});
</script>
@endpush
@endonce