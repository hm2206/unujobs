@extends('layouts.app')

@section('titulo')
    - Crear Cronograma
@endsection

@section('link')
    Planilla
@endsection

@section('content')

<div class="col-md-12">
    <a href="{{ route('cronograma.index') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
</div>


@if (session('success'))
    <div class="col-md-12 mt-3 ">
        <div class="alert alert-success">
            {{ session('success') }}       
        </div>
    </div>
@elseif(session('danger'))
    <div class="col-md-12 mt-3 ">
        <div class="alert alert-danger">
            {{ session('danger') }}       
        </div>
    </div>
@endif

<div class="col-md-7">

    <div class="card">
        <div class="card-header">
            Registro de cronograma</b>
        </div>
        <form class="card-body" action="{{ route('cronograma.store') }}" method="post">
            @csrf

            <div class="form-group">
                <label for="" class="form-control-label">Planilla <span class="text-danger">*</span></label>
                <select name="planilla_id" class="form-control uppercase">
                    <option value="">Seleccionar...</option>
                    @foreach ($planillas as $planilla)
                        <option value="{{ $planilla->id }}" {!! old('planilla_id') ? "selected" : null !!}>{{ $planilla->descripcion }}</option>
                    @endforeach
                </select>
                <b class="text-danger">{{ $errors->first('planilla_id') }}</b>
            </div>

            <div class="form-group">
                <label for="" class="form-control-label">Dias</label>
                <input type="number" class="form-control" name="dias" value="30">
                <b class="text-danger">{{ $errors->first('dias') }}</b>
            </div>

            <div class="form-group">
                <label for="" class="form-control-label">Observación</label>
                <textarea name="observacion" class="form-control">{{ old('observacion') }}</textarea>
            </div>

            <div class="form-group">
                <label for="" class="form-control-label">Adicional</label>
                <br>
                <input type="checkbox" name="adicional" {!! old('adicional') ? 'checked' : '' !!}>
                <b class="text-danger">{{ $errors->first('adicional') }}</b>
            </div>

            <button class="btn btn-primary mt-4" type="submit">Guardar <i class="fas fa-save"></i></button>

        </form>
    </div>

</div>
@endsection