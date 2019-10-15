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
            <btn-personal
                theme="btn-primary btn-sm"
                mes="{{ $mes }}"
                year="{{ $year }}"
            >
                <i class="fas fa-download fa-sm text-white-50"></i> Reporte Personal
            </btn-personal>
           <!-- <btn-mef
                theme="btn-primary btn-sm"
                mes="{{ $mes }}"
                year="{{ $year }}"
            >
                <i class="fas fa-download fa-sm text-white-50"></i> Rpte. MEF
            </btn-mef>

            <btn-alta-baja
                theme="btn-primary btn-sm"
                mes="{{ $mes }}"
                year="{{ $year }}"
            >
                <i class="fas fa-file fa-sm text-white-50"></i>
                Rpte. de altas y bajas
            </btn-alta-baja>

            <btn-resumen-categoria
                theme="btn-primary btn-sm"
                mes="{{ $mes }}"
                year="{{ $year }}"
            >
                <i class="fas fa-file fa-sm text-white-50"></i>
                Resumen de rentas
            </btn-resumen-categoria>-->
        
        </div>
    @endif
</div>

<div class="row">
    <div class="col-md-12 mb-2">
        <a href="{{ route('home') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
        <btn-cronograma 
            theme="btn-primary"
            mes="{{ $mes }}"
            report="{{ $reporte }}"
            redirect="{{ route('cronograma.index', ['mes=' . $mes, 'year=' . $year])}}"
        >
            <i class="fas fa-plus"></i> Nuevo
        </btn-cronograma>
        <report-renta
            theme="btn-outline-dark"
            class="text-left"
        >
            Reporte de Renta
    </report-renta>
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

    @if (session('danger'))
        <div class="col-md-12 mt-3 ">
            <div class="alert alert-danger">
                {{ session('danger') }}       
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

                <div class="table-responsive">
                    <table class="table">
                        <thead class="text-primary">
                            <tr>
                                <th>#ID</th>
                                <th>Planilla</th>
                                <th>Observación</th>
                                <th>Sede</th>
                                <th>Estado</th>
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
                                    <th>
                                        @if ($cronograma->pendiente)
                                            <button class="btn btn-sm btn-warning">
                                                <i class="fas fa-clock"></i> Procesando...
                                            </button>
                                        @else
                                            @if ($cronograma->estado)
                                                <a class="btn btn-sm btn-primary" 
                                                    href="{{ route('cronograma.job', $cronograma->slug()) }}"
                                                >
                                                    <i class="fas fa-clock"></i> En curso
                                                </a>
                                            @else
                                                <a class="btn-sm btn btn-danger"
                                                    href="{{ route('cronograma.job', $cronograma->slug()) }}"
                                                >
                                                    <i class="fas fa-history"></i> Terminado
                                                </a>
                                            @endif
                                        @endif
                                    </th>
                                    <th class="">
                                        <div class="text-right">

                                            @if ($cronograma->pendiente)
                                                <button class="btn btn-sm btn-primary" disabled>
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            @else
                                                <a href="{{ route('cronograma.job', $cronograma->slug()) }}" 
                                                    class="btn btn-sm btn-primary"
                                                >
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endif

                                            @if ($cronograma->pendiente)
                                                <button class="btn btn-sm btn-warning" disabled>
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>
                                            @else
                                                <btn-cronograma
                                                    theme="btn-warning btn-sm"
                                                    class="text-left"
                                                    :datos="{{ $cronograma }}"
                                                    redirect="{{ route('cronograma.index', ['mes=' . $mes, 'year=' . $year])}}"
                                                >
                                                    <i class="fas fa-pencil-alt"></i>
                                                </btn-cronograma>
                                            @endif

                                                
                                            @if ($cronograma->adicional && !$cronograma->pendiente)
                                                <add-work
                                                    theme="btn-dark btn-sm"
                                                    class="text-left"
                                                    :cronograma="{{ $cronograma }}"
                                                >
                                                </add-work>
                                            @endif
                                                
                                            @if ($cronograma->pdf)
                                                <a target="__blank" title="PDF, resumen de todas las metas" href="{{ url($cronograma->pdf) }}" class="btn btn-sm btn-outline-danger">
                                                    <i class="far fa-file-pdf" aria-hidden="true"></i>
                                                </a>
                                            @endif

                                            @if ($cronograma->pdf_meta)
                                                <a target="__blank" title="PDF, resumen metas x metas" href="{{ url($cronograma->pdf_meta) }}" class="btn btn-sm btn-outline-danger">
                                                    <i class="far fa-file-pdf" aria-hidden="true"></i>
                                                </a>
                                            @endif

                                            @if ($cronograma->pendiente)
                                                <button class="btn btn-sm btn-success" disabled>
                                                    <i class="fas fa-file-excel"></i> Exportar
                                                </button>
                                            @else
                                                <validacion 
                                                    btn_text="Exportar" 
                                                    class="text-left"
                                                    url="{{ route('export.cronograma', $cronograma->id) }}"
                                                    method="post"
                                                    token="{{ csrf_token() }}"
                                                    id="exportar-cronograma"
                                                >
                                                </validacion>
                                            @endif

                                        </div>
                                        
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