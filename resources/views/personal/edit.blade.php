@extends('layouts.app')

@section('titulo')
    - Editar Requerimiento de Personal
@endsection


@section('link')
    Recursos Humanos
@endsection

@section('content')

<div class="col-md-12 mb-2">
    <a href="{{ route('personal.index') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
    <a href="{{ route('personal.pdf', $personal->slug) }}" target="__blank" class="btn btn-dark"><i class="fas fa-file-pdf"></i> Ver PDF</a>
</div>

    
<div class="col-md-12">

    <form class="card" id="register" action="{{ route('personal.update', $personal->slug) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <h4 class="card-header"><b>Editar Requerimiento de Personal</b></h4>
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

                <div class="col-md-6" id="">
                    <div class="form-group">
                        <label for="" class="form-control-label">Sede <span class="text-danger">*</span></label>
                        <select name="sede_id" class="form-control" onchange="select(this.form);return false;">
                            <option value="">Seleccionar...</option>
                            @foreach ($sedes as $sede)
                                @if (old('sede_id'))
                                    <option value="{{ $sede->id }}"  {!! old('sede_id') == $sede->id ? "selected" : "" !!}>{{ $sede->descripcion }}</option>
                                @else
                                 <option value="{{ $sede->id }}"  {!! $personal->sede_id == $sede->id ? "selected" : "" !!}>{{ $sede->descripcion }}</option>
                                @endif
                            @endforeach
                        </select>
                        <b class="text-danger">{{ $errors->first('sede_id') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Dependencia, Unidad Órganica y/o Área Solicitante <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="dependencia_txt" 
                            value="{{ old('dependencia_txt') ? old('dependencia_txt') : $personal->dependencia_txt }}"
                        >
                        <b class="text-danger">{{ $errors->first('dependencia_txt') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Denominación del requerimiento o cargo <span class="text-danger">*</span></label>
                        <input type="text" name="cargo_txt" class="form-control" 
                            value="{{ old('cargo_txt') ? old('cargo_txt') : $personal->cargo_txt }}"
                        >
                        <b class="text-danger">{{ $errors->first('cargo_txt') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Cantidad Requerida <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="cantidad" 
                            value="{{ old('cantidad') ? old('cantidad') : $personal->cantidad }}"
                        >
                        <b class="text-danger">{{ $errors->first('cantidad') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Honorarios <span class="text-danger">*</span></label>
                        <textarea name="honorarios" class="form-control">
                            {{ old('honorarios') ? old('honorarios') : $personal->honorarios }}
                        </textarea>
                        <b class="text-danger">{{ $errors->first('honorarios') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Meta <span class="text-danger">*</span></label>
                        <select name="meta_id" id="" class="uppercase form-control">
                            @foreach ($metas as $meta)
                                @if (old('meta_id'))
                                    <option value="{{ $meta->id }}" {!! old('meta_id') == $meta->id ? 'checked' : null !!}>{{ $meta->meta }}</option>
                                @else
                                    <option value="{{ $meta->id }}" {!! $personal->meta_id == $meta->id ? 'checked' : null !!}>{{ $meta->meta }}</option>                                    
                                @endif
                            @endforeach
                        </select>
                        <b class="text-danger">{{ $errors->first('meta_id') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">¿Que labores se van a realizar? <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="deberes">
                            {{ old('deberes') ? old('deberes') : $personal->deberes }}
                        </textarea>
                        <b class="text-danger">{{ $errors->first('deberes') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Lugar de prestación de servicio <span class="text-danger">*</span></label>
                        <input type="text" name="lugar_txt" class="form-control" 
                            value="{{ old('lugar_txt') ? old('lugar_txt') : $personal->lugar_txt }}"
                        >
                        <b class="text-danger">{{ $errors->first('lugar_txt') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Dependencia Supervisora <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="supervisora_txt" 
                            value="{{ old('supervisora_txt') ? old('supervisora_txt') : $personal->supervisora_txt }}"
                        >
                        <b class="text-danger">{{ $errors->first('supervisora_txt') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Fecha Inicio <small>(Periodo de Contratación)</small><span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="fecha_inicio" 
                            value="{{ old('fecha_inicio') ? old('fecha_inicio') : $personal->fecha_inicio }}"
                        >
                        <b class="text-danger">{{ $errors->first('fecha_inicio') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Fecha Final <small>(Periodo de Contratación)</small><span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="fecha_final" 
                            value="{{ old('fecha_final') ? old('fecha_final') : $personal->fecha_final }}"
                        >
                        <b class="text-danger">{{ $errors->first('fecha_final') }}</b>
                    </div>
                </div>


                <div class="col-md-12">
                    <hr>
                    <b>BASE LEGAL</b>

                    <add-base :errors="{{ $errors }}" :bases="{{ old('bases') ? json_encode(old('bases')) : $personal->bases }}"></add-base>
                    <b class="text-danger">{{ $errors->first('bases') }}</b>
                </div>

                <div class="col-md-12">
                    <hr>
                    <b>PERFIL DEL PUESTO</b>
                    <add-question :errors="{{ $errors }}" :questions="{{ old('requisitos') ? json_encode(old('requisitos')) : json_encode($questions) }}"></add-question>
                </div>
            </div>        
        </div>
    </form>
</div>



@endsection