@extends('layouts.app')

@section('titulo')
    - Cargos + Categoria
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

    <div class="col-md-5">
        <div class="card-body">
            <div class="row">
                @foreach ($cargo->categorias as $categoria)
                    <div class="btn btn-warning ml-1 mb-1">
                        {{ $categoria->nombre }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection