<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'PetShop')</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        .navbar-brand {
            font-weight: bold;
            color: #4e73df !important;
        }
        
        .nav-link {
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            color: #4e73df !important;
            transform: translateY(-1px);
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
        }
        
        .badge-pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .dropdown-item:hover {
            background-color: #f8f9fc;
            color: #4e73df;
        }
        
        .sidebar {
            background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
        }
        
        .footer {
            background-color: #f8f9fc;
            border-top: 1px solid #e3e6f0;
        }
        
        .text-analytics {
            color: #4e73df !important;
        }
        
        .bg-analytics {
            background-color: #4e73df !important;
        }
        
        .btn-analytics {
            background-color: #4e73df;
            border-color: #4e73df;
            color: white;
        }
        
        .btn-analytics:hover {
            background-color: #2e59d9;
            border-color: #2e59d9;
            color: white;
        }
    </style>
    
    <link href="{{ asset('css/dashboard-styles.css') }}" rel="stylesheet">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <i class="fas fa-paw me-2"></i>
                    {{ config('app.name', 'PetShop') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}">
                                <i class="fas fa-home me-1"></i>Início
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('products.index') }}">
                                <i class="fas fa-shopping-bag me-1"></i>Produtos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('services.index') }}">
                                <i class="fas fa-clipboard-list me-1"></i>Serviços
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('petshops.index') }}">
                                <i class="fas fa-store me-1"></i>Petshops
                            </a>
                        </li>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Carrinho -->
                        @auth
                        <li class="nav-item">
                            <a class="nav-link position-relative" href="{{ route('cart.index') }}">
                                <i class="fas fa-shopping-cart me-1"></i>Carrinho
                                @if(session('cart') && count(session('cart')) > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger badge-pulse">
                                        {{ array_sum(array_column(session('cart'), 'quantity')) }}
                                    </span>
                                @endif
                            </a>
                        </li>
                        @endauth

                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">
                                        <i class="fas fa-sign-in-alt me-1"></i>{{ __('Login') }}
                                    </a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">
                                        <i class="fas fa-user-plus me-1"></i>{{ __('Register') }}
                                    </a>
                                </li>
                            @endif
                        @else
                            <!-- Dashboard Link -->
                            @if(auth()->user()->hasRole('petshop'))
                                <li class="nav-item">
                                    <a class="nav-link text-analytics fw-bold" href="{{ route('analytics.petshop') }}">
                                        <i class="fas fa-chart-bar me-1"></i>Dashboard Analytics
                                    </a>
                                </li>
                            @elseif(auth()->user()->hasRole('employee'))
                                <li class="nav-item">
                                    <a class="nav-link text-analytics fw-bold" href="{{ route('analytics.employee') }}">
                                        <i class="fas fa-chart-line me-1"></i>Meu Dashboard
                                    </a>
                                </li>
                            @elseif(auth()->user()->hasRole('client'))
                                <li class="nav-item">
                                    <a class="nav-link text-analytics fw-bold" href="{{ route('analytics.client') }}">
                                        <i class="fas fa-tachometer-alt me-1"></i>Meu Painel
                                    </a>
                                </li>
                            @elseif(auth()->user()->hasRole('admin'))
                                <li class="nav-item">
                                    <a class="nav-link text-analytics fw-bold" href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-cogs me-1"></i>Admin Dashboard
                                    </a>
                                </li>
                            @endif

                            <!-- Menu do Usuário -->
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <i class="fas fa-user-circle me-1"></i>
                                    {{ Auth::user()->name }}
                                    @if(auth()->user()->hasRole('admin'))
                                        <span class="badge bg-danger ms-1">Admin</span>
                                    @elseif(auth()->user()->hasRole('petshop'))
                                        <span class="badge bg-primary ms-1">Petshop</span>
                                    @elseif(auth()->user()->hasRole('employee'))
                                        <span class="badge bg-info ms-1">Funcionário</span>
                                    @else
                                        <span class="badge bg-success ms-1">Cliente</span>
                                    @endif
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <!-- Links específicos por papel -->
                                    @if(auth()->user()->hasRole('client'))
                                        <a class="dropdown-item" href="{{ route('pets.index') }}">
                                            <i class="fas fa-paw me-2"></i>Meus Pets
                                        </a>
                                        <a class="dropdown-item" href="{{ route('appointments.index') }}">
                                            <i class="fas fa-calendar-alt me-2"></i>Agendamentos
                                        </a>
                                        <a class="dropdown-item" href="{{ route('orders.index') }}">
                                            <i class="fas fa-shopping-bag me-2"></i>Meus Pedidos
                                        </a>
                                        <a class="dropdown-item" href="{{ route('wishlist.index') }}">
                                            <i class="fas fa-heart me-2"></i>Lista de Desejos
                                        </a>
                                        <div class="dropdown-divider"></div>
                                    @endif

                                    @if(auth()->user()->hasRole('petshop'))
                                        <a class="dropdown-item" href="{{ route('petshop.products.index') }}">
                                            <i class="fas fa-box me-2"></i>Produtos
                                        </a>
                                        <a class="dropdown-item" href="{{ route('petshop.services.index') }}">
                                            <i class="fas fa-clipboard-list me-2"></i>Serviços
                                        </a>
                                        <a class="dropdown-item" href="{{ route('petshop.employees.index') }}">
                                            <i class="fas fa-users me-2"></i>Funcionários
                                        </a>
                                        <a class="dropdown-item" href="{{ route('petshop.orders') }}">
                                            <i class="fas fa-shopping-bag me-2"></i>Pedidos
                                        </a>
                                        <a class="dropdown-item" href="{{ route('petshop.appointments') }}">
                                            <i class="fas fa-calendar-check me-2"></i>Agendamentos
                                        </a>
                                        <div class="dropdown-divider"></div>
                                    @endif

                                    @if(auth()->user()->hasRole('employee'))
                                        <a class="dropdown-item" href="{{ route('employee.appointments') }}">
                                            <i class="fas fa-calendar-check me-2"></i>Meus Agendamentos
                                        </a>
                                        <div class="dropdown-divider"></div>
                                    @endif

                                    @if(auth()->user()->hasRole('admin'))
                                        <a class="dropdown-item" href="{{ route('admin.users.index') }}">
                                            <i class="fas fa-users-cog me-2"></i>Gerenciar Usuários
                                        </a>
                                        <a class="dropdown-item" href="{{ route('admin.petshops.index') }}">
                                            <i class="fas fa-store me-2"></i>Gerenciar Petshops
                                        </a>
                                        <div class="dropdown-divider"></div>
                                    @endif

                                    <!-- Links comuns -->
                                    <a class="dropdown-item" href="{{ route('profile.show') }}">
                                        <i class="fas fa-user-edit me-2"></i>Perfil
                                    </a>

                                    <div class="dropdown-divider"></div>

                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>{{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Alertas -->
        <div class="container mt-3">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>

        <!-- Conteúdo Principal -->
        <main class="py-4">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="footer mt-auto py-4">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-paw text-primary me-2"></i>
                            <span class="text-muted">
                                © {{ date('Y') }} {{ config('app.name', 'PetShop') }}. Todos os direitos reservados.
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="d-flex justify-content-end align-items-center">
                            <span class="text-muted me-3">Conecte-se conosco:</span>
                            <a href="#" class="text-muted me-3" title="Facebook">
                                <i class="fab fa-facebook fa-lg"></i>
                            </a>
                            <a href="#" class="text-muted me-3" title="Instagram">
                                <i class="fab fa-instagram fa-lg"></i>
                            </a>
                            <a href="#" class="text-muted" title="WhatsApp">
                                <i class="fab fa-whatsapp fa-lg"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Links úteis -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="border-top pt-3">
                            <div class="row text-center text-md-start">
                                <div class="col-md-3 mb-2">
                                    <h6 class="text-primary">Para Clientes</h6>
                                    <ul class="list-unstyled mb-0">
                                        <li><a href="{{ route('products.index') }}" class="text-muted small text-decoration-none">Produtos</a></li>
                                        <li><a href="{{ route('services.index') }}" class="text-muted small text-decoration-none">Serviços</a></li>
                                        <li><a href="{{ route('petshops.index') }}" class="text-muted small text-decoration-none">Petshops</a></li>
                                    </ul>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <h6 class="text-primary">Minha Conta</h6>
                                    <ul class="list-unstyled mb-0">
                                        @auth
                                            @if(auth()->user()->hasRole('client'))
                                                <li><a href="{{ route('analytics.client') }}" class="text-muted small text-decoration-none">Meu Dashboard</a></li>
                                                <li><a href="{{ route('orders.index') }}" class="text-muted small text-decoration-none">Meus Pedidos</a></li>
                                                <li><a href="{{ route('pets.index') }}" class="text-muted small text-decoration-none">Meus Pets</a></li>
                                            @endif
                                        @else
                                            <li><a href="{{ route('login') }}" class="text-muted small text-decoration-none">Entrar</a></li>
                                            <li><a href="{{ route('register') }}" class="text-muted small text-decoration-none">Cadastrar</a></li>
                                        @endauth
                                    </ul>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <h6 class="text-primary">Suporte</h6>
                                    <ul class="list-unstyled mb-0">
                                        <li><a href="#" class="text-muted small text-decoration-none">Central de Ajuda</a></li>
                                        <li><a href="#" class="text-muted small text-decoration-none">Fale Conosco</a></li>
                                        <li><a href="#" class="text-muted small text-decoration-none">Política de Privacidade</a></li>
                                    </ul>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <h6 class="text-primary">Analytics</h6>
                                    <ul class="list-unstyled mb-0">
                                        @auth
                                            @if(auth()->user()->hasRole('petshop'))
                                                <li><a href="{{ route('analytics.petshop') }}" class="text-muted small text-decoration-none">Dashboard Petshop</a></li>
                                            @elseif(auth()->user()->hasRole('employee'))
                                                <li><a href="{{ route('analytics.employee') }}" class="text-muted small text-decoration-none">Dashboard Funcionário</a></li>
                                            @elseif(auth()->user()->hasRole('admin'))
                                                <li><a href="{{ route('admin.dashboard') }}" class="text-muted small text-decoration-none">Dashboard Admin</a></li>
                                            @endif
                                        @endauth
                                        <li><span class="text-muted small">Powered by Laravel</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- JavaScript customizado -->
    <script>
        // Auto-hide alerts após 5 segundos
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });

            // Smooth scroll para âncoras
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                });
            });

            // Loading spinner para forms
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function() {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        const originalText = submitBtn.innerHTML;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processando...';
                        
                        // Reabilitar após 30 segundos como fallback
                        setTimeout(() => {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }, 30000);
                    }
                });
            });

            // Tooltips do Bootstrap
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Popovers do Bootstrap
            const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });
        });

        // Função global para confirmação de exclusão
        function confirmDelete(message = 'Tem certeza que deseja excluir este item?') {
            return confirm(message);
        }

        // Função para formatar moeda brasileira
        function formatCurrency(value) {
            return new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }).format(value);
        }

        // Função para atualizar carrinho via AJAX
        function updateCartCount() {
            fetch('/api/cart/count')
                .then(response => response.json())
                .then(data => {
                    const badge = document.querySelector('.navbar-nav .badge');
                    if (badge) {
                        badge.textContent = data.count;
                        if (data.count > 0) {
                            badge.classList.add('badge-pulse');
                        } else {
                            badge.classList.remove('badge-pulse');
                        }
                    }
                })
                .catch(error => console.log('Erro ao atualizar carrinho:', error));
        }

        // Atualizar contagem do carrinho a cada 30 segundos
        @auth
        setInterval(updateCartCount, 30000);
        @endauth
    </script>

    <!-- Scripts adicionais das páginas -->
    @stack('scripts')
</body>
</html>