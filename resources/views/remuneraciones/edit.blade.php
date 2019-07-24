@extends('layouts.app')

@section('titulo')
    - Descuento
@endsection

@section('link')
    Planilla
@endsection

@section('content')

<div class="col-md-12 mb-2">
    <a href="{{ route('remuneracion.index') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
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
            Registro de remuneración</b>
        </div>
        <form class="card-body" action="{{ route('remuneracion.update', $remuneracion->id) }}" method="post">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="" class="form-control-label">Clave <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="key" value="{{ old('key') ? old('key') : $remuneracion->key }}">
                <b class="text-danger">{{ $errors->first('key') }}</b>
            </div>

            <div class="form-group">
                <label for="" class="form-control-label">Descripción <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="descripcion" value="{{ old('descripcion') ? old('descripcion') : $remuneracion->descripcion }}">
                <b class="text-danger">{{ $errors->first('descripcion') }}</b>
            </div>

            <div class="form-group">
                <label for="" class="form-control-label">No considerar a la base imponible <small>(Opcional)</small></label>
                <br>
                @if (old('base'))
                    <input type="checkbox"  name="base" {!! old('base') ? 'checked' :null !!}>                    
                @else
                    <input type="checkbox"  name="base" {!! $remuneracion->base ? 'checked' :null !!}>
                @endif
                <b class="text-danger">{{ $errors->first('base') }}</b>
            </div>

            <button class="btn btn-success" type="submit">Guardar <i class="fas fa-save"></i></button>

        </form>
    </div>

</div>
@endsection