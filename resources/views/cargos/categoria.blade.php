@extends('layouts.app')

@section('titulo')
    - Cargos >> Categoria
@endsection

@section('link')
    Planilla
@endsection

@section('content')

<div class="col-md-12 mb-4">
    <a href="{{ route('cargo.index') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
</div>

@if (session('success'))
    <div class="col-md-12">
        <div class="alert alert-success">
            {{ session('success') }}       
        </div>
    </div>
@endif

<div class="row">

    <div class="col-md-7">
        <div class="card">
            <h5 class="card-header pt-4">
                CARGO <span class="text-danger">>></span> <b class="uppercase">{{ $cargo->descripcion }}</b>
            </h5>
            <hr>

            @if ($errors->first('categorias'))
                <div class="cart-footer pl-3 pr-3">
                    <div class="alert alert-danger">
                        {{ $errors->first('categorias') }}
                    </div>
                </div>
            @endif

            <form class="card-body" action="{{ route('cargo.categoria.store', $cargo->id) }}" method="post">
                @csrf
                    <div class="row">
                        @foreach ($categorias as $categoria)
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="checkbox" name="categorias[]" value="{{ $categoria->id }}">
                                    <i class="fas fa-arrow-right text-danger"></i>
                                    <b><b class="uppercase">{{ $categoria->nombre }}</b></b>
                                </div>
                            </div>
                        @endforeach
                    </div>

                <button class="btn btn-primary" type="submit">Guardar <i class="fas fa-save"></i></button>
            </form>
        </div>
    </div>

    @if($cargo->categorias->count() > 0)
        <div class="col-md-5">

            @if (session('danger'))
                <div class="alert alert-danger">
                    {{ session('danger') }}
                </div>
            @endif

            <div class="card-header">
                Seleccionar para poder eliminar
            </div>
            <form class="card-body" method="POST" action="{{ route('cargo.categoria.delete', $cargo->id) }}">
                <div class="row">
                    @csrf
                    @method('DELETE')
                    @forelse ($cargo->categorias as $categoria)
                        <div class="btn btn-outline-primary ml-1 mb-1">
                            <input type="checkbox" name="categorias[]" value="{{ $categoria->id }}">
                            {{ $categoria->nombre }}
                        </div>
                    @empty
                        <div class="col-md-12">
                            <small>No hay registros disponibles!</small>
                        </div>
                    @endforelse

                    <div class="col-md-12 mt-3">
                        <hr>
                        <input type="password" name="password" class="form-control" required placeholder="Contraseña para confirmar">
                    </div>

                    <div class="col-md-12 mt-3">
                        <button class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i> Eliminar los elementos seleccionados
                        </button>
                    </div>
                </div>
            </form>
        </div>
    @endif
</div>
@endsection