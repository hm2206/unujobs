@extends('layouts.app')

@section('titulo')
    - Cronogramas
@endsection

@section('link')
    Planilla
@endsection

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Crear Planilla x Mes</h1>
    @if ($cronogramas->count() > 0)
        <div class="d-none d-sm-inline-block">
            <a href="{{ route('export.reporte', [$mes, $year, $adicional]) }}" class="btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-download fa-sm text-white-50"></i> Generar Reporte
            </a>
        </div>
    @endif
</div>

<div class="row">
    <div class="col-md-12 mb-2">
        <a href="{{ route('home') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
        <a href="{{ route('cronograma.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> nuevo</a>
    </div>


    <div class="col-md-12 mb-2">
        <div class="card">
            <h4 class="card-header">
                Cronograma
            </h4>

            <form class="card-body" action="" method="GET">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <input type="number" placeholder="mes" name="mes" class="form-control" min="1" max="12" value="{{ $mes }}">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <input type="number" placeholder="año" name="year" max="{{ date('Y') }}" min="{{ date('Y') - 2 }}" class="form-control" value="{{ $year }}">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Adicional</label>
                            <input type="checkbox" name="adicional" {!! $adicional ? 'checked' : null !!}>
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
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="col-md-12 mt-3 ">
            <div class="alert alert-success">
                {{ session('success') }}       
            </div>
        </div>
    @endif

    <div class="col-md-12">

        {{ $cronogramas->links() }}

        <div class="card">
            <div class="card-header card-header-danger">
                <h4 class="card-title">Lista de Cronogramas</h4>
                <p class="card-category">Sub Módulo de Gestión de cronogramas</p>
            </div>
            <div class="card-body">

                @if ($cronogramas->count() > 0)
                    <div class="mb-2 pl-3 pr-3 row">
                        <a target="__blank" href="{{ url($report_planilla_metas) }}" class="btn btn-sm btn-danger mr-1">
                            <i class="fas fa-file-pdf"></i> Reporte Planillas Metas 
                        </a>
                        <a target="__blank" href="{{ url($report_boletas) }}" class="btn btn-sm btn-danger mr-1">
                            <i class="fas fa-file-pdf"></i> Reporte Boletas del Mes
                        </a>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table">
                        <thead class="text-primary">
                            <tr>
                                <th>#ID</th>
                                <th>Planilla</th>
                                <th>Observación</th>
                                <th>Sede</th>
                                <th class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($cronogramas as $cronograma)
                                <tr>
                                    <th>{{ $cronograma->planilla_id }}</th>
                                        @if ($cronograma->adicional)
                                            <th class="uppercase">
                                                {!! $cronograma->planilla 
                                                    ? "Adicional " . "<span class='btn btn-sm btn-primary'>" 
                                                        . $cronograma->numero . "</span>" .  "<span class='text-danger'> >> </span>"  
                                                        . $cronograma->planilla->descripcion 
                                                    : null 
                                                !!}
                                            </th>
                                        @else
                                            <th class="uppercase">{{ $cronograma->planilla ? $cronograma->planilla->descripcion : null }}</th>
                                        @endif
                                        <th class="uppercase">{{ $cronograma->observacion }}</th>
                                    <th class="uppercase">{{ $cronograma->sede ? $cronograma->sede->descripcion : null }}</th>
                                    <th class="text-right">
                                        <form class="btn-group text-right" method="POST" action="{{ route('export.cronograma', $cronograma->id) }}">
                                            
                                            @csrf

                                            <a target="blank" href="{{ route('cronograma.job', $cronograma->slug()) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <a href="{{ route('cronograma.edit', $cronograma->slug()) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>

                                            @if ($cronograma->adicional)
                                                <a class="btn btn-sm btn-success" href="{{ route('cronograma.add', $cronograma->slug()) }}">
                                                    <i class="fas fa-plus"></i>
                                                </a>  
                                            @endif


                                            @if ($cronograma->pendiente)
                                                <button class="btn btn-sm btn-danger" disabled>
                                                    Generar PDF
                                                </button>
                                            @else
                                                <a title="PDF" href="{{ route('export.cronograma.pdf', $cronograma->slug()) }}" class="btn btn-sm btn-danger">
                                                    Generar PDF
                                                </a>
                                            @endif

                                            
                                            @if ($cronograma->pdf)
                                                <a target="__blank" title="PDF" href="{{ url($cronograma->pdf) }}" class="btn btn-sm btn-outline-danger">
                                                    <i class="far fa-file-pdf" aria-hidden="true"></i> Ver PDF
                                                </a>
                                            @endif

                                            <button class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-file-excel"></i> Exportar
                                            </button>

                                        </form>
                                    </th>
                                </tr>
                            @empty
                                <tr class="text-center">
                                    <td colspan="5">No hay registros</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>        

    </div>
</div>
@endsection