@extends('layouts.app')

@section('titulo')
    - Cargos + Categoria
@endsection

@section('link')
    Planilla
@endsection

@section('content')

<div class="col-md-12">
    <a href="{{ route('cargo.index') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
</div>

@if (session('success'))
    <div class="col-md-12 mt-3 ">
        <div class="alert alert-success">
            {{ session('success') }}       
        </div>
    </div>
@endif

<div class="col-md-7">

    <div class="card">
        <div class="card-header">
            CARGO <span class="text-danger">>></span> <b class="uppercase">{{ $cargo->descripcion }}</b>
        </div>
        <form class="card-body" action="{{ route('cargo.categoria.store', $cargo->id) }}" method="post">
            @csrf
            <div class="form-group">
                <label for="" class="form-control-label">Categoria <span class="text-danger">*</span></label>
                <select name="categoria_id" class="form-control">
                    <option value="">Seleccionar...</option>
                    @foreach ($categorias as $categoria)
                        <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                    @endforeach
                </select>
                <b class="text-danger">{{ $errors->first('categoria_id') }}</b>
            </div>

            <button class="btn btn-primary" type="submit">Guardar <i class="fas fa-save"></i></button>

        </form>
    </div>

</div>
@endsection