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
    <a href="{{ route('cronograma.job', $cronograma->slug()) }}" class="btn btn-primary"><i class="fas fa-list"></i> Lista</a>
</div>

<h3 class="text-center uppercase">
    @if ($cronograma->adicional)
        Adicional 
        <span class="btn btn-sm btn-primary">{{ $cronograma->numero }}</span>
        <span class="text-danger"> >> </span>
    @endif
    {{ $cronograma->planilla ? $cronograma->planilla->descripcion : null }}
</h3>

<div class="col-md-12 mb-2">
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

@if ($errors->first('jobs'))
    <div class="col-md-12 mt-3 ">
        <div class="alert alert-warning">
            {{ $errors->first('jobs') }}       
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
            <p class="card-category">Que no pertenecen a esta planilla</p>
        </div>
        <form class="card-body" method="POST" action="{{ route('cronograma.add.store', $cronograma->id) }}">
            @csrf
            <div class="table-responsive">
                <table class="table">
                    <thead class="text-primary">
                        <tr>
                            <th>Seleccionar</th>
                            <th>#ID</th>
                            <th>Nombre Completo</th>
                            <th>Categorias</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jobs as $job)
                            <tr>
                                <th>
                                    <input type="checkbox" name="jobs[]" value="{{ $job->id }}">
                                </th>
                                <th>{{ $job->id }}</th>
                                <th class="uppercase">{{ $job->nombre_completo }}</th>
                                <th class="uppercase">
                                    <div class="btn-group">
                                        @foreach ($job->infos as $info)
                                            <div class="btn btn-sm btn-danger">
                                                {{ $info->categoria ? $info->categoria->nombre : '' }}
                                            </div>
                                        @endforeach
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
            <div class="card-footer">
                <div class="text-center">
                    <button class="btn btn-warning">Guardar</button>
                </div>
            </div>
        </form>
    </div>        

</div>
@endsection