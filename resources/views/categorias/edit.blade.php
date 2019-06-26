@extends('layouts.app')

@section('titulo')
    - Categoria
@endsection

@section('link')
    Planilla
@endsection

@section('content')

<div class="col-md-12">
    <a href="{{ route('categoria.index') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
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
        <h5 class="card-header">
            Editar categoria <span class="text-danger">>></span> <b>{{ $categoria->nombre }}</b>
        </h5>
        <form class="card-body" action="{{ route('categoria.update', $categoria->id) }}" method="post">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="" class="form-control-label">Descripción <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="nombre" value="{{ old('nombre') ? old('nombre') : $categoria->nombre }}">
                <b class="text-danger">{{ $errors->first('nombre') }}</b>
            </div>

            <button class="btn btn-primary" type="submit">Guardar <i class="fas fa-save"></i></button>

        </form>
    </div>

</div>
@endsection