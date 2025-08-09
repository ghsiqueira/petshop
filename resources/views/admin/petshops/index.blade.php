@extends('layouts.app')

@section('title', 'Gerenciar Pet Shops')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1><i class="fas fa-store me-2"></i>Gerenciar Pet Shops</h1>
            <small class="text-muted">{{ $petshops->total() }} pet shops encontrados</small>
        </div>
        <div>
            <a href="{{ route('admin.petshops.create') }}" class="btn btn-primary me-2">
                <i class="fas fa-plus me-2"></i>Adicionar Pet Shop
            </a>
            <div class="btn-group">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-download me-1"></i>Exportar
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.petshops.export', request()->query()) }}">
                            <i class="fas fa-file-csv me-2"></i>Exportar CSV
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Filtros Avançados -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <button class="btn btn-link text-decoration-none p-0" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse">
                    <i class="fas fa-filter me-2"></i>Filtros Avançados
                    <i class="fas fa-chevron-down ms-2"></i>
                </button>
            </h5>
        </div>
        <div class="collapse {{ request()->hasAny(['search', 'filter', 'city', 'date_from', 'date_to', 'min_products', 'sort']) ? 'show' : '' }}" id="filtersCollapse">
            <div class="card-body">
                <form action="{{ route('admin.petshops.index') }}" method="GET" id="filterForm">
                    <div class="row g-3">
                        <!-- Busca geral -->
                        <div class="col-md-6 col-lg-4">
                            <label for="search" class="form-label">Busca Geral</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   placeholder="Nome, endereço, telefone, proprietário..." 
                                   value="{{ request('search') }}">
                        </div>
                        
                        <!-- Status -->
                        <div class="col-md-6 col-lg-2">
                            <label for="filter" class="form-label">Status</label>
                            <select name="filter" id="filter" class="form-select">
                                <option value="">Todos</option>
                                <option value="active" {{ request('filter') == 'active' ? 'selected' : '' }}>Ativos</option>
                                <option value="inactive" {{ request('filter') == 'inactive' ? 'selected' : '' }}>Inativos</option>
                            </select>
                        </div>
                        
                        <!-- Cidade -->
                        <div class="col-md-6 col-lg-3">
                            <label for="city" class="form-label">Cidade</label>
                            <select name="city" id="city" class="form-select">
                                <option value="">Todas as cidades</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>
                                        {{ $city }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Ordenação -->
                        <div class="col-md-6 col-lg-3">
                            <label for="sort" class="form-label">Ordenar por</label>
                            <select name="sort" id="sort" class="form-select">
                                <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Data de criação</option>
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nome</option>
                                <option value="updated_at" {{ request('sort') == 'updated_at' ? 'selected' : '' }}>Última atualização</option>
                                <option value="products_count" {{ request('sort') == 'products_count' ? 'selected' : '' }}>Número de produtos</option>
                                <option value="services_count" {{ request('sort') == 'services_count' ? 'selected' : '' }}>Número de serviços</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-2">
                        <!-- Data de criação -->
                        <div class="col-md-6 col-lg-3">
                            <label for="date_from" class="form-label">Criado a partir de</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                        </div>
                        
                        <div class="col-md-6 col-lg-3">
                            <label for="date_to" class="form-label">Criado até</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                        </div>
                        
                        <!-- Mínimo de produtos -->
                        <div class="col-md-6 col-lg-3">
                            <label for="min_products" class="form-label">Mín. de produtos</label>
                            <input type="number" class="form-control" id="min_products" name="min_products" value="{{ request('min_products') }}" min="0">
                        </div>
                        
                        <!-- Botões de ação -->
                        <div class="col-md-6 col-lg-3 d-flex align-items-end">
                            <div class="btn-group w-100">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>Filtrar
                                </button>
                                <a href="{{ route('admin.petshops.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Limpar
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Resultados -->
    <div class="card">
        <div class="card-body p-0">
            @if($petshops->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th scope="col" width="80">Logo</th>
                                <th scope="col">Nome do Pet Shop</th>
                                <th scope="col">Proprietário</th>
                                <th scope="col">Contato</th>
                                <th scope="col" width="100">Produtos</th>
                                <th scope="col" width="100">Serviços</th>
                                <th scope="col" width="100">Funcionários</th>
                                <th scope="col" width="100">Status</th>
                                <th scope="col" width="200">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($petshops as $petshop)
                                <tr>
                                    <td>
                                        <img src="{{ $petshop->logo ? asset('storage/' . $petshop->logo) : asset('img/no-logo.jpg') }}" 
                                             alt="{{ $petshop->name }}" 
                                             class="img-thumbnail" 
                                             width="60">
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $petshop->name }}</strong>
                                            @if($petshop->opening_hours)
                                                <br><small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>{{ $petshop->opening_hours }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $petshop->user->name }}</strong>
                                            <br><small class="text-muted">{{ $petshop->user->email }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            @if($petshop->phone)
                                                <small class="d-block">
                                                    <i class="fas fa-phone me-1"></i>{{ $petshop->phone }}
                                                </small>
                                            @endif
                                            @if($petshop->email)
                                                <small class="d-block">
                                                    <i class="fas fa-envelope me-1"></i>{{ $petshop->email }}
                                                </small>
                                            @endif
                                            @if($petshop->address)
                                                <small class="d-block text-muted">
                                                    <i class="fas fa-map-marker-alt me-1"></i>{{ Str::limit($petshop->address, 30) }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary rounded-pill">
                                            {{ $petshop->products_count }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success rounded-pill">
                                            {{ $petshop->services_count }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info rounded-pill">
                                            {{ $petshop->employees_count }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input status-toggle" 
                                                   type="checkbox" 
                                                   {{ $petshop->is_active ? 'checked' : '' }}
                                                   data-id="{{ $petshop->id }}">
                                            <label class="form-check-label">
                                                <span class="badge bg-{{ $petshop->is_active ? 'success' : 'danger' }}">
                                                    {{ $petshop->is_active ? 'Ativo' : 'Inativo' }}
                                                </span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.petshops.show', $petshop->id) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.petshops.edit', $petshop->id) }}" 
                                               class="btn btn-sm btn-outline-warning" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('petshops.show', $petshop->id) }}" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="Ver na loja" 
                                               target="_blank">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    title="Excluir"
                                                    onclick="confirmDelete('{{ $petshop->id }}', '{{ $petshop->name }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginação -->
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            Mostrando {{ $petshops->firstItem() }} a {{ $petshops->lastItem() }} de {{ $petshops->total() }} resultados
                        </div>
                        <div>
                            {{ $petshops->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4>Nenhum pet shop encontrado</h4>
                    <p class="text-muted">Tente ajustar os filtros ou criar um novo pet shop.</p>
                    <a href="{{ route('admin.petshops.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Criar Pet Shop
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de confirmação de exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o pet shop <strong id="petshopName"></strong>?</p>
                <p class="text-danger"><small>Esta ação não pode ser desfeita.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle de status
    document.querySelectorAll('.status-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const petshopId = this.dataset.id;
            const isActive = this.checked;
            
            fetch(`/admin/petshops/${petshopId}/toggle-status`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Atualizar o badge
                    const badge = this.parentElement.querySelector('.badge');
                    if (isActive) {
                        badge.className = 'badge bg-success';
                        badge.textContent = 'Ativo';
                    } else {
                        badge.className = 'badge bg-danger';
                        badge.textContent = 'Inativo';
                    }
                    
                    // Mostrar toast de sucesso
                    showToast('Status atualizado com sucesso!', 'success');
                } else {
                    // Reverter o toggle em caso de erro
                    this.checked = !isActive;
                    showToast('Erro ao atualizar status', 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                this.checked = !isActive;
                showToast('Erro ao atualizar status', 'error');
            });
        });
    });
});

function confirmDelete(id, name) {
    document.getElementById('petshopName').textContent = name;
    document.getElementById('deleteForm').action = `/admin/petshops/${id}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function showToast(message, type) {
    // Implementar toast notification
    console.log(`${type}: ${message}`);
}
</script>
@endpush
@endsection