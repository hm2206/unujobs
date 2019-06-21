@extends('layouts.app')

@section('titulo')
    - Registro de nuevo trabajador
@endsection

@section('link')
    Recursos Humanos
@endsection

@section('content')

<div class="col-md-12">
    <a href="{{ route('job.index') }}" class="btn btn-danger"><i class="fas fa-ban"></i> cancelar</a>
    <a href="{{ route('job.show', $job->id) }}" class="btn btn-primary"><i class="fas fa-user"></i> perfil</a>
</div>
    
<div class="col-md-12">

    <form class="card" action="{{ route('job.update', $job->id) }}" method="POST" id="register">
        @csrf
        @method('PUT')
        <h4 class="card-header"><b>Datos Generales</b></h4>
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
                        <label for="" class="form-control-label">Apellido Paterno <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="ape_paterno" value="{{ $job->ape_paterno }}">
                        <b class="text-danger">{{ $errors->first('ape_paterno') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Apellido Materno <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="ape_materno" value="{{  $job->ape_materno }}">
                        <b class="text-danger">{{ $errors->first('ape_materno') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Nombres <span class="text-danger">*</span></label>
                        <input type="text" name="nombres" class="form-control" value="{{ $job->nombres }}">
                        <b class="text-danger">{{ $errors->first('nombres') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Número de documento <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" value="{{ $job->numero_de_documento }}" name="numero_de_documento">
                        <b class="text-danger">{{ $errors->first('numero_de_documento') }}</b>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Fecha de nacimiento <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" value="{{ $job->fecha_de_nacimiento }}" name="fecha_de_nacimiento">
                        <b class="text-danger">{{ $errors->first('fecha_de_nacimiento') }}</b>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Profesión <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="profesion" value="{{ $job->profesion }}">
                        <b class="text-danger">{{ $errors->first('profesion') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Número de Teléfono <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" value="{{ $job->phone }}" name="phone">
                        <b class="text-danger">{{ $errors->first('phone') }}</b>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Fecha de ingreso <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" value="{{ $job->fecha_de_ingreso }}" name="fecha_de_ingreso">
                        <b class="text-danger">{{ $errors->first('fecha_de_ingreso') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Sexo <span class="text-danger">*</span></label>
                        <select name="sexo" id="" class="form-control">
                            <option value="">Seleccionar...</option>
                            <option value="1" {!! $job->sexo == 1 ? 'selected' : '' !!}>Masculino</option>
                            <option value="0" {!! $job->sexo == 0 ? 'selected' : '' !!}>Femenino</option>
                        </select>
                        <b class="text-danger">{{ $errors->first('sexo') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">N° Essalud Autogenerable <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="{{ $job->numero_de_essalud }}" name="numero_de_essalud">
                        <b class="text-danger">{{ $errors->first('numero_de_essalud') }}</b>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Banco <span class="text-danger">*</span></label>
                        <select name="banco_id" class="form-control">
                            <option value="">Seleccionar...</option>
                            @foreach ($bancos as $banco)
                                <option value="{{ $banco->id }}" {!! $job->banco_id == $banco->id ? 'selected': '' !!}>{{ $banco->nombre }}</option>
                            @endforeach
                        </select>
                        <b class="text-danger">{{ $errors->first('banco_id') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Número de Cuenta <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="{{ $job->numero_de_cuenta }}" name="numero_de_cuenta">
                        <b class="text-danger">{{ $errors->first('numero_de_cuenta') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">AFP <span class="text-danger">*</span></label>
                        <select class="form-control" name="afp_id">
                            <option value="">Seleccionar...</option>
                            @foreach ($afps as $afp)
                                <option value="{{ $afp->id }}" {!! $job->afp_id == $afp->id ? 'selected': '' !!}>{{ $afp->nombre }}</option>
                            @endforeach
                        </select>
                        <b class="text-danger">{{ $errors->first('afp_id') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Fecha de Afiliación <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" value="{{ $job->fecha_de_afiliacion }}" name="fecha_de_afiliacion">
                        <b class="text-danger">{{ $errors->first('fecha_de_afiliacion') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Número de cussp <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="{{ $job->numero_de_cussp }}" name="numero_de_cussp">
                        <b class="text-danger">{{ $errors->first('numero_de_cussp') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Accidentes de Trabajo <span class="text-danger">*</span></label>
                        <select name="accidentes"class="form-control">
                            <option value="">Seleccionar...</option>
                            <option value="0" {!! $job->accidentes == 0 ? 'selected' : '' !!}>No Afecto</option>
                            <option value="1" {!! $job->accidentes == 1 ? 'selected' : '' !!}>Afecto</option>
                        </select>
                        <b class="text-danger">{{ $errors->first('accidentes') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Sindicatos</label>
                        <select name="sindicato_id"class="form-control">
                            <option value="">Seleccionar...</option>
                            @foreach ($sindicatos as $sindicato)
                                <option value="{{ $sindicato->id }}" {!! $job->sindicato_id == $sindicato->id ? 'selected' : '' !!}>{{ $sindicato->nombre }}</option>
                            @endforeach
                        </select>
                        <b class="text-danger">{{ $errors->first('sindicato_id') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Cargo <span class="text-danger">*</span></label>
                        <select name="cargo_id"class="form-control" onchange="select(this);return false;">
                            <option value="">Seleccionar...</option>
                            @foreach ($cargos as $cargo)
                                @if (old('cargo_id'))
                                    <option value="{{ $cargo->id }}" {!! old('cargo_id') == $cargo->id ? 'selected': '' !!}>{{ $cargo->descripcion }}</option>
                                @else
                                    <option value="{{ $cargo->id }}" {!! $job->cargo_id == $cargo->id ? 'selected': '' !!}>{{ $cargo->descripcion }}</option>
                                @endif
                            @endforeach
                        </select>
                        <b class="text-danger">{{ $errors->first('cargo_id') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Categoria <span class="text-danger">*</span></label>
                        <select name="categoria_id"class="form-control" id="categoria">
                            <option value="">Seleccionar...</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}" {!! $job->categoria_id == $categoria->id ? 'selected' : '' !!}>{{ $categoria->nombre }}</option>
                            @endforeach
                        </select>
                        <b class="text-danger">{{ $errors->first('categoria_id') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Plaza</label>
                        <input type="text" name="plaza" class="form-control" value="{{ old('plaza') ? old('plaza') : $job->plaza }}">
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label for="" class="form-control-label">Observaciones</label>
                        <textarea name="observaciones" class="form-control form-textarea" rows="10">{{ $job->observaciones ? $job->observaciones : old('observaciones') }}</textarea>
                        <b class="text-danger">{{ $errors->first('observaciones') }}</b>
                    </div>
                </div>

                <div class="col-md-12 mt-4">
                    <button class="btn btn-primary">
                        Guardar y continuar <i class="fas fa-save"></i>
                    </button>
                </div>

            </div>        
        </div>
    </form>
</div>

<script>
    function select(that) {
        let categoria = document.getElementById('categoria');
        categoria.value = "";
        that.form.submit();
    }
</script>

@endsection