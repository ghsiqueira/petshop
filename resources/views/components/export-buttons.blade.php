@php
    $routePrefix = match($type) {
        'client' => 'export.client.dashboard',
        'petshop' => 'export.petshop.dashboard', 
        'admin' => 'export.admin.dashboard',
        default => 'export.client.dashboard'
    };
    
    $colors = match($type) {
        'client' => ['primary' => 'primary', 'accent' => 'info'],
        'petshop' => ['primary' => 'success', 'accent' => 'warning'],
        'admin' => ['primary' => 'dark', 'accent' => 'secondary'],
        default => ['primary' => 'primary', 'accent' => 'info']
    };
@endphp

<div class="export-buttons">
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-outline-{{ $colors['primary'] }} dropdown-toggle export-dropdown-btn" 
                data-bs-toggle="dropdown" aria-expanded="false" id="exportDropdown{{ ucfirst($type) }}">
            <i class="fas fa-download me-1"></i>
            <span class="export-btn-text">Exportar Dashboard</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end export-dropdown" aria-labelledby="exportDropdown{{ ucfirst($type) }}">
            <!-- Header -->
            <li>
                <h6 class="dropdown-header">
                    <i class="fas fa-file-export me-2"></i>Escolha o formato
                </h6>
            </li>
            
            <!-- PDF Export -->
            <li>
                <a class="dropdown-item export-item" href="{{ route($routePrefix, 'pdf') }}" 
                   target="_blank" data-format="PDF" data-type="{{ $type }}">
                    <div class="export-option">
                        <div class="export-icon">
                            <i class="fas fa-file-pdf text-danger"></i>
                        </div>
                        <div class="export-info">
                            <div class="export-title">Exportar como PDF</div>
                            <div class="export-description">Relatório completo formatado</div>
                        </div>
                        <div class="export-size">
                            <small class="text-muted">~2MB</small>
                        </div>
                    </div>
                </a>
            </li>
            
            <!-- Excel Export -->
            <li>
                <a class="dropdown-item export-item" href="{{ route($routePrefix, 'xlsx') }}"
                   data-format="Excel" data-type="{{ $type }}">
                    <div class="export-option">
                        <div class="export-icon">
                            <i class="fas fa-file-excel text-success"></i>
                        </div>
                        <div class="export-info">
                            <div class="export-title">Exportar como Excel</div>
                            <div class="export-description">Planilha com múltiplas abas</div>
                        </div>
                        <div class="export-size">
                            <small class="text-muted">~1MB</small>
                        </div>
                    </div>
                </a>
            </li>
            
            <!-- CSV Export -->
            <li>
                <a class="dropdown-item export-item" href="{{ route($routePrefix, 'csv') }}" 
                   data-format="CSV" data-type="{{ $type }}">
                    <div class="export-option">
                        <div class="export-icon">
                            <i class="fas fa-file-csv text-info"></i>
                        </div>
                        <div class="export-info">
                            <div class="export-title">Exportar como CSV</div>
                            <div class="export-description">Dados para análise</div>
                        </div>
                        <div class="export-size">
                            <small class="text-muted">~500KB</small>
                        </div>
                    </div>
                </a>
            </li>
            
            <li><hr class="dropdown-divider"></li>
            
            <!-- Info Section -->
            <li>
                <div class="dropdown-item-text export-help">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        <strong>Dica:</strong> Use PDF para visualização, Excel para análise e CSV para importar em outras ferramentas.
                    </small>
                </div>
            </li>
        </ul>
    </div>
</div>

<!-- Estilos CSS -->
<style>
.export-buttons {
    position: relative;
}

