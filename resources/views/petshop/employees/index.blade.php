@extends('layouts.app')

@section('title', 'Gerenciar Funcionários')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gerenciar Funcionários</h1>
        <a href="{{ route('petshop.employees.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Adicionar Funcionário
        </a>
    </div>
    
    <div class="card">
        <div class="card-header bg-light">
            <form action="{{ route('petshop.employees.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Buscar funcionários..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-outline-primary">Buscar</button>
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
                            <th scope="col">Email</th>
                            <th scope="col">Cargo</th>
                            <th scope="col">Data de Admissão</th>
                            <th scope="col" width="150">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                            <tr>
                                <td>{{ $employee->user->name }}</td>
                                <td>{{ $employee->user->email }}</td>
                                <td>{{ $employee->position }}</td>
                                <td>{{ $employee->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('petshop.employees.show', $employee->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('petshop.employees.edit', $employee->id) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteEmployeeModal{{ $employee->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Modal de confirmação de exclusão -->
                            <div class="modal fade" id="deleteEmployeeModal{{ $employee->id }}" tabindex="-1" aria-labelledby="deleteEmployeeModalLabel{{ $employee->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteEmployeeModalLabel{{ $employee->id }}">Confirmar Exclusão</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Tem certeza que deseja excluir o funcionário <strong>{{ $employee->user->name }}</strong>?
                                            
                                            @if($employee->appointments()->count() > 0)
                                                <div class="alert alert-warning mt-3">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    Este funcionário possui agendamentos. A exclusão pode afetar esses registros.
                                                </div>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <form action="{{ route('petshop.employees.destroy', $employee->id) }}" method="POST">
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
                                    <p class="mb-0">Nenhum funcionário encontrado.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($employees->count() > 0)
            <div class="card-footer">
                @if(method_exists($employees, 'links'))
                    {{ $employees->links() }}
                @endif
            </div>
        @endif
    </div>
</div>
@endsection