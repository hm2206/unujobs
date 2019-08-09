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

    @if ($errors->first('import'))
    <div class="col-md-12 mt-3 ">
        <div class="alert alert-danger">
            {{ $errors->first('import') }}       
        </div>
    </div>
@endif

    
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" placeholder="Buscar..." name="query_search" value="{{ request('query_search') }}" class="form-control" autofocus>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-info">Buscar <i class="fas fa-search"></i></button>
                        </div>

                        <div class="col-md-4">
                            <div class="row justify-content-around">
                                <validacion 
                                    btn_text="Importar"
                                    method="post"
                                    token="{{ csrf_token() }}"
                                    url="{{ route('import.work') }}"
                                >
    
                                    <div class="form-group">
                                        <a href="{{ url('/formatos/work_import.xlsx') }}" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-file-excel"></i> Formato de importación
                                        </a>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="import" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-upload"></i> Subir Archivo de Excel
                                            <input type="file" name="import" id="import" hidden>
                                        </label>
                                        <small class="text-danger">{{ $errors->first('import') }}</small>
                                    </div>
    
                                </validacion>
    
                                <validacion 
                                    btn_text="Exportar"
                                    method="post"
                                    token="{{ csrf_token() }}"
                                    url="{{ route('export.work') }}"
                                >
    
                                
                                    <div class="form-group">
                                        <small>Limite de trabajadores: <span class="text-danger">{{ $jobs->count() }}</span></small>
                                    </div>
    
                                    <div class="form-group">
                                        <input type="number" name="limite" class="form-control" value="{{ $jobs->count() }}" max="{{ $jobs->count() }}">
                                    </div>
                                    
                                    <div class="form-group">
                                        <input type="checkbox" name="order" title="Ordenar Descendentemente"> 
                                        <i class="fas fa-sort-alpha-up"></i> Ordenar Descendentemente
                                    </div>
    
                                    <hr>
    
                                </validacion>
                            </div>
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
                                <tr>
                                    <th class="capitalize">{{ $job->nombre_completo }}</th>
                                    <th>{{ $job->numero_de_documento }}</th>
                                    <th class="uppercase">{{ $job->profesion }}</th>
                                    <th>{{ $job->phone }}</th>
                                    <th>{{ $job->numero_de_cuenta }}</th>
                                    <th>
                                        <div class="row justify-content-around">
                                            <a href="{{ route('job.edit', $job->slug()) }}" class="btn btn-circle btn-sm btn-warning">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <a href="{{ route('job.show', $job->slug()) }}" class="btn btn-circle btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <btn-boleta theme="btn-dark btn-sm btn-circle"
                                                param="{{ $job->id }}"
                                                url="{{ route('job.boleta.store', $job->id) }}"
                                                nombre_completo="{{ $job->nombre_completo }}"
                                                token="{{ csrf_token() }}"
                                            >
                                                <i class="fas fa-file-alt"></i>
                                            </btn-boleta>

                                            <btn-liquidar theme="btn-danger btn-sm btn-circle"
                                                nombre_completo="{{ $job->nombre_completo }}"
                                                fecha_de_inicio="{{ $job->fecha_de_ingreso }}"
                                                redirect="{{ route('job.index') }}"
                                                nombre_completo="{{ $job->nombre_completo }}"
                                                id="{{ $job->id }}"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </btn-liquidar>

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