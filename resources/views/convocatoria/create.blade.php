@extends('layouts.app')

@section('titulo')
    - Registro de Convocatoria
@endsection

@section('link')
    Recursos Humanos
@endsection

@section('content')

<div class="col-md-12 mb-2">
    <a href="{{ route('convocatoria.index') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
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
                        <label for="" class="form-control-label">Observación <span class="text-danger">*</span></label>
                        <textarea name="observacion" class="form-control">{{ old('observacion') }}</textarea>
                        <b class="text-danger">{{ $errors->first('observacion') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Fecha Inicio <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="fecha_inicio" value="{{ old('fecha_inicio') }}">
                        <b class="text-danger">{{ $errors->first('fecha_inicio') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Fecha Final <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="fecha_final" value="{{ old('fecha_final') }}">
                        <b class="text-danger">{{ $errors->first('fecha_final') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Requerimiento de  Personal <span class="text-danger">*</span></label>
                        <select name="personal_id" class="form-control">
                            <option value="">Seleccionar...</option>
                            @foreach ($personals as $personal)
                                <option value="{{ $personal->id }}">{{ $personal->numero_de_requerimiento }}</option>
                            @endforeach
                        </select>
                        <b class="text-danger">{{ $errors->first('personal_id') }}</b>
                    </div>
                </div>
                

                <div class="col-md-12 mt-4">
                    <button class="btn btn-success">Guardar <i class="fas fa-save"></i></button>
                </div>

            </div>        
        </div>
    </form>
</div>

@endsection