.export-dropdown-btn {
    min-width: 160px;
    font-weight: 500;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.export-dropdown-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.export-dropdown {
    min-width: 320px;
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    border-radius: 12px;
    padding: 8px 0;
    margin-top: 8px;
}

.export-option {
    display: flex;
    align-items: center;
    padding: 4px 0;
    transition: all 0.2s ease;
}

.export-icon {
    width: 40px;
    text-align: center;
    font-size: 1.2rem;
}

.export-info {
    flex: 1;
    margin-left: 8px;
}

.export-title {
    font-weight: 600;
    color: #333;
    margin-bottom: 2px;
}

.export-description {
    font-size: 0.8rem;
    color: #666;
    line-height: 1.2;
}

.export-size {
    margin-left: 8px;
}

.export-item {
    padding: 12px 16px !important;
    border-radius: 8px;
    margin: 2px 8px;
    transition: all 0.2s ease;
    position: relative;
    overflow: hidden;
}

.export-item:hover {
    background-color: #f8f9fa !important;
    transform: translateX(4px);
}

.export-item:hover .export-icon i {
    transform: scale(1.1);
}

.export-item.loading {
    pointer-events: none;
    opacity: 0.7;
}

.export-item.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    right: 16px;
    width: 16px;
    height: 16px;
    margin-top: -8px;
    border: 2px solid #ccc;
    border-top-color: #007bff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

.export-help {
    padding: 8px 16px !important;
    background-color: #f8f9fa;
    margin: 4px 8px;
    border-radius: 8px;
}

.dropdown-header {
    font-size: 0.9rem;
    color: #495057 !important;
    text-transform: none;
    letter-spacing: normal;
    padding: 8px 16px !important;
}

/* Loading states */
.export-btn-loading .export-btn-text::after {
    content: '...';
    animation: dots 1.5s infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

@keyframes dots {
    0%, 20% {
        color: rgba(0,0,0,0);
        text-shadow:
            .25em 0 0 rgba(0,0,0,0),
            .5em 0 0 rgba(0,0,0,0);
    }
    40% {
        color: #333;
        text-shadow:
            .25em 0 0 rgba(0,0,0,0),
            .5em 0 0 rgba(0,0,0,0);
    }
    60% {
        text-shadow:
            .25em 0 0 #333,
            .5em 0 0 rgba(0,0,0,0);
    }
    80%, 100% {
        text-shadow:
            .25em 0 0 #333,
            .5em 0 0 #333;
    }
}

/* Responsive */
@media (max-width: 768px) {
    .export-dropdown {
        min-width: 280px;
    }
    
    .export-dropdown-btn {
        min-width: 140px;
        font-size: 0.9rem;
    }
    
    .export-title {
        font-size: 0.9rem;
    }
    
    .export-description {
        font-size: 0.75rem;
    }
}

/* Toast notification styles */
.export-toast {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #28a745;
    color: white;
    padding: 12px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 9999;
    transform: translateX(100%);
    transition: transform 0.3s ease;
}

.export-toast.show {
    transform: translateX(0);
}

.export-toast.info {
    background: #007bff;
}

.export-toast.success {
    background: #28a745;
}

.export-toast.warning {
    background: #ffc107;
    color: #333;
}
</style>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Função para mostrar toast
    function showExportToast(message, type = 'info') {
        // Remove toast existente
        const existingToast = document.querySelector('.export-toast');
        if (existingToast) {
            existingToast.remove();
        }
        
        // Cria novo toast
        const toast = document.createElement('div');
        toast.className = `export-toast ${type}`;
        toast.innerHTML = `
            <i class="fas fa-download me-2"></i>
            ${message}
        `;
        
        document.body.appendChild(toast);
        
        // Mostra toast
        setTimeout(() => toast.classList.add('show'), 100);
        
        // Remove toast após 4 segundos
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }
    
    // Event listeners para os links de export
    document.querySelectorAll('.export-item').forEach(link => {
        link.addEventListener('click', function(e) {
            const format = this.dataset.format;
            const type = this.dataset.type;
            const item = this;
            
            // Adiciona loading state
            item.classList.add('loading');
            
            // Mostra toast
            showExportToast(`Gerando relatório ${format}...`, 'info');
            
            // Para CSV e Excel, espera um pouco e mostra sucesso
            if (format !== 'PDF') {
                setTimeout(() => {
                    item.classList.remove('loading');
                    showExportToast(`Download do arquivo ${format} iniciado!`, 'success');
                }, 1500);
            } else {
                // Para PDF, remove loading mais rapidamente
                setTimeout(() => {
                    item.classList.remove('loading');
                }, 1000);
            }
            
            // Analytics (se disponível)
            if (typeof gtag !== 'undefined') {
                gtag('event', 'export_dashboard', {
                    'format': format.toLowerCase(),
                    'dashboard_type': type
                });
            }
        });
    });
    
    // Adiciona tooltip aos botões (se Bootstrap tooltips estiver disponível)
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltips.forEach(tooltip => new bootstrap.Tooltip(tooltip));
    }
    
    // Fechar dropdown ao clicar em export
    document.querySelectorAll('.export-item').forEach(item => {
        item.addEventListener('click', function() {
            const dropdown = this.closest('.dropdown-menu');
            if (dropdown) {
                const button = dropdown.previousElementSibling;
                if (button && button.classList.contains('dropdown-toggle')) {
                    // Simula clique no botão para fechar dropdown
                    setTimeout(() => {
                        if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
                            const dropdownInstance = bootstrap.Dropdown.getInstance(button);
                            if (dropdownInstance) {
                                dropdownInstance.hide();
                            }
                        }
                    }, 100);
                }
            }
        });
    });
    
    // Animação do botão principal ao hover
    const dropdownBtn = document.querySelector('.export-dropdown-btn');
    if (dropdownBtn) {
        dropdownBtn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-1px)';
        });
        
        dropdownBtn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    }
});

// Função global para mostrar notificações (compatibilidade)
window.showExportNotification = function(message, type = 'info') {
    if (typeof showToast === 'function') {
        showToast(message, type);
    } else {
        console.log(`Export ${type}: ${message}`);
    }
};
</script>