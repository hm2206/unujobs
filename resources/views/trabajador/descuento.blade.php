@extends('layouts.app')

@section('titulo')
    - Registro de Requerimiento de Personal
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
            Descuento
            <span class="text-danger"> >> </span> 
            <span class="uppercase">{{ $job->nombre_completo }}</span>
        </h4>
        <hr>
        <form class="card-body" action="{{ route('job.descuento.update', $job->id) }}" method="POST">

            <div class="row mt-4">
                @csrf
                @method('PUT')

                @forelse ($descuentos as $descuento)
                    <div class="col-md-4">
                        <div class="row align-items-center">
                            <h6 class="col mb-0 text-primary uppercase text-left">
                                @if ($descuento->typeDescuento)
                                    {{ $descuento->typeDescuento->key }}. <span class="ml-1"></span>
                                    {{ $descuento->typeDescuento->descripcion }}
                                @endif
                            </h6>
                            S./ 
                            <div class="col-md-4">
                                <input type="number" name="{{ $descuento->id }}" class="form-control" value="{{ $descuento->monto }}">
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-md-12">
                        <div class="text-center text-danger">
                            No hay registros disponibles
                        </div>
                    </div>
                @endforelse

                <div class="text-center mt-5 col-md-12">
                    @if (isset($cronograma->id))
                        <input type="hidden" name="cronograma_id" value="{{ $cronograma->id }}">
                    @endif
                    <button class="btn btn-warning">Actualizar</button>
                </div>

            </div>


        </form>

        <hr>

        <div class="card-footer">
            <hr>
            <h4 class="text-left">
                Total DSCTOS:  <b class="text-primary">S./ {{ $total }}</b>
            </h4>
        </div>
    </div>

</div>
    

@endsection