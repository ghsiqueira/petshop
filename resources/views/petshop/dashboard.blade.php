@extends('layouts.app')

@section('title', 'Dashboard do Petshop')

@push('styles')
<style>
    :root {
        --primary-color: #4f46e5;
        --primary-light: #6366f1;
        --primary-dark: #3730a3;
        --secondary-color: #10b981;
        --secondary-light: #34d399;
        --accent-color: #f59e0b;
        --accent-light: #fbbf24;
        --danger-color: #ef4444;
        --warning-color: #f59e0b;
        --info-color: #3b82f6;
        --success-color: #10b981;
        --dark-color: #1f2937;
        --light-color: #f8fafc;
        --border-color: #e5e7eb;
        --text-muted: #6b7280;
        --sidebar-bg: #1e293b;
        --sidebar-hover: #334155;
    }

    .sidebar {
        background: var(--sidebar-bg);
        min-height: calc(100vh - 76px);
        color: white;
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
    }
    
    .sidebar .nav-link {
        color: #cbd5e1;
        padding: 12px 20px;
        border-radius: 8px;
        margin: 4px 8px;
        transition: all 0.2s ease;
        font-weight: 500;
        position: relative;
    }
    
    .sidebar .nav-link:hover {
        background: var(--sidebar-hover);
        color: white;
        transform: translateX(4px);
    }
    
    .sidebar .nav-link.active {
        background: var(--primary-color);
        color: white;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }
    
    .sidebar .nav-link.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 4px;
        height: 20px;
        background: white;
        border-radius: 0 4px 4px 0;
    }

    .main-content {
        background: var(--light-color);
        min-height: calc(100vh - 76px);
    }

    .page-header {
        background: white;
        border-radius: 16px;
        padding: 32px;
        margin-bottom: 32px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border-left: 4px solid var(--primary-color);
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
        overflow: hidden;
        position: relative;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--accent-color);
    }

    .stat-card.products::before {
        background: var(--primary-color);
    }

    .stat-card.services::before {
        background: var(--secondary-color);
    }

    .stat-card.employees::before {
        background: var(--info-color);
    }

    .stat-card.appointments::before {
        background: var(--warning-color);
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .stat-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 16px;
    }

    .stat-icon.products {
        background: rgba(79, 70, 229, 0.1);
        color: var(--primary-color);
    }

    .stat-icon.services {
        background: rgba(16, 185, 129, 0.1);
        color: var(--secondary-color);
    }

    .stat-icon.employees {
        background: rgba(59, 130, 246, 0.1);
        color: var(--info-color);
    }

    .stat-icon.appointments {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning-color);
    }

    .btn-modern {
        border-radius: 8px;
        padding: 12px 24px;
        font-weight: 600;
        transition: all 0.2s ease;
        border: none;
        position: relative;
        overflow: hidden;
    }

    .btn-primary-modern {
        background: var(--primary-color);
        color: white;
    }

    .btn-primary-modern:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
        color: white;
    }

    .btn-success-modern {
        background: var(--secondary-color);
        color: white;
    }

    .btn-success-modern:hover {
        background: #059669;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        color: white;
    }

    .btn-outline-modern {
        background: white;
        border: 2px solid var(--border-color);
        color: var(--dark-color);
    }

    .btn-outline-modern:hover {
        border-color: var(--primary-color);
        color: var(--primary-color);
        transform: translateY(-2px);
    }

    .card-modern {
        background: white;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
    }

    .card-modern:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .card-header-modern {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        color: white;
        border-radius: 16px 16px 0 0;
        padding: 20px 24px;
        border-bottom: none;
    }

    .table-modern {
        border: none;
    }

    .table-modern thead th {
        background: #f8fafc;
        border: none;
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        padding: 16px;
    }

    .table-modern tbody tr {
        border-bottom: 1px solid var(--border-color);
        transition: all 0.2s ease;
    }

    .table-modern tbody tr:hover {
        background: #f8fafc;
    }

    .table-modern tbody td {
        padding: 16px;
        border: none;
        vertical-align: middle;
    }

    .badge-modern {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    .badge-success-modern {
        background: rgba(16, 185, 129, 0.1);
        color: var(--secondary-color);
    }

    .badge-warning-modern {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning-color);
    }

    .badge-danger-modern {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger-color);
    }

    .badge-secondary-modern {
        background: rgba(107, 114, 128, 0.1);
        color: var(--text-muted);
    }

    .product-image {
        width: 64px;
        height: 64px;
        object-fit: cover;
        border-radius: 12px;
        border: 2px solid var(--border-color);
    }

    .employee-avatar {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: var(--primary-color);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .section-title {
        color: var(--dark-color);
        font-weight: 700;
        margin-bottom: 8px;
    }

    .section-subtitle {
        color: var(--text-muted);
        font-size: 0.95rem;
    }

    .search-box {
        border-radius: 12px;
        border: 2px solid var(--border-color);
        padding: 12px 16px;
        transition: all 0.2s ease;
    }

    .search-box:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        outline: none;
    }

    .filter-group .btn {
        border-radius: 8px;
        border: 2px solid var(--border-color);
        background: white;
        color: var(--text-muted);
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .filter-group .btn.active,
    .filter-group .btn:hover {
        border-color: var(--primary-color);
        background: var(--primary-color);
        color: white;
    }

    .activity-card {
        background: white;
        border-radius: 16px;
        border: 1px solid var(--border-color);
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .activity-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color);
        background: #f8fafc;
        border-radius: 16px 16px 0 0;
    }

    .activity-item {
        padding: 16px 24px;
        border-bottom: 1px solid var(--border-color);
        transition: all 0.2s ease;
    }

    .activity-item:hover {
        background: #f8fafc;
    }

    .activity-item:last-child {
        border-bottom: none;
        border-radius: 0 0 16px 16px;
    }

    /* Logo container */
    .logo-container {
        position: relative;
        display: inline-block;
    }

    .logo-container img,
    .logo-container .logo-placeholder {
        border: 3px solid rgba(255,255,255,0.2);
        transition: all 0.3s ease;
    }

    .logo-container:hover img,
    .logo-container:hover .logo-placeholder {
        border-color: rgba(255,255,255,0.4);
        transform: scale(1.05);
    }

    /* Form styling */
    .form-control-modern {
        border-radius: 8px;
        border: 2px solid var(--border-color);
        padding: 12px 16px;
        transition: all 0.2s ease;
    }

    .form-control-modern:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .sidebar {
            min-height: auto;
        }
        .main-content {
            min-height: auto;
        }
        .page-header {
            padding: 24px;
            text-align: center;
        }
        .page-header .d-flex {
            flex-direction: column;
            gap: 16px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0">
    <div class="row g-0">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 sidebar p-0">
            <div class="p-4">
                <div class="text-center mb-4">
                    <div class="logo-container">
                        @if($petshop->logo)
                            <img src="{{ asset('storage/' . $petshop->logo) }}" 
                                 alt="Logo do {{ $petshop->name }}" class="rounded-circle" width="80">
                        @else
                            <div class="logo-placeholder rounded-circle bg-white text-primary d-inline-flex align-items-center justify-content-center" 
                                 style="width: 80px; height: 80px; font-size: 1.8rem; font-weight: bold;">
                                {{ strtoupper(substr($petshop->name, 0, 2)) }}
                            </div>
                        @endif
                    </div>
                    <h6 class="mt-3 mb-1 text-white">{{ $petshop->name }}</h6>
                    <small class="text-slate-400">{{ Str::limit($petshop->address, 30) }}</small>
                </div>
            </div>
            
            <nav class="nav flex-column px-2 pb-4">
                <a class="nav-link active" href="#dashboard" onclick="showSection('dashboard')">
                    <i class="fas fa-chart-bar me-3"></i>Dashboard
                </a>
                <a class="nav-link" href="#products" onclick="showSection('products')">
                    <i class="fas fa-box me-3"></i>Produtos
                </a>
                <a class="nav-link" href="#services" onclick="showSection('services')">
                    <i class="fas fa-scissors me-3"></i>Serviços
                </a>
                <a class="nav-link" href="#employees" onclick="showSection('employees')">
                    <i class="fas fa-user-tie me-3"></i>Funcionários
                </a>
                <a class="nav-link" href="#profile" onclick="showSection('profile')">
                    <i class="fas fa-store me-3"></i>Perfil do Petshop
                </a>
                <a class="nav-link" href="{{ route('petshop.orders') }}">
                    <i class="fas fa-shopping-cart me-3"></i>Pedidos
                </a>
                <a class="nav-link" href="{{ route('petshop.appointments') }}">
                    <i class="fas fa-calendar me-3"></i>Agendamentos
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 main-content p-4">
            <!-- Dashboard Section -->
            <div id="dashboard-section" class="content-section">
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="section-title mb-2">Dashboard do Petshop</h2>
                            <p class="section-subtitle mb-0">Gerencie seus produtos, serviços e informações</p>
                        </div>
                        <div class="d-flex gap-3">
                            <a href="{{ route('analytics.petshop') }}" class="btn btn-success-modern btn-modern">
                                <i class="fas fa-chart-line me-2"></i>Análises Avançadas
                            </a>
                            <button class="btn btn-outline-modern btn-modern" onclick="showSection('profile')">
                                <i class="fas fa-edit me-2"></i>Editar Informações
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-5">
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="stat-card products">
                            <div class="card-body text-center p-4">
                                <div class="stat-icon products mx-auto">
                                    <i class="fas fa-box"></i>
                                </div>
                                <h3 class="fw-bold text-dark mb-2">{{ $productCount }}</h3>
                                <h6 class="text-muted mb-3">Produtos</h6>
                                <button class="btn btn-sm btn-outline-modern" onclick="showSection('products')">
                                    Ver Todos
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="stat-card services">
                            <div class="card-body text-center p-4">
                                <div class="stat-icon services mx-auto">
                                    <i class="fas fa-scissors"></i>
                                </div>
                                <h3 class="fw-bold text-dark mb-2">{{ $serviceCount }}</h3>
                                <h6 class="text-muted mb-3">Serviços</h6>
                                <button class="btn btn-sm btn-outline-modern" onclick="showSection('services')">
                                    Ver Todos
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="stat-card employees">
                            <div class="card-body text-center p-4">
                                <div class="stat-icon employees mx-auto">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <h3 class="fw-bold text-dark mb-2">{{ $employeeCount }}</h3>
                                <h6 class="text-muted mb-3">Funcionários</h6>
                                <button class="btn btn-sm btn-outline-modern" onclick="showSection('employees')">
                                    Ver Todos
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="stat-card appointments">
                            <div class="card-body text-center p-4">
                                <div class="stat-icon appointments mx-auto">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <h3 class="fw-bold text-dark mb-2">{{ $pendingAppointments }}</h3>
                                <h6 class="text-muted mb-3">Agendamentos Pendentes</h6>
                                <a href="{{ route('petshop.appointments') }}" class="btn btn-sm btn-outline-modern">
                                    Ver Todos
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="activity-card">
                            <div class="activity-header">
                                <h5 class="mb-0 fw-bold text-dark">
                                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                    Produtos com Baixo Estoque
                                </h5>
                            </div>
                            @php
                                $lowStockProducts = $petshop->products()->where('stock', '<=', 5)->limit(5)->get();
                            @endphp
                            @forelse($lowStockProducts as $product)
                                <div class="activity-item d-flex justify-content-between align-items-center">
                                    <span class="fw-medium">{{ $product->name }}</span>
                                    <span class="badge-modern badge-{{ $product->stock <= 2 ? 'danger' : 'warning' }}-modern">
                                        {{ $product->stock }} unidades
                                    </span>
                                </div>
                            @empty
                                <div class="activity-item text-center text-muted">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Todos os produtos com estoque adequado
                                </div>
                            @endforelse
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div class="activity-card">
                            <div class="activity-header">
                                <h5 class="mb-0 fw-bold text-dark">
                                    <i class="fas fa-shopping-bag text-success me-2"></i>
                                    Vendas Recentes
                                </h5>
                            </div>
                            @forelse($recentOrders->take(5) as $order)
                                <div class="activity-item d-flex justify-content-between align-items-center">
                                    <span class="fw-medium">Pedido #{{ $order->id }}</span>
                                    <span class="text-success fw-bold">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</span>
                                </div>
                            @empty
                                <div class="activity-item text-center text-muted">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Nenhuma venda recente
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Section -->
            <div id="products-section" class="content-section" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="section-title">Gerenciar Produtos</h2>
                        <p class="section-subtitle">Adicione, edite e gerencie seus produtos</p>
                    </div>
                    <div class="d-flex gap-3">
                        <a href="{{ route('petshop.products.create') }}" class="btn btn-primary-modern btn-modern">
                            <i class="fas fa-plus me-2"></i>Adicionar Produto
                        </a>
                        <a href="{{ route('petshop.products.index') }}" class="btn btn-outline-modern btn-modern">
                            <i class="fas fa-list me-2"></i>Ver Lista Completa
                        </a>
                    </div>
                </div>

                <div class="card-modern">
                    <div class="card-body p-0">
                        <div class="p-4 border-bottom">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <input type="text" class="form-control search-box" placeholder="Buscar produtos..." id="productSearch">
                                </div>
                                <div class="col-md-6 text-end">
                                    <div class="filter-group btn-group">
                                        <button class="btn active" onclick="filterProducts('all')">Todos</button>
                                        <button class="btn" onclick="filterProducts('active')">Ativos</button>
                                        <button class="btn" onclick="filterProducts('inactive')">Inativos</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-modern mb-0">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th>Preço</th>
                                        <th>Estoque</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="productsTableBody">
                                    @forelse($petshop->products()->limit(10)->get() as $product)
                                        <tr data-status="{{ $product->is_active ? 'active' : 'inactive' }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($product->image)
                                                        <img src="{{ asset('storage/' . $product->image) }}" 
                                                             alt="{{ $product->name }}" class="product-image me-3">
                                                    @else
                                                        <div class="product-image bg-light d-flex align-items-center justify-content-center me-3">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="fw-bold text-dark">{{ $product->name }}</div>
                                                        @if($product->brand)
                                                            <small class="text-muted">{{ $product->brand }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="fw-bold text-dark">R$ {{ number_format($product->price, 2, ',', '.') }}</span></td>
                                            <td>
                                                <span class="badge-modern badge-{{ $product->stock <= 5 ? 'danger' : ($product->stock <= 15 ? 'warning' : 'success') }}-modern">
                                                    {{ $product->stock }} unidades
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge-modern badge-{{ $product->is_active ? 'success' : 'secondary' }}-modern">
                                                    {{ $product->is_active ? 'Ativo' : 'Inativo' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('petshop.products.edit', $product) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('products.show', $product) }}" 
                                                       class="btn btn-sm btn-outline-info" title="Ver" target="_blank">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-outline-danger" 
                                                            onclick="confirmDelete({{ $product->id }})" title="Excluir">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5">
                                                <i class="fas fa-box fa-3x text-muted mb-3"></i>
                                                <p class="text-muted mb-3">Nenhum produto cadastrado ainda</p>
                                                <a href="{{ route('petshop.products.create') }}" class="btn btn-primary-modern btn-modern">
                                                    <i class="fas fa-plus me-2"></i>Adicionar Primeiro Produto
                                                </a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services Section -->
            <div id="services-section" class="content-section" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="section-title">Gerenciar Serviços</h2>
                        <p class="section-subtitle">Adicione e gerencie os serviços oferecidos</p>
                    </div>
                    <div class="d-flex gap-3">
                        <a href="{{ route('petshop.services.create') }}" class="btn btn-primary-modern btn-modern">
                            <i class="fas fa-plus me-2"></i>Adicionar Serviço
                        </a>
                        <a href="{{ route('petshop.services.index') }}" class="btn btn-outline-modern btn-modern">
                            <i class="fas fa-list me-2"></i>Ver Lista Completa
                        </a>
                    </div>
                </div>

                <div class="row">
                    @forelse($petshop->services()->limit(6)->get() as $service)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card-modern">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="fw-bold text-dark mb-0">{{ $service->name }}</h5>
                                        <span class="badge-modern badge-{{ $service->is_active ? 'success' : 'secondary' }}-modern">
                                            {{ $service->is_active ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </div>
                                    <p class="text-muted mb-4">{{ Str::limit($service->description, 80) }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-primary h5 mb-0">R$ {{ number_format($service->price, 2, ',', '.') }}</span>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('petshop.services.edit', $service) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    onclick="confirmDeleteService({{ $service->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fas fa-scissors fa-4x text-muted mb-4"></i>
                                <h4 class="text-muted mb-2">Nenhum serviço cadastrado</h4>
                                <p class="text-muted mb-4">Adicione serviços para atrair mais clientes</p>
                                <a href="{{ route('petshop.services.create') }}" class="btn btn-primary-modern btn-modern">
                                    <i class="fas fa-plus me-2"></i>Adicionar Primeiro Serviço
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Employees Section -->
            <div id="employees-section" class="content-section" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="section-title">Gerenciar Funcionários</h2>
                        <p class="section-subtitle">Gerencie sua equipe e funcionários</p>
                    </div>
                    <div class="d-flex gap-3">
                        <a href="{{ route('petshop.employees.create') }}" class="btn btn-primary-modern btn-modern">
                            <i class="fas fa-plus me-2"></i>Adicionar Funcionário
                        </a>
                        <a href="{{ route('petshop.employees.index') }}" class="btn btn-outline-modern btn-modern">
                            <i class="fas fa-list me-2"></i>Ver Lista Completa
                        </a>
                    </div>
                </div>

                <div class="row">
                    @forelse($petshop->employees()->limit(6)->get() as $employee)
                        <div class="col-lg-6 mb-4">
                            <div class="card-modern">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="employee-avatar me-3">
                                            {{ strtoupper(substr($employee->user->name, 0, 2)) }}
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="fw-bold text-dark mb-1">{{ $employee->user->name }}</h5>
                                            <p class="text-muted mb-1">{{ $employee->position }}</p>
                                            <small class="text-muted">{{ $employee->user->email }}</small>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('petshop.employees.edit', $employee) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    onclick="confirmDeleteEmployee({{ $employee->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fas fa-user-tie fa-4x text-muted mb-4"></i>
                                <h4 class="text-muted mb-2">Nenhum funcionário cadastrado</h4>
                                <p class="text-muted mb-4">Adicione funcionários para gerenciar sua equipe</p>
                                <a href="{{ route('petshop.employees.create') }}" class="btn btn-primary-modern btn-modern">
                                    <i class="fas fa-plus me-2"></i>Adicionar Primeiro Funcionário
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Profile Section -->
            <div id="profile-section" class="content-section" style="display: none;">
                <div class="mb-4">
                    <h2 class="section-title">Perfil do Petshop</h2>
                    <p class="section-subtitle">Atualize as informações do seu estabelecimento</p>
                </div>

                <div class="card-modern">
                    <div class="card-header-modern">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-store me-2"></i>
                            Informações do Petshop
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('petshop.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">Nome do Petshop</label>
                                    <input type="text" class="form-control form-control-modern" name="name" value="{{ $petshop->name }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">Email</label>
                                    <input type="email" class="form-control form-control-modern" name="email" value="{{ $petshop->email }}" required>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">Telefone</label>
                                    <input type="text" class="form-control form-control-modern" name="phone" value="{{ $petshop->phone }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">Horário de Funcionamento</label>
                                    <input type="text" class="form-control form-control-modern" name="opening_hours" value="{{ $petshop->opening_hours }}" placeholder="Ex: Seg-Sex: 8h-18h | Sáb: 8h-12h">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold text-dark">Endereço</label>
                                <input type="text" class="form-control form-control-modern" name="address" value="{{ $petshop->address }}" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold text-dark">Descrição</label>
                                <textarea class="form-control form-control-modern" rows="4" name="description" placeholder="Descreva seu petshop...">{{ $petshop->description }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold text-dark">Logo do Petshop</label>
                                <input type="file" class="form-control form-control-modern" name="logo" accept="image/*">
                                <small class="text-muted">Formatos aceitos: JPG, PNG. Tamanho máximo: 2MB.</small>
                                @if($petshop->logo)
                                    <div class="mt-3">
                                        <img src="{{ asset('storage/' . $petshop->logo) }}" alt="Logo atual" width="120" class="rounded border">
                                    </div>
                                @endif
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary-modern btn-modern">
                                    <i class="fas fa-save me-2"></i>Salvar Alterações
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Navigation functions
    function showSection(sectionName) {
        // Hide all sections
        const sections = document.querySelectorAll('.content-section');
        sections.forEach(section => {
            section.style.display = 'none';
        });

        // Show selected section
        document.getElementById(sectionName + '-section').style.display = 'block';

        // Update navigation
        const navLinks = document.querySelectorAll('.sidebar .nav-link');
        navLinks.forEach(link => {
            link.classList.remove('active');
        });
        
        // Find and activate the corresponding nav link
        const activeLink = document.querySelector(`[onclick="showSection('${sectionName}')"]`);
        if (activeLink) {
            activeLink.classList.add('active');
        }

        // Update URL hash
        window.location.hash = sectionName;
    }

    // Product filtering
    function filterProducts(filter) {
        const rows = document.querySelectorAll('#productsTableBody tr');
        const buttons = document.querySelectorAll('.filter-group .btn');
        
        // Update button states
        buttons.forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
        
        // Filter rows
        rows.forEach(row => {
            const status = row.dataset.status;
            if (filter === 'all' || status === filter) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Delete confirmations
    function confirmDelete(productId) {
        if (confirm('Tem certeza que deseja excluir este produto?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/petshop/products/${productId}`;
            form.innerHTML = `
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="DELETE">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }

    function confirmDeleteService(serviceId) {
        if (confirm('Tem certeza que deseja excluir este serviço?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/petshop/services/${serviceId}`;
            form.innerHTML = `
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="DELETE">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }

    function confirmDeleteEmployee(employeeId) {
        if (confirm('Tem certeza que deseja remover este funcionário?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/petshop/employees/${employeeId}`;
            form.innerHTML = `
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="DELETE">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Search functionality
    function setupSearch() {
        const searchInput = document.getElementById('productSearch');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = document.querySelectorAll('#productsTableBody tr');
                
                rows.forEach(row => {
                    const productCell = row.querySelector('td:first-child');
                    if (productCell) {
                        const productName = productCell.querySelector('.fw-bold');
                        if (productName) {
                            const text = productName.textContent.toLowerCase();
                            row.style.display = text.includes(searchTerm) ? '' : 'none';
                        }
                    }
                });
            });
        }
    }

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        // Check URL hash and show corresponding section
        const hash = window.location.hash.substring(1);
        if (hash && document.getElementById(hash + '-section')) {
            showSection(hash);
        } else {
            showSection('dashboard');
        }

        // Setup search functionality
        setupSearch();

        // Add hover effects to stat cards
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px)';
            });
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
            alerts.forEach(function(alert) {
                if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            });
        }, 5000);

        // Success/error messages
        @if(session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif

        @if(session('error'))
            showToast('{{ session('error') }}', 'error');
        @endif

        console.log('🎨 Dashboard moderno carregado com sucesso!');
    });

    // Toast notification function
    function showToast(message, type = 'success') {
        const toastContainer = document.getElementById('toast-container') || createToastContainer();
        
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type === 'error' ? 'danger' : type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.style.borderRadius = '12px';
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
        
        if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            
            toast.addEventListener('hidden.bs.toast', () => {
                toast.remove();
            });
        }
    }

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

    // Smooth scrolling for better UX
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
</script>
@endpush