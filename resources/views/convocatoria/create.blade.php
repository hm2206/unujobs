@extends('layouts.app')

@section('titulo')
    - Registro de Convocatoria
@endsection

@section('link')
    Recursos Humanos
@endsection

@section('content')

<div class="col-md-12">
    <a href="{{ route('home') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
</div>
    
<div class="col-md-12">
    <form class="card" action="{{ route('convocatoria.store') }}" method="POST">
        @csrf
        <h4 class="card-header">Registro de Convocatoria</h4>
        <div class="card-body">

            <span>Campo obligatorio (<span class="text-danger">*</span>)</span>
            <hr>

            <div class="row">

                @if(session('success'))
                    <div class="col-md-12">
                        <div class="alert alert-success" role="alert">
                            <b>{{ session('success') }}</b>
                        </div>
                    </div>
                @endif

                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Número de convocatoria <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="numero_de_convocatoria" value="{{ old('numero_de_convocatoria') }}">
                        <b class="text-danger">{{ $errors->first('numero_de_convocatoria') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Periodo de convocatoria <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="periodo" value="{{ old('periodo') }}">
                        <b class="text-danger">{{ $errors->first('periodo') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Área responsable del proceso <span class="text-danger">*</span></label>
                        <input type="text" name="area_responsable" class="form-control" value="{{ old('area_responsable') }}">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Código de postulación <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="codigo_de_postulacion" value="{{ old('codigo_de_postulacion') }}">
                        <b class="text-danger">{{ $errors->first('codigo_de_postulacion') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Selección de la oficina que realiza el requerimiento <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="{{ old('oficina_id') }}" name="oficina_id">
                        <b class="text-danger">{{ $errors->first('oficina_id') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Asignar factores de evaluación <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="factor_evaluacion" value="{{ old('factor_evaluacion') }}">
                        <b class="text-danger">{{ $errors->first('factor_evaluacion') }}</b>
                    </div>
                </div>

                <div class="col-md-12 mt-4">
                    <button class="btn btn-primary">Guardar <i class="material-icons">save</i></button>
                </div>

            </div>        
        </div>
    </form>
</div>

@endsection