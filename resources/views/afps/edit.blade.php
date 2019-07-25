@extends('layouts.app')

@section('titulo')
    - Descuento
@endsection

@section('link')
    Planilla
@endsection

@section('content')

<div class="col-md-12 mb-2">
    <a href="{{ route('afp.index') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
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
            Registro de AFP</b>
        </div>
        <form class="card-body" action="{{ route('afp.update', $afp->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="" class="form-control-label">Nombre <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="nombre" value="{{ old('nombre') ? old('nombre') : $afp->nombre }}">
                <b class="text-danger">{{ $errors->first('nombre') }}</b>
            </div>

            <div class="form-group">
                <label for="" class="form-control-label">Flujo <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="flujo" value="{{ old('flujo') ? old('flujo') : $afp->flujo }}">
                <b class="text-danger">{{ $errors->first('flujo') }}</b>
            </div>

            <div class="form-group">
                <label for="" class="form-control-label">Mixta <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="mixta" value="{{ old('mixta') ? old('mixta') : $afp->mixta }}">
                <b class="text-danger">{{ $errors->first('mixta') }}</b>
            </div>

            <div class="form-group">
                <label for="" class="form-control-label">Aporte <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="aporte" value="{{ old('aporte') ? old('aporte') : $afp->aporte }}">
                <b class="text-danger">{{ $errors->first('aporte') }}</b>
            </div>

            <div class="form-group">
                <label for="" class="form-control-label">Prima <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="prima" value="{{ old('prima') ? old('prima') : $afp->prima }}">
                <b class="text-danger">{{ $errors->first('prima') }}</b>
            </div>

            <button class="btn btn-success" type="submit">Guardar <i class="fas fa-save"></i></button>

        </form>
    </div>

</div>
@endsection