@extends('layouts.app')

@section('titulo')
    - Obligacion
@endsection


@section('link')
    Recursos Humanos
@endsection

@section('content')

<div class="col-md-12 mb-3">
    <a href="{{ route('job.show', $job->slug()) }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
    <a href="{{ route('job.remuneracion', $job->slug()) }}" class="btn btn-dark">Remuneración</a>
    <a href="{{ route('job.descuento', $job->slug()) }}" class="btn btn-dark">Descuento</a>
</div>


@if (session('danger'))
    <div class="alert alert-danger">
        <b>{{ session('danger') }}</b>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success">
        <b>{{ session('success') }}</b>
    </div>
@endif



<div class="col-md-12">

    <div class="card">
        <h4 class="card-header">
            Obligaciones judiciales
            <span class="text-danger"> >> </span> 
            <span class="capitalize">{{ $job->nombre_completo }}</span>
        </h4>

        <form class="card-body" method="POST" action="{{ route('obligacion.store') }}">
            @csrf
            <table class="table">
                <thead>
                    <tr>
                        <th>Beneficiario</th>
                        <th>N° de Documento</th>
                        <th>Cuenta Banco de la Nación</th>
                        <th>Monto</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>
                            <input type="text" class="form-control" name="beneficiario" value="{{ old('beneficiario') }}">
                            <small class="text-danger">{{ $errors->first('beneficiario') }}</small>
                        </th>
                        <th>
                            <input type="text" class="form-control" name="numero_de_documento" value="{{ old('numero_de_documento') }}">
                            <small class="text-danger">{{ $errors->first('numero_de_documento') }}</small>
                        </th>
                        <th>
                            <input type="text" class="form-control" name="numero_de_cuenta" value="{{ old('numero_de_cuenta') }}">
                            <small class="text-danger">{{ $errors->first('numero_de_cuenta') }}</small>
                        </th>
                        <th>
                            <input type="number" value="{{ old('monto', 0) }}" class="form-control" name="monto">
                            <small class="text-danger">{{ $errors->first('monto') }}</small>
                        </th>
                    </tr>
                </tbody>
            </table>

            <div class="col-md-12 text-right">

                <input type="hidden" name="work_id" value="{{ $job->id }}">

                <button class="btn btn-success">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </form>
    </div>

    @if (session('update'))
        <div class="alert alert-primary mt-2">
            <b>{{ session('update') }}</b>
        </div>
    @endif

    @if($job->obligaciones->count() > 0)
        <div class="card mt-2">
            <h5 class="card-header">
                Lista de Obligaciones judiciales
            </h5>

            <div class="card-body">
                @foreach ($job->obligaciones as $obligacion) 
                    <form action="{{ route('obligacion.update', $obligacion->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Beneficiario</th>
                                    <th>N° de Documento</th>
                                    <th>Cuenta Banco de la Nación</th>
                                    <th>Monto</th>
                                    <th>Actualizar</th>
                                    <th>Eliminar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @csrf
                                    <tr>
                                        <th>
                                            <input type="text" class="form-control" name="up.beneficiario" value="{{ $obligacion->beneficiario }}">
                                        </th>
                                        <th>
                                            <input type="text" class="form-control" name="up.numero_de_documento" value="{{ $obligacion->numero_de_documento }}">
                                        </th>
                                        <th>
                                            <input type="text" class="form-control" name="up.numero_de_cuenta" value="{{ $obligacion->numero_de_cuenta }}">
                                        </th>
                                        <th>
                                            <input type="number" class="form-control" name="up.monto" value="{{ $obligacion->monto }}">
                                        </th>
                                        <th>
                                            <div class="col-md-12 text-right">

                                                <input type="hidden" name="up.work_id" value="{{ $job->id }}">

                                                <button class="btn btn-primary btn-sm">
                                                    <i class="fas fa-sync"></i>
                                                </button>
                                            </div>
                                        </th>
                                        <th>
                                            <btn-delete tipo="danger" size="sm" url="{{ route('obligacion.destroy', $obligacion->id) }}"></btn-delete>
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                        </form>
                    @endforeach
                </div>
            </div>
        @endif

</div>
    

@endsection