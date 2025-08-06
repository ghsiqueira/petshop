<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - PetShop Online</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @yield('styles')
</head>
<body data-route-name="{{ Route::currentRouteName() }}">
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="{{ route('home') }}">
                    <i class="fas fa-paw me-2"></i>PetShop Online
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Início</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">Produtos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('petshops.*') ? 'active' : '' }}" href="{{ route('petshops.index') }}">Pet Shops</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}" href="{{ route('services.index') }}">Serviços</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav">
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">Cadastro</a>
                            </li>
                        @else
                            <!-- Lista de Desejos -->
                            <li class="nav-item">
                                <a class="nav-link position-relative {{ request()->routeIs('wishlist.*') ? 'active' : '' }}" href="{{ route('wishlist.index') }}">
                                    <i class="fas fa-heart text-danger"></i>
                                    <span class="d-none d-lg-inline ms-1">Lista de Desejos</span>
                                    @if(auth()->user()->wishlists()->count() > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                                              id="wishlist-counter" style="font-size: 0.65rem;">
                                            {{ auth()->user()->wishlists()->count() }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                            
                            <!-- Carrinho -->
                            <li class="nav-item">
                                <a class="nav-link position-relative {{ request()->routeIs('cart.*') ? 'active' : '' }}" href="{{ route('cart.index') }}">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span class="d-none d-lg-inline ms-1">Carrinho</span>
                                    @if(session()->has('cart') && count(session('cart')) > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success" style="font-size: 0.65rem;">
                                            {{ count(session('cart')) }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                            
                            <!-- Menu do Usuário -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user me-1"></i>
                                    {{ Auth::user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('dashboard') }}">
                                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('profile.show') }}">
                                            <i class="fas fa-user me-2"></i>Perfil
                                        </a>
                                    </li>
                                    
                                    @role('client')
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('pets.index') }}">
                                            <i class="fas fa-paw me-2"></i>Meus Pets
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('appointments.index') }}">
                                            <i class="fas fa-calendar me-2"></i>Agendamentos
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('orders.index') }}">
                                            <i class="fas fa-shopping-bag me-2"></i>Pedidos
                                        </a>
                                    </li>
                                    @endrole
                                    
                                    @role('petshop')
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('petshop.dashboard') }}">
                                            <i class="fas fa-store me-2"></i>Área do Pet Shop
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('coupons.index') }}">
                                            <i class="fas fa-tags me-2"></i>Cupons
                                        </a>
                                    </li>
                                    @endrole
                                    
                                    @role('employee')
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('employee.dashboard') }}">
                                            <i class="fas fa-id-card me-2"></i>Área do Funcionário
                                        </a>
                                    </li>
                                    @endrole
                                    
                                    @role('admin')
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.users.index') }}">
                                            <i class="fas fa-user-shield me-2"></i>Área Admin
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('coupons.index') }}">
                                            <i class="fas fa-tags me-2"></i>Cupons
                                        </a>
                                    </li>
                                    @endrole
                                    
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="fas fa-sign-out-alt me-2"></i>Sair
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <div class="container py-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>PetShop Online</h5>
                    <p>Tudo para o seu pet em um só lugar</p>
                </div>
                <div class="col-md-4">
                    <h5>Links Rápidos</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('home') }}" class="text-white">Início</a></li>
                        <li><a href="{{ route('products.index') }}" class="text-white">Produtos</a></li>
                        <li><a href="{{ route('petshops.index') }}" class="text-white">Pet Shops</a></li>
                        @auth
                            <li><a href="{{ route('wishlist.index') }}" class="text-white">Lista de Desejos</a></li>
                        @endauth
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contato</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-envelope me-2"></i> contato@petshoponline.com</li>
                        <li><i class="fas fa-phone me-2"></i> (11) 9999-9999</li>
                    </ul>
                </div>
            </div>
            <hr>
            <div class="text-center">
                &copy; {{ date('Y') }} PetShop Online. Todos os direitos reservados.
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom JS -->
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('scripts')
</body>
</html>