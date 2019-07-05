@extends('layouts.app')

@section('titulo')
    - Cronogramas
@endsection

@section('link')
    Planilla
@endsection

@section('content')


<div class="col-md-12">
    <a href="{{ route('cronograma.index') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
    @if ($cronograma->adicional)
        <a href="{{ route('cronograma.add', $cronograma->id) }}" class="btn btn-primary"><i class="fas fa-plus"></i> Agregar Trabajador</a>
    @endif
</div>

<h3 class="text-center uppercase">
    @if ($cronograma->adicional)
        Adicional 
        <span class="btn btn-sm btn-primary">{{ $cronograma->numero }}</span>
        <span class="text-danger"> >> </span>
    @endif
    {{ $cronograma->planilla ? $cronograma->planilla->descripcion : null }}
</h3>

<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <i class="fas fa-filter"></i> Filtrar
        </div>
        <form class="card-body" method="GET">
            <div class="row align-items-center">

                <div class="col-md-4">
                    <input type="text" class="form-control" name="query_search" value="{{ $like }}">
                </div>

                <div class="col-md-3">
                    <button class="btn btn-info">
                        <i class="fas fa-search"></i> Buscar
                    </button>
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

    @if ($jobs)
        {{ $jobs->links() }}
    @endif

    <div class="card">
        <div class="card-header card-header-danger">
            <h4 class="card-title">Lista de Trabajadores </h4>
            <p class="card-category">Que pertenecen a esta planilla</p>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead class="text-primary">
                        <tr>
                            <th>#ID</th>
                            <th>Nombre Completo</th>
                            <th>Meta</th>
                            <th>Cargo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jobs as $job)
                            <tr>
                                <th>{{ $job->id }}</th>
                                <th class="uppercase">{{ $job->nombre_completo }}</th>
                                <th class="uppercase">{{ $job->meta ? $job->meta->metaID : null }}</th>
                                <th class="uppercase">{{ $job->cargo ? $job->cargo->descripcion : null }}</th>
                                <th>
                                    <div class="btn-group">
                                        <a href="{{ route('job.show', $job->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a target="blank" 
                                            title="remuneracion"
                                            href="{{ route('job.remuneracion', [$job->id, "mes={$cronograma->mes}&year={$cronograma->año}&adicional={$cronograma->adicional}"]) }}" 
                                            class="btn btn-sm btn-warning"
                                        >
                                            <i class="fas fa-coins"></i>
                                        </a>
                                        <a target="blank" 
                                            title="descuento"
                                            href="{{ route('job.descuento', [$job->id, "mes={$cronograma->mes}&year={$cronograma->año}&adicional={$cronograma->adicional}"]) }}" 
                                            class="btn btn-sm btn-danger"
                                        >
                                            <i class="fab fa-creative-commons-nc"></i>
                                        </a>
                                    </div>
                                </th>
                            </tr>
                        @empty
                            <tr>
                                <th colspan="5" class="text-center">No hay registros disponibles</th>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>        

</div>
@endsection