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
    @if ($cronograma)
        <a href="{{ route('cronograma.job', $cronograma->slug()) }}" class="btn btn-primary"><i class="fas fa-calendar-week"></i> planilla</a>
    @endif
    <a href="{{ route('job.remuneracion', $job->slug()) . "?mes={$mes}&year={$year}" }}" class="btn btn-dark">Remuneración</a>
    <a href="{{ route('job.descuento', $job->slug()) . "?mes={$mes}&year={$year}" }}" class="btn btn-dark">Descuento</a>
</div>


@if (session('danger'))
    <div class="alert alert-danger">
        <b>{{ session('danger') }}</b>
    </div>
@endif


<div class="col-md-12">

    <div class="card">
        <h4 class="card-header">
            Obligaciones judiciales
            <span class="text-danger"> >> </span> 
            <span class="uppercase">{{ $job->nombre_completo }}</span>
        </h4>

        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Beneficiario</th>
                        <th>N° de Identidad</th>
                        <th>Cuenta Banco de la Nación</th>
                        <th>Monto</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>
                            <input type="text" class="form-control">
                        </th>
                        <th>
                            <input type="text" class="form-control">
                        </th>
                        <th>
                            <input type="text" class="form-control">
                        </th>
                        <th>
                            <input type="number" value="0" class="form-control">
                        </th>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="card-footer">
            
        </div>
    </div>

</div>
    

@endsection