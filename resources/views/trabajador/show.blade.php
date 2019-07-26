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

            </div>
        </div>
    </div> 
    
    <div class="card mt-3">
        <div class="card-header">
            Cargos Asociados
        </div>
        <div class="card-body">
            <div class="row">
                @foreach ($job->infos as $info)
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                {{ $info->cargo ? $info->cargo->descripcion : '' }}
                            </div>
                            <div class="card-body">
                                <ul>
                                    <li>
                                        <b>Categoria: <button class="btn btn-sm btn-danger">{{ $info->categoria ? $info->categoria->nombre : '' }}</button></b>
                                    </li>
                                    <li class="mt-1">
                                        <b>Meta: <button class="btn btn-sm btn-danger">{{ $info->meta ? $info->meta->meta : '' }}</button></b>
                                    </li>
                                    <li class="mt-1">
                                        <b>Perfil: <button class="btn btn-sm btn-danger">{{ $info->perfil }}</button></b>
                                    </li>
                                    @if ($info->plaza)
                                        <li class="mt-1">
                                            <b>Plaza: <button class="btn btn-sm btn-danger">{{ $info->plaza }}</button></b>
                                        </li>
                                    @endif

                                    @if ($info->escuela)
                                        <li class="mt-1">
                                            <b>Escuela: <button class="btn btn-sm btn-danger">{{ $info->escuela }}</button></b>
                                        </li>
                                    @endif

                                    @if ($info->fuente_id)
                                        <li class="mt-1">
                                            <b>Fuente de Ingreso: <button class="btn btn-sm btn-danger">{{ $info->functe_id }}</button></b>
                                        </li>
                                    @endif

                                    @if ($info->ruc)
                                        <li class="mt-1">
                                            <b>RUC: <button class="btn btn-sm btn-danger">{{ $info->ruc }}</button></b>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            Sindicatos Asociados
        </div>
        <div class="card-body">
            <div class="row">
                @foreach ($job->sindicatos as $sindicato)
                    <div class="col-md-3">
                        <div class="btn btn-outline-dark">
                            {{ $sindicato->nombre }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
@endsection