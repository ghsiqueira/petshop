@extends('layouts.app')

@section('title', 'Editar Pet')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pets.index') }}">Meus Pets</a></li>
            <li class="breadcrumb-item active" aria-current="page">Editar {{ $pet->name }}</li>
        </ol>
    </nav>
    
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Editar Pet</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('pets.update', $pet->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nome*</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $pet->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="species" class="form-label">Espécie*</label>
                        <select class="form-select @error('species') is-invalid @enderror" id="species" name="species" required>
                            <option value="" disabled>Selecione...</option>
                            <option value="dog" {{ old('species', $pet->species) == 'dog' ? 'selected' : '' }}>Cachorro</option>
                            <option value="cat" {{ old('species', $pet->species) == 'cat' ? 'selected' : '' }}>Gato</option>
                            <option value="bird" {{ old('species', $pet->species) == 'bird' ? 'selected' : '' }}>Pássaro</option>
                            <option value="reptile" {{ old('species', $pet->species) == 'reptile' ? 'selected' : '' }}>Réptil</option>
                            <option value="rodent" {{ old('species', $pet->species) == 'rodent' ? 'selected' : '' }}>Roedor</option>
                            <option value="other" {{ old('species', $pet->species) == 'other' ? 'selected' : '' }}>Outro</option>
                        </select>
                        @error('species')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="breed" class="form-label">Raça</label>
                        <input type="text" class="form-control @error('breed') is-invalid @enderror" id="breed" name="breed" value="{{ old('breed', $pet->breed) }}">
                        @error('breed')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="gender" class="form-label">Gênero*</label>
                        <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                            <option value="" disabled>Selecione...</option>
                            <option value="male" {{ old('gender', $pet->gender) == 'male' ? 'selected' : '' }}>Macho</option>
                            <option value="female" {{ old('gender', $pet->gender) == 'female' ? 'selected' : '' }}>Fêmea</option>
                            <option value="unknown" {{ old('gender', $pet->gender) == 'unknown' ? 'selected' : '' }}>Desconhecido</option>
                        </select>
                        @error('gender')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="birth_date" class="form-label">Data de Nascimento</label>
                        <input type="date" class="form-control @error('birth_date') is-invalid @enderror" id="birth_date" name="birth_date" value="{{ old('birth_date', $pet->birth_date ? $pet->birth_date->format('Y-m-d') : '') }}">
                        @error('birth_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="photo" class="form-label">Foto</label>
                        <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo">
                        @error('photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        @if($pet->photo)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $pet->photo) }}" alt="{{ $pet->name }}" class="img-thumbnail" style="height: 100px;">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="remove_photo" id="remove_photo" value="1">
                                    <label class="form-check-label" for="remove_photo">
                                        Remover foto atual
                                    </label>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="medical_information" class="form-label">Informações Médicas</label>
                    <textarea class="form-control @error('medical_information') is-invalid @enderror" id="medical_information" name="medical_information" rows="3">{{ old('medical_information', $pet->medical_information) }}</textarea>
                    @error('medical_information')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-flex justify-content-end">
                    <a href="{{ route('pets.index') }}" class="btn btn-outline-secondary me-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection