@extends('layouts.app')

@section('title', 'Gerenciar Cupons')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="fas fa-tags me-2"></i>
            Gerenciar Cupons
        </h1>
        <a href="{{ route('coupons.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Criar Cupom
        </a>
    </div>
    
    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('coupons.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Buscar por código ou nome..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Todos os Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativos</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expirados</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativos</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary me-2">
                        <i class="fas fa-search me-1"></i>Filtrar
                    </button>
                    <a href="{{ route('coupons.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if($coupons->count() > 0)
        <div class="row">
            @foreach($coupons as $coupon)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 {{ !$coupon->is_active ? 'border-secondary' : ($coupon->isValid() ? 'border-success' : 'border-warning') }}">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">{{ $coupon->code }}</h5>
                                <small class="text-muted">{{ $coupon->name }}</small>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('coupons.show', $coupon) }}">
                                            <i class="fas fa-eye me-2"></i>Ver Detalhes
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('coupons.edit', $coupon) }}">
                                            <i class="fas fa-edit me-2"></i>Editar
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('coupons.toggle', $coupon) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-{{ $coupon->is_active ? 'pause' : 'play' }} me-2"></i>
                                                {{ $coupon->is_active ? 'Desativar' : 'Ativar' }}
                                            </button>
                                        </form>
                                    </li>
                                    @if($coupon->used_count == 0)
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('coupons.destroy', $coupon) }}" method="POST" 
                                                  onsubmit="return confirm('Tem certeza que deseja excluir este cupom?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-trash me-2"></i>Excluir
                                                </button>
                                            </form>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <!-- Desconto -->
                            <div class="text-center mb-3">
                                <span class="badge bg-primary fs-6 px-3 py-2">
                                    {{ $coupon->getDiscountText() }}
                                </span>
                            </div>
                            
                            <!-- Descrição -->
                            @if($coupon->description)
                                <p class="card-text text-muted small">{{ Str::limit($coupon->description, 80) }}</p>
                            @endif
                            
                            <!-- Informações -->
                            <div class="small text-muted">
                                @if($coupon->minimum_amount)
                                    <div><i class="fas fa-coins me-1"></i> Min: R$ {{ number_format($coupon->minimum_amount, 2, ',', '.') }}</div>
                                @endif
                                
                                @if($coupon->usage_limit)
                                    <div><i class="fas fa-users me-1"></i> {{ $coupon->getRemainingUsages() }}/{{ $coupon->usage_limit }} restantes</div>
                                @else
                                    <div><i class="fas fa-infinity me-1"></i> Uso ilimitado</div>
                                @endif
                                
                                <div><i class="fas fa-user me-1"></i> {{ $coupon->usage_limit_per_user }}x por usuário</div>
                                
                                @if($coupon->expires_at)
                                    <div class="{{ $coupon->isExpiringSoon() ? 'text-warning' : '' }}">
                                        <i class="fas fa-calendar me-1"></i> 
                                        Expira: {{ $coupon->expires_at->format('d/m/Y H:i') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    @if($coupon->is_active && $coupon->isValid())
                                        <span class="badge bg-success">Ativo</span>
                                    @elseif(!$coupon->is_active)
                                        <span class="badge bg-secondary">Inativo</span>
                                    @elseif($coupon->expires_at && \Carbon\Carbon::now()->gt($coupon->expires_at))
                                        <span class="badge bg-danger">Expirado</span>
                                    @else
                                        <span class="badge bg-warning">Indisponível</span>
                                    @endif
                                </div>
                                
                                <small class="text-muted">
                                    {{ $coupon->used_count }} usos
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Paginação -->
        @if($coupons->hasPages())
            <div class="d-flex justify-content-center">
                {{ $coupons->links() }}
            </div>
        @endif
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-tags fa-4x text-muted mb-3"></i>
                <h3 class="text-muted">Nenhum cupom encontrado</h3>
                <p class="text-muted mb-4">
                    @if(request()->has('search') || request()->has('status'))
                        Nenhum cupom corresponde aos filtros aplicados.
                    @else
                        Crie seu primeiro cupom de desconto para atrair mais clientes!
                    @endif
                </p>
                <a href="{{ route('coupons.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Criar Primeiro Cupom
                </a>
            </div>
        </div>
    @endif
</div>
@endsection