@extends('layouts.app')

@section('titulo')
    - Registro de Requerimiento de Personal
@endsection


@section('link')
    Recursos Humanos
@endsection

@section('content')

<div class="col-md-12 mb-2">
    <a href="{{ route('personal.index') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
</div>
    
<div class="col-md-12">

    <form class="card" id="register" action="{{ route('personal.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <h4 class="card-header"><b>Registro de Requerimiento de Personal</b></h4>
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
                        <label for="" class="form-control-label">Número de Requerimiento <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="numero_de_requerimiento" 
                            value="{{ old('numero_de_requerimiento') }}">
                        <b class="text-danger">{{ $errors->first('numero_de_requerimiento') }}</b>
                    </div>
                </div>

                <div class="col-md-6" id="">
                    <div class="form-group">
                        <label for="" class="form-control-label">Sede <span class="text-danger">*</span></label>
                        <select name="sede_id" class="form-control" onchange="select(this.form);return false;">
                            <option value="">Seleccionar...</option>
                            @foreach ($sedes as $sede)
                                <option value="{{ $sede->id }}"  {!! old('sede_id') == $sede->id ? "selected" : "" !!}>{{ $sede->descripcion }}</option>
                            @endforeach
                        </select>
                        <b class="text-danger">{{ $errors->first('sede_id') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Dependencia, Unidad Órganica y/o Área Solicitante <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="dependencia_txt" value="{{ old('dependencia_txt') }}">
                        <b class="text-danger">{{ $errors->first('dependencia_txt') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Denominación del requerimiento o cargo <span class="text-danger">*</span></label>
                        <input type="text" name="cargo_txt" class="form-control" value="{{ old('cargo_txt') }}">
                        <b class="text-danger">{{ $errors->first('cargo_txt') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Cantidad Requerida <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="cantidad" value="{{ old('cantidad') }}">
                        <b class="text-danger">{{ $errors->first('cantidad') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Honorarios <span class="text-danger">*</span></label>
                        <textarea name="honorarios" class="form-control">{{ old('honorarios') }}</textarea>
                        <b class="text-danger">{{ $errors->first('honorarios') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Meta <span class="text-danger">*</span></label>
                        <select name="meta_id" id="" class="uppercase form-control">
                            @foreach ($metas as $meta)
                                <option value="{{ $meta->id }}" {!! old('meta_id') ? 'checked' : null !!}>{{ $meta->meta }}</option>
                            @endforeach
                        </select>
                        <b class="text-danger">{{ $errors->first('meta_id') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">¿Que labores se van a realizar? <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="deberes">{{ old('deberes') }}</textarea>
                        <b class="text-danger">{{ $errors->first('deberes') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Lugar de prestación de servicio <span class="text-danger">*</span></label>
                        <input type="text" name="lugar_txt" class="form-control" value="{{ old('lugar_txt') ? old('lugar_txt') : 'Universidad Nacional de Ucayali' }}">
                        <b class="text-danger">{{ $errors->first('lugar_txt') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Dependencia Supervisora <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="supervisora_txt" 
                            value="{{ old('supervisora_txt') ? old('supervisora_txt') : 'Oficina Ejecutiva de Recursos Humanos' }}"
                        >
                        <b class="text-danger">{{ $errors->first('supervisora_txt') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Fecha Inicio <small>(Periodo de Contratación)</small><span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="fecha_inicio" value="{{ old('fecha_inicio') }}">
                        <b class="text-danger">{{ $errors->first('fecha_inicio') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Fecha Final <small>(Periodo de Contratación)</small><span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="fecha_final" value="{{ old('fecha_final') }}">
                        <b class="text-danger">{{ $errors->first('fecha_final') }}</b>
                    </div>
                </div>


                <div class="col-md-12">
                    <hr>
                    <b>BASE LEGAL</b>

                    <add-base :errors="{{ $errors }}" :bases="{{ old('bases') ? json_encode(old('bases')) : json_encode([]) }}"></add-base>
                    <b class="text-danger">{{ $errors->first('bases') }}</b>
                </div>

                <div class="col-md-12">
                    <hr>
                    <b>PERFIL DEL PUESTO</b>
                    <add-question :errors="{{ $errors }}" :questions="{{ old('requisitos') ? json_encode(old('requisitos')) : json_encode([]) }}"></add-question>
                </div>
            </div>        
        </div>
    </form>
</div>



@endsection