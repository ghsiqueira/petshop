/**
 * ========================================================================== 
 * SISTEMA DE TEMA ESCURO/CLARO
 * ==========================================================================
 */

class ThemeManager {
    constructor() {
        this.currentTheme = this.getStoredTheme() || 'light';
        this.toggleButton = null;
        this.init();
    }

    /**
     * Inicializar o sistema de temas
     */
    init() {
        // Aplicar tema inicial
        this.applyTheme(this.currentTheme);
        
        // Aguardar DOM carregar
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.setupToggleButton();
                this.updateCharts();
            });
        } else {
            this.setupToggleButton();
            this.updateCharts();
        }

        // Detectar mudanças de tema do sistema
        this.detectSystemTheme();
        
        // Listener para mudanças nas preferências do sistema
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!this.getStoredTheme()) {
                this.setTheme(e.matches ? 'dark' : 'light');
            }
        });

        console.log('🎨 ThemeManager inicializado:', this.currentTheme);
    }

    /**
     * Obter tema armazenado
     */
    getStoredTheme() {
        return localStorage.getItem('petshop-theme');
    }

    /**
     * Salvar tema no localStorage
     */
    storeTheme(theme) {
        localStorage.setItem('petshop-theme', theme);
    }

    /**
     * Detectar tema do sistema
     */
    detectSystemTheme() {
        if (!this.getStoredTheme()) {
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            this.currentTheme = systemPrefersDark ? 'dark' : 'light';
            this.applyTheme(this.currentTheme);
        }
    }

    /**
     * Aplicar tema ao documento
     */
    applyTheme(theme) {
        const html = document.documentElement;
        
        // Remover classes de tema existentes
        html.removeAttribute('data-theme');
        
        // Aplicar novo tema
        if (theme === 'dark') {
            html.setAttribute('data-theme', 'dark');
        }
        
        this.currentTheme = theme;
        this.storeTheme(theme);
        
        // Atualizar estado do toggle
        this.updateToggleState();
        
        // Atualizar gráficos se existirem
        setTimeout(() => {
            this.updateCharts();
        }, 100);

        // Disparar evento customizado
        window.dispatchEvent(new CustomEvent('themeChanged', { 
            detail: { theme: theme } 
        }));

        console.log('🎨 Tema aplicado:', theme);
    }

    /**
     * Alternar entre temas
     */
    toggleTheme() {
        const newTheme = this.currentTheme === 'light' ? 'dark' : 'light';
        this.setTheme(newTheme);
        
        // Feedback visual
        this.showThemeChangeFeedback(newTheme);
    }

    /**
     * Definir tema específico
     */
    setTheme(theme) {
        if (theme !== 'light' && theme !== 'dark') {
            console.warn('⚠️ Tema inválido:', theme);
            return;
        }
        
        this.applyTheme(theme);
    }

    /**
     * Obter tema atual
     */
    getCurrentTheme() {
        return this.currentTheme;
    }

    /**
     * Verificar se está no modo escuro
     */
    isDarkMode() {
        return this.currentTheme === 'dark';
    }

    /**
     * Configurar botão de toggle
     */
    setupToggleButton() {
        this.toggleButton = document.getElementById('theme-toggle');
        
        if (this.toggleButton) {
            this.toggleButton.addEventListener('change', () => {
                this.toggleTheme();
            });
            
            this.updateToggleState();
            console.log('🔘 Toggle button configurado');
        } else {
            console.warn('⚠️ Toggle button não encontrado');
        }
    }

    /**
     * Atualizar estado do toggle
     */
    updateToggleState() {
        if (this.toggleButton) {
            this.toggleButton.checked = this.currentTheme === 'dark';
        }
    }

    /**
     * Atualizar gráficos Chart.js para novo tema
     */
    updateCharts() {
        if (typeof Chart === 'undefined') return;

        const isDark = this.isDarkMode();
        
        // Configurações para tema escuro/claro
        const chartConfig = {
            dark: {
                gridColor: '#3a3f5c',
                textColor: '#b7bbc8',
                tooltipBg: '#242940',
                tooltipBorder: '#3a3f5c'
            },
            light: {
                gridColor: '#e3e6f0',
                textColor: '#858796',
                tooltipBg: '#ffffff',
                tooltipBorder: '#e3e6f0'
            }
        };

        const config = chartConfig[this.currentTheme];

        // Atualizar configurações padrão do Chart.js
        Chart.defaults.color = config.textColor;
        Chart.defaults.borderColor = config.gridColor;
        Chart.defaults.backgroundColor = config.tooltipBg;

        // Atualizar todos os gráficos existentes
        Chart.instances.forEach(chart => {
            if (chart.options.scales) {
                // Atualizar scales
                Object.keys(chart.options.scales).forEach(scaleId => {
                    const scale = chart.options.scales[scaleId];
                    if (scale.grid) {
                        scale.grid.color = config.gridColor;
                    }
                    if (scale.ticks) {
                        scale.ticks.color = config.textColor;
                    }
                    if (scale.title) {
                        scale.title.color = config.textColor;
                    }
                });
            }

            // Atualizar plugins
            if (chart.options.plugins) {
                if (chart.options.plugins.legend && chart.options.plugins.legend.labels) {
                    chart.options.plugins.legend.labels.color = config.textColor;
                }
                
                if (chart.options.plugins.tooltip) {
                    chart.options.plugins.tooltip.backgroundColor = config.tooltipBg;
                    chart.options.plugins.tooltip.borderColor = config.tooltipBorder;
                    chart.options.plugins.tooltip.titleColor = config.textColor;
                    chart.options.plugins.tooltip.bodyColor = config.textColor;
                }
            }

            // Redesenhar gráfico
            chart.update('none');
        });

        console.log('📊 Gráficos atualizados para tema:', this.currentTheme);
    }

    /**
     * Mostrar feedback visual da mudança de tema
     */
    showThemeChangeFeedback(newTheme) {
        // Criar elemento de feedback
        const feedback = document.createElement('div');
        feedback.className = 'theme-change-feedback';
        feedback.innerHTML = `
            <div class="theme-change-content">
                <i class="fas fa-${newTheme === 'dark' ? 'moon' : 'sun'}"></i>
                <span>Tema ${newTheme === 'dark' ? 'Escuro' : 'Claro'} ativado</span>
            </div>
        `;
        
        // Estilos inline para o feedback
        Object.assign(feedback.style, {
            position: 'fixed',
            top: '20px',
            right: '20px',
            backgroundColor: newTheme === 'dark' ? '#242940' : '#ffffff',
            color: newTheme === 'dark' ? '#e3e6f0' : '#5a5c69',
            padding: '12px 20px',
            borderRadius: '8px',
            boxShadow: '0 4px 12px rgba(0,0,0,0.15)',
            zIndex: '9999',
            border: `1px solid ${newTheme === 'dark' ? '#3a3f5c' : '#e3e6f0'}`,
            transform: 'translateX(100%)',
            transition: 'transform 0.3s ease-in-out',
            fontFamily: 'inherit',
            fontSize: '14px',
            display: 'flex',
            alignItems: 'center',
            gap: '8px'
        });

        feedback.querySelector('.theme-change-content').style.cssText = `
            display: flex;
            align-items: center;
            gap: 8px;
        `;

        document.body.appendChild(feedback);

        // Animar entrada
        setTimeout(() => {
            feedback.style.transform = 'translateX(0)';
        }, 10);

        // Remover após 3 segundos
        setTimeout(() => {
            feedback.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (feedback.parentNode) {
                    feedback.parentNode.removeChild(feedback);
                }
            }, 300);
        }, 3000);
    }

    /**
     * Obter cores CSS personalizadas para o tema atual
     */
    getThemeColors() {
        const style = getComputedStyle(document.documentElement);
        
        return {
            primary: style.getPropertyValue('--primary-color').trim(),
            secondary: style.getPropertyValue('--secondary-color').trim(),
            success: style.getPropertyValue('--success-color').trim(),
            info: style.getPropertyValue('--info-color').trim(),
            warning: style.getPropertyValue('--warning-color').trim(),
            danger: style.getPropertyValue('--danger-color').trim(),
            bgPrimary: style.getPropertyValue('--bg-primary').trim(),
            bgSecondary: style.getPropertyValue('--bg-secondary').trim(),
            textPrimary: style.getPropertyValue('--text-primary').trim(),
            textSecondary: style.getPropertyValue('--text-secondary').trim(),
            borderColor: style.getPropertyValue('--border-color').trim()
        };
    }

    /**
     * Aplicar tema a elementos específicos
     */
    applyThemeToElement(element, classes = {}) {
        if (!element) return;

        const themeClasses = {
            light: classes.light || [],
            dark: classes.dark || []
        };

        // Remover classes de ambos os temas
        element.classList.remove(...themeClasses.light, ...themeClasses.dark);
        
        // Aplicar classes do tema atual
        element.classList.add(...themeClasses[this.currentTheme]);
    }

    /**
     * Resetar tema para padrão
     */
    resetTheme() {
        localStorage.removeItem('petshop-theme');
        this.detectSystemTheme();
        console.log('🔄 Tema resetado para padrão do sistema');
    }

    /**
     * Exportar configurações de tema
     */
    exportThemeConfig() {
        return {
            currentTheme: this.currentTheme,
            storedTheme: this.getStoredTheme(),
            systemPrefersDark: window.matchMedia('(prefers-color-scheme: dark)').matches,
            colors: this.getThemeColors()
        };
    }

    /**
     * Debug - imprimir informações do tema
     */
    debug() {
        console.table(this.exportThemeConfig());
    }
}

