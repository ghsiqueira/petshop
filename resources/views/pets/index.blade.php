@extends('layouts.app')

@section('title', 'Meus Pets')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Meus Pets</h1>
        <a href="{{ route('pets.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Adicionar Pet
        </a>
    </div>
    
    <!-- Modificação no alerta (sem alterações na estrutura) -->
    @if(session('warning'))
        <div id="warningAlert" class="alert alert-warning" role="alert">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('warning') }}
                </div>
                <div>
                    <button type="button" class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal{{ session('pet_id') }}">
                        Excluir mesmo assim
                    </button>
                    <button type="button" class="btn-close" onclick="document.getElementById('warningAlert').style.display='none';" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif
    
    <div class="row">
        @forelse($pets as $pet)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ $pet->photo ? asset('storage/' . $pet->photo) : asset('img/no-pet-image.jpg') }}" class="card-img-top" alt="{{ $pet->name }}" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title">{{ $pet->name }}</h5>
                        <p class="card-text mb-1">
                            <strong>Espécie:</strong> {{ ucfirst($pet->species) }}
                        </p>
                        @if($pet->breed)
                            <p class="card-text mb-1">
                                <strong>Raça:</strong> {{ $pet->breed }}
                            </p>
                        @endif
                        <p class="card-text mb-1">
                            <strong>Gênero:</strong> {{ $pet->gender == 'male' ? 'Macho' : ($pet->gender == 'female' ? 'Fêmea' : 'Desconhecido') }}
                        </p>
                        @if($pet->birth_date)
                            <p class="card-text mb-1">
                                <strong>Idade:</strong> {{ $pet->formatted_age }}
                            </p>
                        @endif
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('pets.show', $pet->id) }}" class="btn btn-outline-primary">Detalhes</a>
                            <a href="{{ route('pets.edit', $pet->id) }}" class="btn btn-outline-secondary">Editar</a>
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deletePetModal{{ $pet->id }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modal de confirmação de exclusão -->
            <div class="modal fade" id="deletePetModal{{ $pet->id }}" tabindex="-1" aria-labelledby="deletePetModalLabel{{ $pet->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deletePetModalLabel{{ $pet->id }}">Confirmar Exclusão</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Tem certeza que deseja excluir o pet <strong>{{ $pet->name }}</strong>?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <form action="{{ route('pets.destroy', $pet->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Excluir</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modal de confirmação para exclusão com agendamentos -->
            <div class="modal fade" id="confirmDeleteModal{{ $pet->id }}" tabindex="-1" aria-labelledby="confirmDeleteModalLabel{{ $pet->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmDeleteModalLabel{{ $pet->id }}">Atenção: Exclusão com Agendamentos</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Atenção:</strong> O pet <strong>{{ $pet->name }}</strong> possui agendamentos associados.
                            </div>
                            <p>Ao excluir este pet, <strong>todos os seus agendamentos</strong> também serão excluídos permanentemente.</p>
                            <p>Esta ação não pode ser desfeita.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <form action="{{ route('pets.destroy', $pet->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="force_delete" value="1">
                                <button type="submit" class="btn btn-danger">Sim, excluir pet e agendamentos</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    Você ainda não cadastrou nenhum pet. <a href="{{ route('pets.create') }}">Clique aqui</a> para adicionar seu primeiro pet.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection