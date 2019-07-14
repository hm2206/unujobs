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

    <div class="col-md-12">
        <div class="card">
            <h5 class="card-header pt-4">
                CARGO <span class="text-danger">>></span> <b class="uppercase">{{ $cargo->descripcion }} </b>
                <span class="text-danger">>> </span><b>CONFIGURAR APORTES AFP</b>
            </h5>

            @if ($errors->first('categorias'))
                <div class="cart-footer pl-3 pr-3">
                    <div class="alert alert-danger">
                        {{ $errors->first('categorias') }}
                    </div>
                </div>
            @endif

            <form class="card-body" action="{{ route('cargo.config.store', $cargo->id) }}" method="post">
                @csrf
                    <div class="row">
                        @foreach ($types as $type)
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="checkbox" name="types[]" {!! $type->checked ? 'checked': null !!} value="{{ $type->id }}">
                                    <i class="fas fa-arrow-right text-danger"></i>
                                    <b><b class="uppercase">{{ $type->descripcion }}</b></b>
                                </div>
                            </div>
                        @endforeach
                    </div>

                <button class="btn btn-success" type="submit">Guardar <i class="fas fa-save"></i></button>
            </form>
        </div>
    </div>
</div>
@endsection