/**
 * ==========================================================================
 * UTILITÁRIOS E HELPERS
 * ==========================================================================
 */

/**
 * Aguardar elemento aparecer no DOM
 */
function waitForElement(selector, timeout = 5000) {
    return new Promise((resolve, reject) => {
        const element = document.querySelector(selector);
        if (element) {
            resolve(element);
            return;
        }

        const observer = new MutationObserver((mutations, obs) => {
            const element = document.querySelector(selector);
            if (element) {
                obs.disconnect();
                resolve(element);
            }
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });

        setTimeout(() => {
            observer.disconnect();
            reject(new Error(`Elemento ${selector} não encontrado em ${timeout}ms`));
        }, timeout);
    });
}

/**
 * Aplicar tema a gráficos Chart.js específicos
 */
function updateChartTheme(chartInstance, theme = 'light') {
    if (!chartInstance || typeof Chart === 'undefined') return;

    const isDark = theme === 'dark';
    const gridColor = isDark ? '#3a3f5c' : '#e3e6f0';
    const textColor = isDark ? '#b7bbc8' : '#858796';

    // Atualizar configurações do gráfico
    if (chartInstance.options.scales) {
        Object.values(chartInstance.options.scales).forEach(scale => {
            if (scale.grid) scale.grid.color = gridColor;
            if (scale.ticks) scale.ticks.color = textColor;
            if (scale.title) scale.title.color = textColor;
        });
    }

    if (chartInstance.options.plugins && chartInstance.options.plugins.legend) {
        chartInstance.options.plugins.legend.labels.color = textColor;
    }

    chartInstance.update('none');
}

