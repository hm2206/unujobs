@extends('layouts.app')

@section('titulo')
    - Trabajadores
@endsection

@section('link')
    Planilla
@endsection

@section('content')

<div class="row">
    <div class="col-md-12">
        <a href="{{ route('home') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
        <a href="{{ route('job.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> nuevo</a>
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
    
    <div class="col-md-6 mt-3">

        <form method="POST" class="card" action="{{ route('import.work') }}" enctype="multipart/form-data">
            @csrf
            <div class="card-header">
                Importar trabajadores
                <a href="{{ url('/formatos/work_import.xlsx') }}" class="btn btn-sm btn-outline-success">
                    <i class="fas fa-file-excel"></i> Formato
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label for="import" class="btn btn-sm btn-block btn-outline-primary">
                            <input type="file" name="import" id="import" hidden>
                            <i class="fas fa-upload"></i> Subir Archivo de Excel
                        </label>
                        <small class="text-danger">{{ $errors->first('import') }}</small>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-success btn-sm">Importar <i class="fas fa-file-excel"></i></button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="col-md-6">
        <form method="POST" class="card mt-3" action="{{ route('export.work') }}">
            @csrf
            <div class="card-header">
                Exportación de trabajadores
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div class="form-group">
                            <small>Limite de trabajadores <span class="text-danger">{{ $jobs->count() }}</span></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="number" name="limite" class="form-control" value="{{ $jobs->count() }}" max="{{ $jobs->count() }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <input type="checkbox" name="order" title="Ordenar Descendentemente"> <i class="fas fa-sort-alpha-up"></i>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-success btn-sm">Exportar <i class="fas fa-file-excel"></i></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <div class="col-md-12 mt-3">
    
        {!! $jobs->appends(['query_search' => request('query_search')])->links() !!}
    
        <div class="card">
            <div class="card-header card-header-danger">
                <h4 class="card-title">Lista Rápida de Trabajadores</h4>
                <p class="card-category">Sub Módulo de Trabajadores</p>
            </div>
            <div class="card-body">

                <form method="GET card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <input type="text" placeholder="Buscar..." name="query_search" value="{{ request('query_search') }}" class="form-control" autofocus>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-info">Buscar <i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table">
                        <thead class="text-primary">
                            <tr>
                                <th>Nombre Completo</th>
                                <th>Número de documento</th>
                                <th>Profesión</th>
                                <th>teléfono</th>
                                <th>Número de cuenta</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($jobs as $job)
                                <tr class="uppercase capitalize">
                                    <th>{{ $job->nombre_completo }}</th>
                                    <th>{{ $job->numero_de_documento }}</th>
                                    <th>{{ $job->profesion }}</th>
                                    <th>{{ $job->phone }}</th>
                                    <th>{{ $job->numero_de_cuenta }}</th>
                                    <th>
                                        <div class="btn-group">
                                            <a href="{{ route('job.edit', $job->slug()) }}" class="btn btn-circle btn-xs btn-warning">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <a href="{{ route('job.show', $job->slug()) }}" class="btn btn-xs btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('job.boleta', $job->slug()) }}" target="blank" title="Boleta" class="btn btn-circle btn-xs btn-dark">
                                                <i class="fas fa-file"></i>
                                            </a>
                                        </div>
                                    </th>
                                </tr>
                            @empty
                                <tr class="text-center">
                                    <td colspan="7">No hay registros disponibles</td>
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