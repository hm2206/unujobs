@extends('layouts.app')

@section('titulo')
    - Trabajadores
@endsection

@section('link')
    Planilla
@endsection

@section('content')

<div class="col-md-12 mb-2">
    <a href="{{ route('job.index') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
    <a href="{{ route('job.edit', $job->slug()) }}" class="btn btn-warning"><i class="fas fa-pencil-alt"></i> editar</a>
    <btn-work-config theme="btn-dark"
        param="{{ $job->id }}"
        nombre_completo="{{ $job->nombre_completo }}"
        :sindicatos="{{ $job->sindicatos }}"
    >
        <i class="fas fa-cog"></i> Configuración
    </btn-work-config>
</div>

@if (session('success'))
    <div class="col-md-12 mt-3 ">
        <div class="alert alert-success">
            {{ session('success') }}       
        </div>
    </div>
@endif


<div class="col-md-12">

    <h3>Trabajador <span class="text-danger">>> </span> <b class="uppercase">{{ $job->nombre_completo }}</b> </h3>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Apellido Paterno</label>
                        <small class="form-control uppercase">{{ $job->ape_paterno }}</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Apellido Materno</label>
                        <span class="form-control uppercase">{{ $job->ape_materno }}</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Nombres</label>
                        <span class="form-control uppercase">{{ $job->nombres }}</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Número de documento</label>
                        <span class="form-control ">{{ $job->numero_de_documento }}</span>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Fecha de nacimiento</label>
                        <span class="form-control">{{ $job->fecha_de_nacimiento }}</span>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Email</label>
                        <span class="form-control uppercase">{{ $job->email}}</span>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Dirección</label>
                        <span class="form-control uppercase">{{ $job->direccion }}</span>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Profesión</label>
                        <span class="form-control uppercase">{{ $job->profesion }}</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Número de Teléfono</label>
                        <span class="form-control">{{ $job->phone }}</span>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Fecha de ingreso</label>
                        <span class="form-control">{{ $job->fecha_de_ingreso }}</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Sexo</label>
                        <span class="form-control uppercase">{{ $job->sexo == 1 ? "Masculino": "Femenino" }}</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">N° Essalud Autogenerable</label>
                        <span class="form-control">{{ $job->numero_de_essalud }}</span>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Banco</label>
                        <span class="form-control">{{ $job->banco ? $job->banco->nombre: '' }}</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Número de Cuenta</label>
                        <span class="form-control">{{ $job->numero_de_cuenta }}</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">AFP</label>
                        <span class="form-control">{{ $job->afp ? $job->afp->nombre : '' }}</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Tipo de AFP</label>
                        <span class="form-control">{{ $job->type_afp }}</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Fecha de Afiliación</label>
                        <span class="form-control">{{ $job->fecha_de_afiliacion }}</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Número de cussp</label>
                        <span class="form-control">{{ $job->numero_de_cussp }}</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Accidentes de Trabajo</label>
                        <span class="form-control uppercase">{{ $job->accidentes ? 'Afecto': 'No afecto' }}</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Descanso medico por Maternidad</label>
                        <br>
                        <input type="checkbox" name="descanso" {!! $job->descanso ? 'checked' : '' !!}>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Afecto</label>
                        <span class="form-control">{{ $job->afecto ? 'Si' : 'No' }}</span>
                    </div>
                </div>

            </div>
        </div>
    </div> 
</div>
@endsection