@extends('layouts.app')

@section('titulo')
    - Registro de nuevo trabajador
@endsection

@section('link')
    Recursos Humanos
@endsection

@section('content')

<div class="col-md-12 mb-2">
    <a href="{{ route('job.index') }}" class="btn btn-danger"><i class="fas fa-ban"></i> cancelar</a>
    <a href="{{ route('job.show', $job->slug()) }}" class="btn btn-primary"><i class="fas fa-user"></i> perfil</a>
    <btn-work-config theme="btn-dark"
        param="{{ $job->id }}"
        nombre_completo="{{ $job->nombre_completo }}"
        :sindicatos="{{ $job->sindicatos }}"
    >
        <i class="fas fa-cogs"></i> Configuración
    </btn-work-config>
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
                        <label for="" class="form-control-label">Email</label>
                        <input type="email" class="form-control" value="{{ old('email') ? old('email') : $job->email }}" name="email">
                        <b class="text-danger">{{ $errors->first('email') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Dirección <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="{{ old('direccion') ? old('direccion') : $job->direccion }}" name="direccion">
                        <b class="text-danger">{{ $errors->first('direccion') }}</b>
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
                        <label for="" class="form-control-label">N° Essalud Autogenerable</label>
                        <input type="text" class="form-control" value="{{ $job->numero_de_essalud }}" name="numero_de_essalud">
                        <b class="text-danger">{{ $errors->first('numero_de_essalud') }}</b>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Banco</label>
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
                        <label for="" class="form-control-label">Número de Cuenta</label>
                        <input type="text" class="form-control" value="{{ $job->numero_de_cuenta }}" name="numero_de_cuenta">
                        <b class="text-danger">{{ $errors->first('numero_de_cuenta') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">AFP</label>
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
                        <label for="" class="form-control-label">Tipo de AFP</label>
                        <select class="form-control" name="type_afp">
                            <option value="">Seleccionar...</option>
                            @if (old('type_afp'))
                                <option value="flujo" {!! old('type_afp') == 'flujo' ? 'selected' : null !!}>Flujo</option>
                                <option value="mixta" {!! old('type_afp') == 'mixta' ? 'selected' : null !!}>Mixta</option>
                            @else   
                                <option value="flujo" {!! $job->type_afp == 'flujo' ? 'selected' : null !!}>Flujo</option>
                                <option value="mixta" {!! $job->type_afp == 'mixta' ? 'selected' : null !!}>Mixta</option>
                            @endif
                        </select>
                        <b class="text-danger">{{ $errors->first('type_afp') }}</b>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Fecha de Afiliación</label>
                        <input type="date" class="form-control" value="{{ $job->fecha_de_afiliacion }}" name="fecha_de_afiliacion">
                        <b class="text-danger">{{ $errors->first('fecha_de_afiliacion') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Número de cussp</label>
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
                        <label for="" class="form-control-label">Afectación</label> <br>
                        <input type="checkbox" name="afecto" {!! $job->afecto ? 'checked' : '' !!}>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Descanso medico por Maternidad</label>
                        <br>
                        <input type="checkbox" name="descanso" {!! $job->descanso ? 'checked' : '' !!}>
                    </div>
                </div>

                <div class="col-md-12 mt-4">
                    <button class="btn btn-success">
                        Guardar y continuar <i class="fas fa-save"></i>
                    </button>
                </div>

            </div>        
        </div>
    </form>
</div>
@endsection