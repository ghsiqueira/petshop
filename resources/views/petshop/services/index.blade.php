@extends('layouts.app')

@section('title', 'Gerenciar Serviços')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gerenciar Serviços</h1>
        <a href="{{ route('petshop.services.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Adicionar Serviço
        </a>
    </div>
    
    <div class="card">
        <div class="card-header bg-light">
            <form action="{{ route('petshop.services.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Buscar serviços..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-outline-primary">Buscar</button>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="btn-group" role="group">
                        <a href="{{ route('petshop.services.index') }}" class="btn btn-outline-secondary {{ !request('filter') ? 'active' : '' }}">Todos</a>
                        <a href="{{ route('petshop.services.index', ['filter' => 'active']) }}" class="btn btn-outline-secondary {{ request('filter') == 'active' ? 'active' : '' }}">Ativos</a>
                        <a href="{{ route('petshop.services.index', ['filter' => 'inactive']) }}" class="btn btn-outline-secondary {{ request('filter') == 'inactive' ? 'active' : '' }}">Inativos</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col">Nome</th>
                            <th scope="col">Preço</th>
                            <th scope="col">Duração</th>
                            <th scope="col">Status</th>
                            <th scope="col" width="150">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $service)
                            <tr>
                                <td>{{ $service->name }}</td>
                                <td>R$ {{ number_format($service->price, 2, ',', '.') }}</td>
                                <td>{{ $service->duration_minutes }} minutos</td>
                                <td>
                                    <span class="badge bg-{{ $service->is_active ? 'success' : 'danger' }}">
                                        {{ $service->is_active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('services.show', $service->id) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('petshop.services.edit', $service->id) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteServiceModal{{ $service->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Modal de confirmação de exclusão -->
                            <div class="modal fade" id="deleteServiceModal{{ $service->id }}" tabindex="-1" aria-labelledby="deleteServiceModalLabel{{ $service->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteServiceModalLabel{{ $service->id }}">Confirmar Exclusão</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Tem certeza que deseja excluir o serviço <strong>{{ $service->name }}</strong>?
                                            
                                            @if($service->appointments()->count() > 0)
                                                <div class="alert alert-warning mt-3">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    Este serviço possui agendamentos. A exclusão pode afetar esses registros.
                                                </div>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <form action="{{ route('petshop.services.destroy', $service->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Excluir</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <p class="mb-0">Nenhum serviço encontrado.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(isset($services) && $services->hasPages())
            <div class="card-footer">
                {{ $services->links() }}
            </div>
        @endif
    </div>
</div>
@endsection