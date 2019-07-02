@extends('layouts.app')

@section('titulo')
    - Obligacion
@endsection


@section('link')
    Recursos Humanos
@endsection

@section('content')

<div class="col-md-12">
    <a href="{{ route('job.show', $job->id) }}" class="btn btn-warning"><i class="fas fa-arrow-left"></i> atrás</a>
    @if ($cronograma)
        <a href="{{ route('cronograma.job', $cronograma->id) }}" class="btn btn-primary"><i class="fas fa-calendar-week"></i> planilla</a>
    @endif
    <a href="{{ route('job.remuneracion', $job->id) . "?mes={$mes}&year={$year}" }}" class="btn">remuneración</a>
    <a href="{{ route('job.descuento', $job->id) . "?mes={$mes}&year={$year}" }}" class="btn">descuento</a>
</div>


@if (session('danger'))
    <div class="alert alert-danger">
        <b>{{ session('danger') }}</b>
    </div>
@endif


<div class="col-md-12">
    <div class="card">
        <h5 class="card-header">
            <i class="fas fa-filter"></i> Filtro:
        </h5>
        <hr>
        <form class="card-body" method="GET">
            <div class="row">

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="" class="form-control-label">Mes</label>
                        <input type="number" name="mes" class="form-control" min="1" max="12" value="{{ $mes }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="" class="form-control-label">Año</label>
                        <input type="number" name="year" class="form-control" min="{{ date('Y') - 2 }}" max="{{ date('Y') }}" value="{{ $year }}">
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label for="" class="form-control-label">Adicional</label> <br>
                        <input type="checkbox" name="adicional" {!! request()->input('adicional') ? 'checked' : null !!}>
                    </div>
                </div>

                @if (request()->input('adicional'))
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="" class="form-control-label">Numero</label> <br>
                            <select name="numero" class="form-control">
                                @foreach ($seleccionar as $select)
                                    <option value="{{ $select->numero }}" {!! $select->numero == $numero ? 'selected': null !!}>
                                        {{ $select->numero }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif

                <div class="col-md-3">
                    <div class="form-group">
                        <button class="btn btn-info">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>


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
            <hr>
            <h4 class="text-left">
                Total DSCTOS:  <b class="text-primary">S./ {{ $total }}</b>
            </h4>
        </div>
    </div>

</div>
    

@endsection