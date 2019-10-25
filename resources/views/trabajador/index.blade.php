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
        
        <div class="row">
            <div class="col-md-10">
                <a href="{{ route('home') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
                <a href="{{ route('job.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> nuevo</a>
                
                <validacion 
                    btn_text="Imp. Trabajadores"
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
                    btn_text="Imp. Configuración"
                    method="post"
                    token="{{ csrf_token() }}"
                    url="{{ route('import.work.config') }}"
                >
        
                    <div class="form-group">
                        <a href="{{ url('/formatos/work_config_import.xlsx') }}" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-file-excel"></i> Formato de importación
                        </a>
                    </div>
                                
                    <div class="form-group">
                        <label for="import_config" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-upload"></i> Subir Archivo de Excel
                            <input type="file" name="import" id="import_config" hidden>
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
                        <small>Limite de trabajadores: <span class="text-danger">{{ $count }}</span></small>
                    </div>
        
                    <div class="form-group">
                        <input type="number" name="limite" class="form-control" value="{{ $count }}" max="{{ $count }}">
                    </div>
                                
                    <div class="form-group">
                        <input type="checkbox" name="order" title="Ordenar Descendentemente"> 
                        <i class="fas fa-sort-alpha-up"></i> Ordenar Descendentemente
                    </div>
        
                    <hr>
        
                </validacion>
            </div>

            <div class="col-md-2 text-right">
                <h1>{{ $count }}</h1>
            </div>
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

    @if ($errors->first('import'))
    <div class="col-md-12 mt-3 ">
        <div class="alert alert-danger">
            {{ $errors->first('import') }}       
        </div>
    </div>
@endif

    
    <div class="col-md-12 mt-3">
    
        {!! 
            $jobs->appends([
                'query_search' => request('query_search'),
                "planilla_id" => $planilla_id,
                "cargo_id" => $cargo_id,
                "categoria_id" => $categoria_id,
                "meta_id" => $meta_id,
                "estado" => $estado
            ])->links() 
        !!}
    
        <div class="card">
            <div class="card-header card-header-danger">
                <h4 class="card-title">Lista Rápida de Trabajadores</h4>
                <p class="card-category">Sub Módulo de Trabajadores</p>
            </div>
            <div class="card-body">

                <form method="GET card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <input type="text" placeholder="Buscar..." name="query_search" value="{{ request('query_search') }}" class="form-control" autofocus>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <select-filtro
                                planilla_id="{{ $planilla_id }}"
                                cargo_id="{{ $cargo_id }}"
                                categoria_id="{{ $categoria_id }}"
                                meta_id="{{ $meta_id }}"
                                estado_id="{{ $estado }}"
                            >
                            </select-filtro>
                        </div>

                        <div class="col-md-1">
                            <button class="btn btn-info">Buscar</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table">
                        <thead class="text-primary">
                            <tr>
                                <th>Nombre Completo</th>
                                <th>N° Documento</th>
                                <th>Cargo</th>
                                <th>Categoria</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($jobs as $job)
                                <tr>
                                    <th class="capitalize" width="30%">{{ $job->work->nombre_completo }}</th>
                                    <th>{{ $job->work->numero_de_documento }}</th>
                                    <th class="uppercase">
                                        <span class="btn btn-sm btn-dark">
                                            {{ $job->cargo ? $job->cargo->descripcion : '' }}
                                        </span>
                                    </th>
                                    <th class="uppercase">
                                        <span class="btn btn-sm btn-dark">
                                            {{ $job->categoria ? $job->categoria->nombre : '' }}
                                        </span>
                                    </th>
                                    <th>
                                        @if ($job->active)
                                            <button class="btn-sm btn btn-success">
                                                Activo
                                            </button>
                                        @else
                                            <button class="btn-sm btn btn-danger">
                                                Inactivo
                                            </button>
                                        @endif
                                    </th>
                                    <th>
                                        <div class="row justify-content-around">
                                            <btn-work-config theme="btn-warning btn-sm btn-circle"
                                                param="{{ $job->id }}"
                                                nombre_completo="{{ $job->nombre_completo }}"
                                            >
                                                <i class="fas fa-cog"></i>
                                            </btn-work-config>
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

                                            <active-info 
                                                :active="{{ $job->active }}"
                                                param="{{ $job->id }}"
                                            />

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