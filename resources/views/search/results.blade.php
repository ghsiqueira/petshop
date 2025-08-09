@extends('layouts.app')

@section('title', 'Resultados da Busca')

@section('content')
<div class="container">
    <div class="row">
        @foreach($results as $item)
            <div class="col-md-6 col-lg-4 mb-4">
                @include('search.components.result-item', ['item' => $item])
            </div>
        @endforeach
    </div>
    
    @if($results->hasPages())
        <div class="d-flex justify-content-center">
            {{ $results->links() }}
        </div>
    @endif
</div>
@endsection