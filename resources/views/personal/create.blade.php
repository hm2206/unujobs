@extends('layouts.app')

@section('titulo')
    - Registro de Requerimiento de Personal
@endsection


@section('link')
    Recursos Humanos
@endsection

@section('content')

<div class="col-md-12">
    <a href="{{ route('home') }}" class="btn btn-warning"><i class="material-icons">undo</i> atrás</a>
</div>
    
<div class="col-md-12">
    <form class="card" id="register" action="{{ route('personal.store') }}" method="post">
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
                        <label for="" class="form-control-label">Dependencia <span class="text-danger">*</span></label>
                        <select name="dependencia_id" value="{{ old('dependencia_id') }}" class="form-control" onchange="select(this.form);return false;">
                            <option value="">Seleccionar...</option>
                            @foreach ($dependencias as $dependencia)
                                <option value="{{ $dependencia->id }}" {!! old('dependencia_id') == $dependencia->id ? "selected" : "" !!}>{{ $dependencia->descripcion }}</option>
                            @endforeach
                        </select>
                        <b class="text-danger">{{ $errors->first('dependencia_id') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Oficina Solicitante</label>
                        <select name="oficina_id" value="{{ old('oficina_id') }}" class="form-control">
                            <option value="">Seleccionar...</option>
                            @foreach ($oficinas as $oficina)
                                <option value="{{ $oficina->id }}"  {!! old('oficina_id') == $oficina->id ? "selected" : "" !!}>{{ $oficina->descripcion }}</option>
                            @endforeach
                        </select>
                        <b class="text-danger">{{ $errors->first('oficina_id') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Denominación del requerimiento o cargo <span class="text-danger">*</span></label>
                        <select name="cargo_id" class="form-control" value="{{ old('cargo_id') }}">
                            <option value="">Seleccionar...</option>
                            @foreach ($cargos as $cargo)
                                <option value="{{ $cargo->id }}" {!! old('cargo_id') == $cargo->id ? "selected" : "" !!}>{{ $cargo->descripcion }}</option>
                            @endforeach
                        </select>
                        <b class="text-danger">{{ $errors->first('cargo_id') }}</b>
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
                        <input type="number" class="form-control" name="honorarios" value="{{ old('honorarios') }}">
                        <b class="text-danger">{{ $errors->first('honorarios') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Meta <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="meta_id" value="{{ old('meta_id') }}">
                        <b class="text-danger">{{ $errors->first('meta_id') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Fuente <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="fuente_id" value="{{ old('fuente_id') }}">
                        <b class="text-danger">{{ $errors->first('fuente_id') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Especificación de gastos<span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="gasto" value="{{ old('gasto') }}">
                        <b class="text-danger">{{ $errors->first('gasto') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Periodo de contratación <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="periodo" value="{{ old('periodo') }}">
                        <b class="text-danger">{{ $errors->first('periodo') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Lugar de prestación de servicio <span class="text-danger">*</span></label>
                        <select name="lugar_id" id="" class="form-control">
                            <option value="">Seleccionar...</option>
                            @foreach ($lugares as $lugar)
                                <option value="{{ $lugar->id }}"  {!! old('lugar_id') == $lugar->id ? "selected" : "" !!}>{{ $lugar->descripcion }}</option>
                            @endforeach
                        </select>
                        <b class="text-danger">{{ $errors->first('lugar_id') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Dependencia Supervisora</label>
                        <input type="text" class="form-control">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Perfil del puesto <span class="text-danger">*</span></label>
                        <textarea name="perfil" class="form-control">{{ old('perfil') }}</textarea>
                        <b class="text-danger">{{ $errors->first('perfil') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Principales funciones</label>
                        <input type="text" class="form-control">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Lista de preguntas y respuestas</label>
                        <input type="text" class="form-control">
                    </div>
                </div>


                <div class="col-md-12 mt-4">
                    <button class="btn btn-primary">Guardar <i class="material-icons">save</i></button>
                </div>

            </div>        
        </div>
    </form>
</div>


<script>

    function select(form) {
        form.submit();
    }

</script>

@endsection