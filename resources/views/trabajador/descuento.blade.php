@extends('layouts.app')

@section('titulo')
    - Descuentos
@endsection


@section('link')
    Recursos Humanos
@endsection

@section('content')

<div class="col-md-12 mb-2">
    <a href="{{ route('job.show', $job->id) }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
    @if ($cronograma)
        <a href="{{ route('cronograma.job', $cronograma->id) }}" class="btn btn-primary capitalize"><i class="fas fa-calendar-week"></i> planilla</a>
    @endif
    <a href="{{ route('job.remuneracion', $job->id) . "?mes={$mes}&year={$year}" }}" class="btn btn-dark capitalize">remuneración</a>
    <a href="{{ route('job.obligacion', $job->id) . "?mes={$mes}&year={$year}" }}" class="btn btn-dark capitalize">obligaciones judiciales</a>
</div>


@if (session('danger'))
    <div class="alert alert-danger">
        <b>{{ session('danger') }}</b>
    </div>
@endif


<div class="col-md-12 mb-2">
    <div class="card">
        <h5 class="card-header">
            <i class="fas fa-filter"></i> Filtro:
        </h5>
        
        <form class="card-body" method="GET">
            <div class="row">

                <div class="col-md-3">
                    <div class="form-group">
                        <input type="number" name="mes" class="form-control" min="1" max="12" value="{{ $mes }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <input type="number" name="year" class="form-control" min="{{ date('Y') - 2 }}" max="{{ date('Y') }}" value="{{ $year }}">
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label for="">Adicional</label>
                        <input type="checkbox" name="adicional" {!! request()->input('adicional') ? 'checked' : null !!}>
                    </div>
                </div>

                @if (request()->input('adicional'))
                    <div class="col-md-3">
                        <div class="form-group">
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

                <div class="col-md-12">
                    <div class="row">

                        <div class="col-md-3">
                            <select name="categoria_id" class="form-control">
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" {!! request()->categoria_id == $categoria->id ? 'selected' : '' !!}>
                                        {{ $categoria->categoria ? $categoria->categoria->nombre : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    Dias:
                                </div>
                                <div class="col-md-9">
                                    <input disabled type="text" class="form-control" value="{{ $dias }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <button class="btn btn-info">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
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
            <span class="uppercase">{{ $job->nombre_completo }} </span>
        </h4>
        <form class="card-body" action="{{ route('job.descuento.update', $job->id) }}" method="POST">
            <div class="row mt-4">
                @csrf
                @method('PUT')

                @forelse ($descuentos as $descuento)
                    <div class="col-md-4 mb-2">
                        <div class="row align-items-center">
                            <h6 class="col mb-0 text-primary uppercase text-left">
                                @if ($descuento->typeDescuento)
                                    <span class="text-danger mr-1">{{ $descuento->typeDescuento->key }}.</span>
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

                    <input type="hidden" name="cargo_id" value="{{ $current->cargo_id }}">
                    <input type="hidden" name="categoria_id" value="{{ $current->categoria_id }}">

                    <button class="btn btn-primary"><i class="fas fa-sync"></i> Actualizar</button>
                </div>

            </div>

        </form>

        @if (count($descuentos) > 0)
            <div class="card-footer">
                <h6 class="text-right">
                    <b>TOTAL DSCTOS:</b>  <b class="text-primary">S./ {{ $total }}</b>
                </h6>
                <h6 class="text-right">
                    <b>BASE IMPONIBLE:</b>  <b class="text-primary">S./ {{ $base }}</b>
                </h6>
                <h6 class="text-right">
                    <b>APORTE PATRONAL ESSALUD:</b>  <b class="text-primary">S./ {{ $aporte }}</b>
                </h6>
                <hr>
                <h6 class="text-right">
                    <b>TOTAL:</b>  <b class="text-primary">S./ {{ $total_neto }}</b>
                </h6>
            </div>
        @endif

    </div>

</div>
    

@endsection