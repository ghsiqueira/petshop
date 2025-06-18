@extends('layouts.app')

@section('title', 'Editar Funcionário')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('petshop.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('petshop.employees.index') }}">Funcionários</a></li>
            <li class="breadcrumb-item active" aria-current="page">Editar Funcionário</li>
        </ol>
    </nav>
    
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Editar Funcionário: {{ $employee->user->name }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('petshop.employees.update', $employee->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <h5 class="mb-3">Informações do Usuário</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nome*</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $employee->user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email*</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $employee->user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label">Nova Senha (deixe em branco para manter a atual)</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Confirmar Nova Senha</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
                </div>
                
                <hr class="my-4">
                
                <h5 class="mb-3">Informações do Funcionário</h5>
                <div class="mb-3">
                    <label for="position" class="form-label">Cargo*</label>
                    <input type="text" class="form-control @error('position') is-invalid @enderror" id="position" name="position" value="{{ old('position', $employee->position) }}" required>
                    @error('position')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="bio" class="form-label">Biografia/Descrição</label>
                    <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="3">{{ old('bio', $employee->bio) }}</textarea>
                    @error('bio')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('petshop.employees.index') }}" class="btn btn-outline-secondary me-md-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Atualizar Funcionário</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection