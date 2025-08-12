@extends('layouts.app')

@section('title', 'Meu Perfil')

@section('content')
<div class="container">
    <!-- Header do Perfil -->
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-user me-2"></i>Meu Perfil
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Coluna da Foto -->
                        <div class="col-md-4 text-center mb-4 mb-md-0">
                            <div class="position-relative d-inline-block">
                                <!-- Avatar Principal -->
                                @if($user->profile_picture && Storage::disk('public')->exists($user->profile_picture))
                                    <img src="{{ asset('storage/' . $user->profile_picture) }}" 
                                         alt="{{ $user->name }}" 
                                         class="rounded-circle user-avatar mb-3 shadow"
                                         style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #fff;"
                                         id="profile-main-avatar"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <!-- Fallback caso a imagem falhe -->
                                    <div class="rounded-circle avatar-initials d-none align-items-center justify-content-center mx-auto mb-3 shadow"
                                         style="width: 150px; height: 150px; font-size: 48px; border: 4px solid #fff;"
                                         id="profile-main-initials">
                                        @php
                                            $words = explode(' ', $user->name);
                                            $initials = '';
                                            foreach($words as $word) {
                                                if(!empty($word)) {
                                                    $initials .= strtoupper(substr($word, 0, 1));
                                                }
                                                if(strlen($initials) >= 2) break;
                                            }
                                        @endphp
                                        {{ $initials ?: 'U' }}
                                    </div>
                                @else
                                    <div class="rounded-circle avatar-initials d-flex align-items-center justify-content-center mx-auto mb-3 shadow"
                                         style="width: 150px; height: 150px; font-size: 48px; border: 4px solid #fff;"
                                         id="profile-main-initials">
                                        @php
                                            $words = explode(' ', $user->name);
                                            $initials = '';
                                            foreach($words as $word) {
                                                if(!empty($word)) {
                                                    $initials .= strtoupper(substr($word, 0, 1));
                                                }
                                                if(strlen($initials) >= 2) break;
                                            }
                                        @endphp
                                        {{ $initials ?: 'U' }}
                                    </div>
                                @endif

                                <!-- Botão para alterar foto -->
                                <div class="position-absolute bottom-0 end-0 mb-3 me-3">
                                    <button type="button" 
                                            class="btn btn-primary btn-sm rounded-circle" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#photoModal"
                                            style="width: 40px; height: 40px;"
                                            title="Alterar foto">
                                        <i class="fas fa-camera"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Informações do usuário -->
                            <h3 class="mt-3">{{ $user->name }}</h3>
                            <p class="text-muted">{{ $user->email }}</p>
                            
                            @if($user->roles->isNotEmpty())
                                <span class="badge bg-primary fs-6">
                                    {{ ucfirst($user->roles->first()->name) }}
                                </span>
                            @endif

                            @if($user->phone)
                                <p class="mt-2">
                                    <i class="fas fa-phone me-2"></i>{{ $user->phone }}
                                </p>
                            @endif

                            <!-- Estatísticas do Perfil -->
                            @if(isset($profileStats))
                                <div class="mt-4">
                                    <h6>Estatísticas</h6>
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="border rounded p-2">
                                                <div class="h5 mb-0 text-primary">{{ $profileStats['profile_completion'] }}%</div>
                                                <small class="text-muted">Perfil</small>
                                            </div>
                                        </div>
                                        @if($user->hasRole('client') && isset($profileStats['total_pets']))
                                            <div class="col-4">
                                                <div class="border rounded p-2">
                                                    <div class="h5 mb-0 text-success">{{ $profileStats['total_pets'] }}</div>
                                                    <small class="text-muted">Pets</small>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="border rounded p-2">
                                                    <div class="h5 mb-0 text-info">{{ $profileStats['total_orders'] ?? 0 }}</div>
                                                    <small class="text-muted">Pedidos</small>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Coluna das Informações -->
                        <div class="col-md-8">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Informações Pessoais</h5>
                                <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-edit me-1"></i>Editar Perfil
                                </a>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Nome Completo</label>
                                    <p class="form-control-plaintext">{{ $user->name }}</p>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Email</label>
                                    <p class="form-control-plaintext">{{ $user->email }}</p>
                                </div>
                                
                                @if($user->phone)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Telefone</label>
                                        <p class="form-control-plaintext">{{ $user->phone }}</p>
                                    </div>
                                @endif
                                
                                @if($user->address)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Endereço</label>
                                        <p class="form-control-plaintext">{{ $user->address }}</p>
                                    </div>
                                @endif
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Membro desde</label>
                                    <p class="form-control-plaintext">{{ $user->created_at->format('d/m/Y') }}</p>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Último acesso</label>
                                    <p class="form-control-plaintext">{{ $user->updated_at->diffForHumans() }}</p>
                                </div>
                            </div>

                            <!-- Ações Rápidas -->
                            <div class="mt-4">
                                <h6>Ações Rápidas</h6>
                                <div class="btn-group-vertical w-100" role="group">
                                    @if($user->hasRole('client'))
                                        <a href="{{ route('pets.index') }}" class="btn btn-outline-primary">
                                            <i class="fas fa-paw me-2"></i>Gerenciar Meus Pets
                                        </a>
                                        <a href="{{ route('orders.index') }}" class="btn btn-outline-success">
                                            <i class="fas fa-shopping-bag me-2"></i>Meus Pedidos
                                        </a>
                                        <a href="{{ route('appointments.index') }}" class="btn btn-outline-info">
                                            <i class="fas fa-calendar me-2"></i>Meus Agendamentos
                                        </a>
                                    @elseif($user->hasRole('petshop'))
                                        <a href="{{ route('analytics.petshop') }}" class="btn btn-outline-primary">
                                            <i class="fas fa-chart-line me-2"></i>Dashboard do Pet Shop
                                        </a>
                                        <a href="{{ route('petshop.products.index') }}" class="btn btn-outline-success">
                                            <i class="fas fa-box me-2"></i>Gerenciar Produtos
                                        </a>
                                        <a href="{{ route('petshop.services.index') }}" class="btn btn-outline-info">
                                            <i class="fas fa-cut me-2"></i>Gerenciar Serviços
                                        </a>
                                    @endif
                                    
                                    <a href="{{ route('profile.export', 'json') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-download me-2"></i>Exportar Dados
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Upload de Foto -->
<div class="modal fade" id="photoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Alterar Foto de Perfil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="photo-upload-form" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="photo" class="form-label">Escolher nova foto (máx. 5MB)</label>
                        <input type="file" class="form-control" id="photo" name="photo" 
                               accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" required>
                        <div class="form-text">Formatos aceitos: JPG, PNG, GIF, WebP</div>
                    </div>
                    
                    <!-- Preview da imagem -->
                    <div id="photo-preview" class="text-center mb-3" style="display: none;">
                        <img id="preview-image" class="rounded" style="max-width: 200px; max-height: 200px;">
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary" id="upload-btn">
                            <i class="fas fa-upload me-2"></i>Fazer Upload
                        </button>
                        @if($user->profile_picture)
                            <button type="button" class="btn btn-outline-danger" id="delete-photo-btn">
                                <i class="fas fa-trash me-2"></i>Remover Foto Atual
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const photoInput = document.getElementById('photo');
    const photoForm = document.getElementById('photo-upload-form');
    const uploadBtn = document.getElementById('upload-btn');
    const deleteBtn = document.getElementById('delete-photo-btn');
    const preview = document.getElementById('photo-preview');
    const previewImage = document.getElementById('preview-image');

    // Preview da imagem
    photoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    // Upload da foto
    photoForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        uploadBtn.disabled = true;
        uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enviando...';
        
        fetch('{{ route("profile.upload-photo") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Atualizar avatar na página
                const mainAvatar = document.getElementById('profile-main-avatar');
                const mainInitials = document.getElementById('profile-main-initials');
                
                if (mainAvatar) {
                    mainAvatar.src = data.photo_url;
                    mainAvatar.style.display = 'block';
                    if (mainInitials) {
                        mainInitials.style.display = 'none';
                    }
                } else if (mainInitials) {
                    // Criar nova imagem se só havia iniciais
                    const newImg = document.createElement('img');
                    newImg.src = data.photo_url;
                    newImg.alt = '{{ $user->name }}';
                    newImg.className = 'rounded-circle user-avatar mb-3 shadow';
                    newImg.style.cssText = 'width: 150px; height: 150px; object-fit: cover; border: 4px solid #fff;';
                    newImg.id = 'profile-main-avatar';
                    
                    mainInitials.parentNode.insertBefore(newImg, mainInitials);
                    mainInitials.style.display = 'none';
                }
                
                // Atualizar avatars na navbar
                if (window.updateUserAvatar) {
                    window.updateUserAvatar(data.photo_url);
                }
                
                // Fechar modal e mostrar sucesso
                bootstrap.Modal.getInstance(document.getElementById('photoModal')).hide();
                window.showToast(data.message, 'success');
                
                // Recarregar página após 1 segundo para atualizar tudo
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                window.showToast(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            window.showToast('Erro ao fazer upload da foto', 'error');
        })
        .finally(() => {
            uploadBtn.disabled = false;
            uploadBtn.innerHTML = '<i class="fas fa-upload me-2"></i>Fazer Upload';
        });
    });

    // Deletar foto
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function() {
            if (confirm('Tem certeza que deseja remover sua foto de perfil?')) {
                fetch('{{ route("profile.delete-photo") }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.showToast(data.message, 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        window.showToast(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    window.showToast('Erro ao remover foto', 'error');
                });
            }
        });
    }
});
</script>
@endpush