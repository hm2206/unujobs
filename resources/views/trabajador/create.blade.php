@extends('layouts.app')

@section('titulo')
    - Registro de nuevo trabajador
@endsection

@section('link')
    Recursos Humanos
@endsection

@section('content')

<div class="col-md-12 mb-2">
    <a href="{{ route('job.index') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
</div>
    
<div class="col-md-12">

    <form method="GET" class="card mb-2">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="number" placeholder="Número de documento" name="documento" value="{{ request()->documento }}" class="form-control" autofocus>
                        @if ($result->success == false)
                            <span class="text-danger">
                                {{ $result->message }}
                            </span>
                        @endif  
                    </div>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-info">Buscar <i class="fas fa-search"></i></button>
                </div>
            </div>
        </div>
    </form>


    <form class="card" action="{{ route('job.store') }}" method="POST">
        @csrf
        <h4 class="card-header"><b>Datos Generales</b></h4>
        <div class="card-body">

            <span>Campo obligatorio (<span class="text-danger">*</span>)</span>
            <hr />

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
                        <input type="text" class="form-control" name="ape_paterno" value="{{ $result->success ? $result->result->paterno : old('ape_paterno') }}">
                        <b class="text-danger">{{ $errors->first('ape_paterno') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Apellido Materno <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="ape_materno" value="{{ $result->success ? $result->result->materno : old('ape_materno') }}">
                        <b class="text-danger">{{ $errors->first('ape_materno') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Nombres <span class="text-danger">*</span></label>
                        <input type="text" name="nombres" class="form-control" value="{{ $result->success ? $result->result->nombre : old('nombres') }}">
                        <b class="text-danger">{{ $errors->first('nombres') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Número de documento <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" value="{{ $result->success ? $result->result->dni : old('numero_de_documento') }}" name="numero_de_documento">
                        <b class="text-danger">{{ $errors->first('numero_de_documento') }}</b>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Fecha de nacimiento <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" value="{{ $result->success ? $result->result->nacimiento_parse : old('fecha_de_nacimiento') }}" name="fecha_de_nacimiento">
                        <b class="text-danger">{{ $errors->first('fecha_de_nacimiento') }}</b>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Profesión <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="profesion" value="{{ old('profesion') }}">
                        <b class="text-danger">{{ $errors->first('profesion') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Dirección <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="{{ old('direccion') }}" name="direccion">
                        <b class="text-danger">{{ $errors->first('direccion') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Número de Teléfono <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" value="{{ old('phone') }}" name="phone">
                        <b class="text-danger">{{ $errors->first('phone') }}</b>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Fecha de ingreso <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" value="{{ old('fecha_de_ingreso') }}" name="fecha_de_ingreso">
                        <b class="text-danger">{{ $errors->first('fecha_de_ingreso') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Sexo <span class="text-danger">*</span></label>
                        <select name="sexo" id="" class="form-control">
                            <option value="">Seleccionar...</option>
                            <option value="1" {!! old('sexo') == 1 ? 'selected' : '' !!}>Masculino</option>
                            <option value="0" {!! old('sexo') == 0 ? 'selected' : '' !!}>Femenino</option>
                        </select>
                        <b class="text-danger">{{ $errors->first('sexo') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">N° Essalud Autogenerable</label>
                        <input type="text" class="form-control" value="{{ old('numero_de_essalud') }}" name="numero_de_essalud">
                        <b class="text-danger">{{ $errors->first('numero_de_essalud') }}</b>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Banco</label>
                        <select name="banco_id" class="form-control">
                            <option value="">Seleccionar...</option>
                            @foreach ($bancos as $banco)
                                <option value="{{ $banco->id }}" {!! old('banco_id') == $banco->id ? 'selected': '' !!}>{{ $banco->nombre }}</option>
                            @endforeach
                        </select>
                        <b class="text-danger">{{ $errors->first('banco_id') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Número de Cuenta</label>
                        <input type="text" class="form-control" value="{{ old('numero_de_cuenta') }}" name="numero_de_cuenta">
                        <b class="text-danger">{{ $errors->first('numero_de_cuenta') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">AFP</label>
                        <select class="form-control" name="afp_id">
                            <option value="">Seleccionar...</option>
                            @foreach ($afps as $afp)
                                <option value="{{ $afp->id }}" {!! old('afp_id') == $afp->id ? 'selected': '' !!}>{{ $afp->nombre }}</option>
                            @endforeach
                        </select>
                        <b class="text-danger">{{ $errors->first('afp_id') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Fecha de Afiliación</label>
                        <input type="date" class="form-control" value="{{ old('fecha_de_afiliacion') }}" name="fecha_de_afiliacion">
                        <b class="text-danger">{{ $errors->first('fecha_de_afiliacion') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Número de cussp</label>
                        <input type="text" class="form-control" value="{{ old('numero_de_cussp') }}" name="numero_de_cussp">
                        <b class="text-danger">{{ $errors->first('numero_de_cussp') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Accidentes de Trabajo <span class="text-danger">*</span></label>
                        <select name="accidentes"class="form-control">
                            <option value="">Seleccionar...</option>
                            <option value="0" {!! old('accidentes') == 0 ? 'selected' : '' !!}>No Afecto</option>
                            <option value="1" {!! old('accidentes') == 1 ? 'selected' : '' !!}>Afecto</option>
                        </select>
                        <b class="text-danger">{{ $errors->first('accidentes') }}</b>
                    </div>
                </div>

                <div class="col-md-12 mt-4">
                    <button class="btn btn-success">Guardar y continuar <i class="fas fa-save"></i></button>
                </div>

            </div>        
        </div>
    </form>
</div>

<script>

function combobox(that, id) {
    let combo = document.getElementById(id);
    combo.value = "";
    that.form.submit();
}


</script>

@endsection