@extends('layouts.app')

@section('titulo')
    - Descuento
@endsection

@section('link')
    Planilla
@endsection

@section('content')

<div class="col-md-12">
    <a href="{{ route('descuento.index') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
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
            Editar >> {{ $type->descripcion }}</b>
        </div>
        <form class="card-body" action="{{ route('descuento.update', $type->id) }}" method="post">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="" class="form-control-label">Clave <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="key" value="{{ old('key') ? old('key') : $type->key }}">
                <b class="text-danger">{{ $errors->first('key') }}</b>
            </div>

            <div class="form-group">
                <label for="" class="form-control-label">Descripción <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="descripcion" value="{{ old('descripcion') ? old('descripcion') : $type->descripcion }}">
                <b class="text-danger">{{ $errors->first('descripcion') }}</b>
            </div>

            <button class="btn btn-primary" type="submit">Guardar <i class="fas fa-save"></i></button>

        </form>
    </div>

</div>
@endsection