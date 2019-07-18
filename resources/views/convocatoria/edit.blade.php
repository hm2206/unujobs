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
    <form class="card" action="{{ route('convocatoria.update', $convocatoria->id) }}" method="POST">
        @csrf
        @method('PUT')
        <h5 class="card-header">Actualizar Convocatoria</h5>
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
                        <input type="text" class="form-control" name="numero_de_convocatoria" 
                            value="{{ old('numero_de_convocatoria') ? old('numero_de_convocatoria') : $convocatoria->numero_de_convocatoria }}"
                        >
                        <b class="text-danger">{{ $errors->first('numero_de_convocatoria') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Observación <span class="text-danger">*</span></label>
                        <textarea name="observacion" class="form-control">
                            {{ old('observacion') ? old('observacion') : $convocatoria->observacion }}
                        </textarea>
                        <b class="text-danger">{{ $errors->first('observacion') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Fecha Inicio <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="fecha_inicio" 
                            value="{{ old('fecha_inicio') ? old('fecha_inicio') : $convocatoria->fecha_inicio }}"
                        >
                        <b class="text-danger">{{ $errors->first('fecha_inicio') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Fecha Final <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="fecha_final" 
                            value="{{ old('fecha_final') ? old('fecha_final') : $convocatoria->fecha_final }}"
                        >
                        <b class="text-danger">{{ $errors->first('fecha_final') }}</b>
                    </div>
                </div>


                <div class="col-md-12">
                    <hr>
                    <h5>Requerimientos de Personal</h5>

                    <div class="row mt-3 mb-3">
                        @foreach ($personals as $personal)
                            <div class="col-md-4">
                                <input type="checkbox" value="{{ $personal->id }}" {!! $personal->convocatoria_id == $convocatoria->id ? 'checked' : null !!} 
                                    name="personals[]"
                                >
                                {{ $personal->cargo_txt }}
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-md-12">
                    <hr>
                    <h5>Cronograma de Actividades</h5>

                    <add-actividades :errors="{{ $errors }}" :activities="{{  old('activities') ? json_encode( old('activities')) : json_encode($actividades) }}"></add-actividades>
                   
                </div>

            </div>        
        </div>
    </form>
</div>

@endsection