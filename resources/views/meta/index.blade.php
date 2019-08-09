@extends('layouts.app')

@section('titulo')
    - Metas presupuestales
@endsection

@section('link')
    Planilla
@endsection

@section('content')

<div class="col-md-12">
    <h3>Metas presupuestales</h3>
</div>


<div class="col-md-12 mb-2">
    <a href="{{ route('home') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
    <a href="{{ route('meta.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> nuevo</a>
</div>

@if (session('success'))
    <div class="col-md-12 mt-3">
        <div class="alert alert-success">
            {{ session('success') }}       
        </div>
    </div>
@endif

@if (session('danger'))
    <div class="col-md-12 mt-3">
        <div class="alert alert-danger">
            {{ session('danger') }}       
        </div>
    </div>
@endif

@if ($errors->first('import'))
    <div class="col-md-12 mt-3">
        <div class="alert alert-danger">
            {{ $errors->first('import')}}       
        </div>
    </div>
@endif

<div class="row mb-3">
    <div class="col-md-12 mt-3">
    
        <form method="GET" class="card">
            <div class="card-header">
                Buscar Metas presupuestales
            </div>
            <div class="card-body">
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
            </div>
        </form>
    </div>
</div>


<div class="col-md-12 mb-2 mt-2">

    {!! $metas->appends(['query_search' => request('query_search')])->links() !!}

    <form class="card">
        @forelse ($metas as $meta)

        <div class="card-header">
            <div class="row">
                <a href="{{ route('meta.edit', $meta->slug()) }}" class="btn btn-warning"><i class="fas fa-pencil-alt"></i> Editar</a>

                <validacion 
                    btn_text="Importar"
                    method="post"
                    theme="btn-success ml-1"
                    token="{{ csrf_token() }}"
                    url="{{ route('import.meta') }}"
                >

                    <div class="form-group">
                        <a href="{{ url('/formatos/meta_import.xlsx') }}" class="btn btn-sm btn-outline-success">
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
                    theme="btn-success ml-1"
                    token="{{ csrf_token() }}"
                    url="{{ route('export.meta') }}"
                >
                                
                    <div class="form-group">
                        <small>Limite de trabajadores: <span class="text-danger">{{ $metas->count() }}</span></small>
                    </div>
    
                    <div class="form-group">
                        <input type="number" name="limite" class="form-control" value="{{ $metas->count() }}" max="{{ $metas->count() }}">
                    </div>
                                    
                    <div class="form-group">
                        <input type="checkbox" name="order" title="Ordenar Descendentemente"> 
                        <i class="fas fa-sort-alpha-up"></i> Ordenar Descendentemente
                    </div>
    
                    <hr>

                </validacion>

                
            </div>

        </div>

        <div class="card-body">
                <div class="row">
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Código de Meta</label>
                            <span class="form-control uppercase">{{ $meta->metaID }}</span>
                        </div>
                    </div>
    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Meta <span class="text-danger">*</span></label>
                            <span class="form-control uppercase">{{ $meta->meta }}</span>
                        </div>
                    </div>
    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Código de Sector <span class="text-danger">*</span></label>
                            <span class="form-control uppercase">{{ $meta->sectorID }}</span>
                        </div>
                    </div>
    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Sector <span class="text-danger">*</span></label>
                            <span class="form-control uppercase">{{ $meta->sector }}</span>
                        </div>
                    </div>
    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Código de Pliego <span class="text-danger">*</span></label>
                            <span class="form-control uppercase">{{ $meta->pliegoID }}</span>
                        </div>
                    </div>
    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Pliego <span class="text-danger">*</span></label>
                            <span class="form-control uppercase">{{ $meta->pliego }}</span>
                        </div>
                    </div>
    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Código Unidad Ejecutora <span class="text-danger">*</span></label>
                            <span class="form-control uppercase">{{ $meta->unidadID }}</span>
                        </div>
                    </div>
    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Unidad Ejecutora <span class="text-danger">*</span></label>
                            <span class="form-control uppercase">{{ $meta->unidad_ejecutora }}</span>
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Código Programa <span class="text-danger">*</span></label>
                            <span class="form-control uppercase">{{ $meta->programaID }}</span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Código Unidad Ejecutora <span class="text-danger">*</span></label>
                            <span class="form-control uppercase">{{ $meta->programa }}</span>
                        </div>
                    </div>
    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Código Función <span class="text-danger">*</span></label>
                            <span class="form-control uppercase">{{ $meta->funcionID }}</span>
                        </div>
                    </div>
    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Función <span class="text-danger">*</span></label>
                            <span class="form-control uppercase">{{ $meta->funcion }}</span>
                        </div>
                    </div>
    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Código Sub Programa <span class="text-danger">*</span></label>
                            <span class="form-control uppercase">{{ $meta->subProgramaID }}</span>
                        </div>
                    </div>
    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Sub Programa <span class="text-danger">*</span></label>
                            <span class="form-control uppercase">{{ $meta->sub_programa }}</span>
                        </div>
                    </div>
    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Código Actividad <span class="text-danger">*</span></label>
                            <span class="form-control uppercase">{{ $meta->actividadID }}</span>
                        </div>
                    </div>
    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Actividad <span class="text-danger">*</span></label>
                            <span class="form-control uppercase">{{ $meta->actividad }}</span>
                        </div>
                    </div>
        
                </div>        
            </div>
        @empty
            <div class="card-body">No hay metas disponibles</div>
        @endforelse
    </form>
</div>

@endsection