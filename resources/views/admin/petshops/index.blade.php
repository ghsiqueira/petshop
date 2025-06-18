@extends('layouts.app')

@section('title', 'Gerenciar Pet Shops')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gerenciar Pet Shops</h1>
        <a href="{{ route('admin.petshops.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Adicionar Pet Shop
        </a>
    </div>
    
    <div class="card">
        <div class="card-header bg-light">
            <div class="row g-3">
                <div class="col-md-6">
                    <form action="{{ route('admin.petshops.index') }}" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control me-2" placeholder="Buscar pet shops..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-outline-primary">Buscar</button>
                    </form>
                </div>
                <div class="col-md-6 text-end">
                    <div class="btn-group" role="group">
                        <a href="{{ route('admin.petshops.index') }}" class="btn btn-outline-secondary {{ !request('filter') ? 'active' : '' }}">Todos</a>
                        <a href="{{ route('admin.petshops.index', ['filter' => 'active']) }}" class="btn btn-outline-secondary {{ request('filter') == 'active' ? 'active' : '' }}">Ativos</a>
                        <a href="{{ route('admin.petshops.index', ['filter' => 'inactive']) }}" class="btn btn-outline-secondary {{ request('filter') == 'inactive' ? 'active' : '' }}">Inativos</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col" width="100">Logo</th>
                            <th scope="col">Nome</th>
                            <th scope="col">Proprietário</th>
                            <th scope="col">Contato</th>
                            <th scope="col">Status</th>
                            <th scope="col" width="180">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($petshops as $petshop)
                            <tr>
                                <td>
                                    <img src="{{ $petshop->logo ? asset('storage/' . $petshop->logo) : asset('img/no-logo.jpg') }}" alt="{{ $petshop->name }}" class="img-thumbnail" width="80">
                                </td>
                                <td>{{ $petshop->name }}</td>
                                <td>{{ $petshop->user->name }}</td>
                                <td>
                                    <div>{{ $petshop->phone }}</div>
                                    <div>{{ $petshop->email }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $petshop->is_active ? 'success' : 'danger' }}">
                                        {{ $petshop->is_active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('petshops.show', $petshop->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.petshops.edit', $petshop->id) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deletePetshopModal{{ $petshop->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Modal de confirmação de exclusão -->
                            <div class="modal fade" id="deletePetshopModal{{ $petshop->id }}" tabindex="-1" aria-labelledby="deletePetshopModalLabel{{ $petshop->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deletePetshopModalLabel{{ $petshop->id }}">Confirmar Exclusão</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Tem certeza que deseja excluir o pet shop <strong>{{ $petshop->name }}</strong>?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <form action="{{ route('admin.petshops.destroy', $petshop->id) }}" method="POST">
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
                                <td colspan="6" class="text-center py-4">
                                    <p class="mb-0">Nenhum pet shop encontrado.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-center">
                {{ $petshops->links() }}
            </div>
        </div>
    </div>
</div>
@endsection