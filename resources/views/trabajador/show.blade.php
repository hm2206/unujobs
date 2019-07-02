@extends('layouts.app')

@section('titulo')
    - Trabajadores
@endsection

@section('link')
    Planilla
@endsection

@section('content')

<div class="col-md-12">
    <a href="{{ route('job.index') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
    <a href="{{ route('job.edit', $job->id) }}" class="btn btn-warning"><i class="fas fa-pencil-alt"></i> editar</a>
    <a href="{{ route('job.remuneracion', $job->id) }}" class="btn btn-dark">Remuneraciones</a>
    <a href="{{ route('job.descuento', $job->id) }}" class="btn btn-dark">Descuentos</a>
    <a href="{{ route('job.obligacion', $job->id) }}" class="btn btn-dark">Obligaciones Judiciales</a>
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
                        <span class="form-control uppercase">{{ $job->ape_paterno }}</span>
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
                        <label for="" class="form-control-label">Sindicatos</label>
                        <span class="form-control uppercase">{{ $job->sindicato ? $job->sindicato->nombre : ''}}</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Cargo</label>
                        <span class="form-control uppercase">{{ $job->cargo ? $job->cargo->descripcion : ''}}</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Categoria</label>
                        <span class="form-control uppercase">{{ $job->categoria ? $job->categoria->nombre : ''}}</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Condicion P.A.P</label>
                        <span class="form-control uppercase">{{ $job->condicion_pap }}</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Plaza</label>
                        <span class="form-control uppercase">{{ $job->plaza ? $job->plaza : "No Tiene" }}</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Meta</label>
                        <span class="form-control uppercase">{{ $job->meta ? $job->meta->meta : ''}}</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Perfil</label>
                        <span class="form-control uppercase">{{ $job->perfil }}</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Ext Pptto</label>
                        <span class="form-control uppercase">{{ $job->ext_pptto}}</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Escuela</label>
                        <span class="form-control uppercase">{{ $job->escuela_id }}</span>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label for="" class="form-control-label">Observaciones</label>
                        <span class="form-control uppercase">{{ $job->observaciones }}</span>
                    </div>
                </div>

                @if ($job->ruc)
                    <h4 class="col-md-12 mt-4">Solo C.A.S</h4>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">R.U.C</label>
                            <span class="form-control uppercase">{{ $job->ruc }}</span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Fuente de Ingreso</label>
                            <span class="form-control uppercase">{{ $job->fuente_id }}</span>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>        

</div>
@endsection