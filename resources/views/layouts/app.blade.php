<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Petshop') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Theme CSS (deve vir depois do Bootstrap) -->
    <link href="{{ asset('css/theme.css') }}" rel="stylesheet">

    <!-- Estilos customizados da página -->
    @stack('styles')

    <!-- Meta tags para tema -->
    <meta name="theme-color" content="#4e73df">
    <meta name="color-scheme" content="light dark">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    <!-- Preload do script de tema para evitar flash -->
    <script>
        // Script inline para aplicar tema antes do DOM carregar (evita flash)
        (function() {
            const savedTheme = localStorage.getItem('petshop-theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = savedTheme || (systemPrefersDark ? 'dark' : 'light');
            
            if (theme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();
    </script>
</head>
<body>
    <div id="app">
        <!-- Navbar Principal -->
        <nav class="navbar navbar-expand-md navbar-light shadow-sm">
            <div class="container">
                <!-- Logo/Brand -->
                <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                    <i class="fas fa-paw me-2 text-primary"></i>
                    <strong>{{ config('app.name', 'Petshop') }}</strong>
                </a>

                <!-- Botão Mobile -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Menu Principal -->
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Links da Esquerda -->
                    <ul class="navbar-nav me-auto">
                        @auth
                            @if(auth()->user()->hasRole('admin'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                                    </a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                        <i class="fas fa-cog me-1"></i>Administração
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('admin.users.index') }}">
                                            <i class="fas fa-users me-2"></i>Usuários
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.petshops.index') }}">
                                            <i class="fas fa-store me-2"></i>Pet Shops
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.roles.index') }}">
                                            <i class="fas fa-user-tag me-2"></i>Papéis
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="{{ route('coupons.index') }}">
                                            <i class="fas fa-ticket-alt me-2"></i>Cupons
                                        </a></li>
                                    </ul>
                                </li>
                            @elseif(auth()->user()->hasRole('petshop'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('analytics.petshop') }}">
                                        <i class="fas fa-chart-line me-1"></i>Dashboard
                                    </a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                        <i class="fas fa-store me-1"></i>Meu Pet Shop
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('petshop.products.index') }}">
                                            <i class="fas fa-box me-2"></i>Produtos
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('petshop.services.index') }}">
                                            <i class="fas fa-cut me-2"></i>Serviços
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('petshop.employees.index') }}">
                                            <i class="fas fa-users me-2"></i>Funcionários
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="{{ route('petshop.orders') }}">
                                            <i class="fas fa-shopping-bag me-2"></i>Pedidos
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('petshop.appointments') }}">
                                            <i class="fas fa-calendar me-2"></i>Agendamentos
                                        </a></li>
                                    </ul>
                                </li>
                            @elseif(auth()->user()->hasRole('employee'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('analytics.employee') }}">
                                        <i class="fas fa-user-md me-1"></i>Meu Dashboard
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('employee.appointments') }}">
                                        <i class="fas fa-calendar-check me-1"></i>Agendamentos
                                    </a>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('analytics.client') }}">
                                        <i class="fas fa-home me-1"></i>Dashboard
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('products.index') }}">
                                        <i class="fas fa-shopping-bag me-1"></i>Produtos
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('services.index') }}">
                                        <i class="fas fa-cut me-1"></i>Serviços
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('petshops.index') }}">
                                        <i class="fas fa-store me-1"></i>Pet Shops
                                    </a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('products.index') }}">
                                    <i class="fas fa-shopping-bag me-1"></i>Produtos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('services.index') }}">
                                    <i class="fas fa-cut me-1"></i>Serviços
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('petshops.index') }}">
                                    <i class="fas fa-store me-1"></i>Pet Shops
                                </a>
                            </li>
                        @endauth
                    </ul>

                    <!-- Links da Direita -->
                    <ul class="navbar-nav ms-auto align-items-center">
                        <!-- Toggle de Tema -->
                        <li class="nav-item me-3">
                            <div class="theme-toggle-container d-flex align-items-center">
                                <span class="me-2 small text-muted d-none d-md-inline">Tema</span>
                                <label class="theme-toggle" for="theme-toggle" title="Alternar tema claro/escuro">
                                    <input type="checkbox" id="theme-toggle">
                                    <span class="theme-toggle-slider"></span>
                                </label>
                            </div>
                        </li>

                        @auth
                            <!-- Carrinho (só para clientes) -->
                            @if(auth()->user()->hasRole('client'))
                                <li class="nav-item me-2">
                                    <a class="nav-link position-relative" href="{{ route('cart.index') }}" title="Carrinho">
                                        <i class="fas fa-shopping-cart"></i>
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                                              id="cart-count" style="font-size: 0.7rem;">
                                            {{ session('cart') ? count(session('cart')) : 0 }}
                                        </span>
                                    </a>
                                </li>

                                <!-- Wishlist -->
                                <li class="nav-item me-2">
                                    <a class="nav-link" href="{{ route('wishlist.index') }}" title="Lista de Desejos">
                                        <i class="fas fa-heart"></i>
                                    </a>
                                </li>
                            @endif

                            <!-- Notificações (placeholder para futuro) -->
                            <li class="nav-item dropdown me-2">
                                <a class="nav-link position-relative" href="#" data-bs-toggle="dropdown" title="Notificações">
                                    <i class="fas fa-bell"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning" 
                                          style="font-size: 0.7rem;">3</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" style="width: 300px;">
                                    <li class="dropdown-header">
                                        <i class="fas fa-bell me-2"></i>Notificações
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li class="dropdown-item-text">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-info-circle text-info"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <div class="small"><strong>Sistema de notificações</strong></div>
                                                <div class="small text-muted">Em breve disponível!</div>
                                                <div class="small text-muted">há 1 minuto</div>
                                            </div>
                                        </div>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li class="text-center">
                                        <a class="dropdown-item small" href="#">Ver todas</a>
                                    </li>
                                </ul>
                            </li>

                            <!-- Menu do Usuário -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                                    <div class="me-2">
                                        @if(auth()->user()->profile_photo)
                                            <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" 
                                                 alt="{{ auth()->user()->name }}" 
                                                 class="rounded-circle"
                                                 style="width: 32px; height: 32px; object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                                 style="width: 32px; height: 32px; font-size: 14px;">
                                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li class="dropdown-header">
                                        <div class="text-center">
                                            @if(auth()->user()->profile_photo)
                                                <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" 
                                                     alt="{{ auth()->user()->name }}" 
                                                     class="rounded-circle mb-2"
                                                     style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-2"
                                                     style="width: 60px; height: 60px; font-size: 24px;">
                                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div><strong>{{ auth()->user()->name }}</strong></div>
                                            <div class="small text-muted">{{ auth()->user()->email }}</div>
                                            @if(auth()->user()->roles->isNotEmpty())
                                                <span class="badge bg-secondary mt-1">
                                                    {{ ucfirst(auth()->user()->roles->first()->name) }}
                                                </span>
                                            @endif
                                        </div>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    
                                    <li><a class="dropdown-item" href="{{ route('profile.show') }}">
                                        <i class="fas fa-user me-2"></i>Meu Perfil
                                    </a></li>
                                    
                                    @if(auth()->user()->hasRole('client'))
                                        <li><a class="dropdown-item" href="{{ route('pets.index') }}">
                                            <i class="fas fa-paw me-2"></i>Meus Pets
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('orders.index') }}">
                                            <i class="fas fa-shopping-bag me-2"></i>Meus Pedidos
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('appointments.index') }}">
                                            <i class="fas fa-calendar me-2"></i>Agendamentos
                                        </a></li>
                                    @endif

                                    <li><hr class="dropdown-divider"></li>
                                    
                                    <li><a class="dropdown-item" href="#" onclick="themeManager.resetTheme()">
                                        <i class="fas fa-palette me-2"></i>Resetar Tema
                                    </a></li>
                                    
                                    <li><hr class="dropdown-divider"></li>
                                    
                                    <li>
                                        <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="fas fa-sign-out-alt me-2"></i>Sair
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @else
                            <!-- Links para usuários não autenticados -->
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">
                                    <i class="fas fa-sign-in-alt me-1"></i>Entrar
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">
                                    <i class="fas fa-user-plus me-1"></i>Cadastrar
                                </a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Breadcrumb (opcional) -->
        @hasSection('breadcrumb')
            <nav aria-label="breadcrumb" class="bg-light">
                <div class="container">
                    <ol class="breadcrumb py-2 mb-0">
                        @yield('breadcrumb')
                    </ol>
                </div>
            </nav>
        @endif

        <!-- Alertas -->
        @if(session('success') || session('error') || session('warning') || session('info'))
            <div class="container mt-3">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            </div>
        @endif

        <!-- Conteúdo Principal -->
        <main class="py-4">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="mt-5 py-4 border-top">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-paw me-2 text-primary"></i>
                            <strong>{{ config('app.name', 'Petshop') }}</strong>
                        </div>
                        <p class="text-muted small">
                            Sistema completo de gestão para pet shops e clínicas veterinárias.
                            Desenvolvido com ❤️ para o bem-estar dos pets.
                        </p>
                    </div>
                    <div class="col-md-3">
                        <h6>Links Úteis</h6>
                        <ul class="list-unstyled">
                            <li><a href="{{ route('products.index') }}" class="text-muted small text-decoration-none">Produtos</a></li>
                            <li><a href="{{ route('services.index') }}" class="text-muted small text-decoration-none">Serviços</a></li>
                            <li><a href="{{ route('petshops.index') }}" class="text-muted small text-decoration-none">Pet Shops</a></li>
                            @auth
                                @if(auth()->user()->hasRole('client'))
                                    <li><a href="{{ route('pets.index') }}" class="text-muted small text-decoration-none">Meus Pets</a></li>
                                @endif
                            @endauth
                        </ul>
                    </div>
                    <div class="col-md-3">
                        <h6>Suporte</h6>
                        <ul class="list-unstyled">
                            <li><a href="#" class="text-muted small text-decoration-none">Central de Ajuda</a></li>
                            <li><a href="#" class="text-muted small text-decoration-none">Contato</a></li>
                            <li><a href="#" class="text-muted small text-decoration-none">Termos de Uso</a></li>
                            <li><a href="#" class="text-muted small text-decoration-none">Política de Privacidade</a></li>
                        </ul>
                    </div>
                </div>
                <hr class="my-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="text-muted small mb-0">
                            © {{ date('Y') }} {{ config('app.name', 'Petshop') }}. Todos os direitos reservados.
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="d-flex justify-content-md-end align-items-center">
                            <span class="text-muted small me-3">Tema atual:</span>
                            <span class="badge bg-primary" id="current-theme-indicator">Claro</span>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Chart.js (se necessário) -->
    @stack('chart-js')
    
    <!-- Theme Manager -->
    <script src="{{ asset('js/theme.js') }}"></script>

    <!-- Scripts customizados da página -->
    @stack('scripts')

    <!-- Script para atualizar indicador de tema -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Atualizar indicador de tema
            function updateThemeIndicator() {
                const indicator = document.getElementById('current-theme-indicator');
                if (indicator && window.themeManager) {
                    const currentTheme = window.themeManager.getCurrentTheme();
                    indicator.textContent = currentTheme === 'dark' ? 'Escuro' : 'Claro';
                    indicator.className = 'badge bg-' + (currentTheme === 'dark' ? 'dark' : 'primary');
                }
            }

            // Atualizar no carregamento
            updateThemeIndicator();

            // Escutar mudanças de tema
            window.addEventListener('themeChanged', updateThemeIndicator);

            // Auto-hide alerts após 5 segundos
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);

            // Atualizar contador do carrinho via AJAX (para clientes)
            @auth
                @if(auth()->user()->hasRole('client'))
                    function updateCartCount() {
                        fetch('{{ route("cart.index") }}', {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            const cartCount = document.getElementById('cart-count');
                            if (cartCount && data.count !== undefined) {
                                cartCount.textContent = data.count;
                                cartCount.style.display = data.count > 0 ? 'inline' : 'none';
                            }
                        })
                        .catch(error => {
                            console.log('Erro ao atualizar contador do carrinho:', error);
                        });
                    }

                    // Atualizar contador a cada 30 segundos
                    setInterval(updateCartCount, 30000);
                @endif
            @endauth

            // Smooth scroll para links âncora
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Adicionar classe para animações de entrada
            const animatedElements = document.querySelectorAll('.card, .alert, .btn');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            });

            animatedElements.forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(el);
            });

            console.log('🚀 Layout carregado com sucesso!');
        });

        // Função global para mostrar notificação toast
        window.showToast = function(message, type = 'success') {
            const toastContainer = document.getElementById('toast-container') || createToastContainer();
            
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type} border-0`;
            toast.setAttribute('role', 'alert');
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-${getToastIcon(type)} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            
            toastContainer.appendChild(toast);
            
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            
            toast.addEventListener('hidden.bs.toast', () => {
                toast.remove();
            });
        };

        function createToastContainer() {
            const container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
            return container;
        }

        function getToastIcon(type) {
            const icons = {
                success: 'check-circle',
                error: 'exclamation-circle',
                warning: 'exclamation-triangle',
                info: 'info-circle'
            };
            return icons[type] || 'info-circle';
        }

        // Debug do tema (remover em produção)
        @if(config('app.debug'))
            console.log('🎨 Tema Debug ativo');
            window.debugTheme = function() {
                if (window.themeManager) {
                    window.themeManager.debug();
                }
            };
        @endif
    </script>

    <!-- Google Analytics ou outros trackers -->
    @production
        <!-- Adicionar Google Analytics ou outros scripts de produção aqui -->
    @endproduction
</body>
</html>