/**
 * ==========================================================================
 * INICIALIZAÇÃO GLOBAL
 * ==========================================================================
 */

// Instância global do gerenciador de temas
let themeManager;

// Inicializar assim que o script carregar
(function() {
    themeManager = new ThemeManager();
    
    // Expor globalmente para debug
    window.themeManager = themeManager;
    window.updateChartTheme = updateChartTheme;
    
    // Compatibilidade com jQuery se disponível
    if (typeof $ !== 'undefined') {
        $(document).ready(function() {
            console.log('📱 Tema jQuery ready');
        });
    }
})();

/**
 * ==========================================================================
 * EVENTOS CUSTOMIZADOS
 * ==========================================================================
 */

// Evento disparado quando tema muda
window.addEventListener('themeChanged', function(e) {
    const { theme } = e.detail;
    console.log('🔄 Evento themeChanged:', theme);
    
    // Aqui você pode adicionar lógica customizada para mudança de tema
    // Por exemplo: atualizar componentes específicos, fazer requests AJAX, etc.
});

// Evento disparado quando página carrega
window.addEventListener('load', function() {
    console.log('🌟 Página carregada com tema:', themeManager.getCurrentTheme());
});

/**
 * ==========================================================================
 * FUNÇÕES AUXILIARES GLOBAIS
 * ==========================================================================
 */

/**
 * Alternar tema (função global)
 */
function toggleTheme() {
    if (themeManager) {
        themeManager.toggleTheme();
    }
}

/**
 * Definir tema específico (função global)
 */
function setTheme(theme) {
    if (themeManager) {
        themeManager.setTheme(theme);
    }
}

/**
 * Obter tema atual (função global)
 */
function getCurrentTheme() {
    return themeManager ? themeManager.getCurrentTheme() : 'light';
}

/**
 * Verificar se está no modo escuro (função global)
 */
function isDarkMode() {
    return themeManager ? themeManager.isDarkMode() : false;
}

/**
 * Obter cores do tema atual (função global)
 */
function getThemeColors() {
    return themeManager ? themeManager.getThemeColors() : {};
}

// Exportar para módulos ES6 se suportado
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        ThemeManager,
        toggleTheme,
        setTheme,
        getCurrentTheme,
        isDarkMode,
        getThemeColors,
        updateChartTheme
    };
}