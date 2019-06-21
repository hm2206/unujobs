@extends('layouts.app')

@section('titulo')
    - Metas presupuestales
@endsection

@section('link')
    Planilla
@endsection

@section('content')

<div class="col-md-12">
    <a href="{{ route('planilla') }}" class="btn btn-primary"><i class="fas fa-arrow-left"></i> atrás</a>
    <a href="{{ route('meta.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> nuevo</a>
</div>

@if (session('success'))
    <div class="col-md-12 mt-3 ">
        <div class="alert alert-success">
            {{ session('success') }}       
        </div>
    </div>
@endif

<div class="col-md-12">

    <h3>Metas presupuestales</h3>

    <form method="GET" class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Buscar</label>
                        <input type="text" name="query_search" value="{{ request('query_search') }}" class="form-control" autofocus>
                    </div>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-info">Buscar <i class="fas fa-search"></i></button>
                </div>
            </div>
        </div>
    </form>

    {!! $metas->appends(['query_search' => request('query_search')])->links() !!}

    <form class="card">
        @forelse ($metas as $meta)

        <h4 class="card-header">
            <a href="{{ route('meta.edit', $meta->metaID) }}" class="btn btn-warning"><i class="material-icons">create</i> Editar</a>
        </h4>

        <div class="card-body">
                <div class="row">
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Código de Meta</label>
                            <span class="form-control">{{ $meta->metaID }}</span>
                        </div>
                    </div>
    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Meta <span class="text-danger">*</span></label>
                            <span class="form-control">{{ $meta->meta }}</span>
                        </div>
                    </div>
    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Código de Sector <span class="text-danger">*</span></label>
                            <span class="form-control">{{ $meta->sectorID }}</span>
                        </div>
                    </div>
    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Sector <span class="text-danger">*</span></label>
                            <span class="form-control">{{ $meta->sector }}</span>
                        </div>
                    </div>
    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Código de Pliego <span class="text-danger">*</span></label>
                            <span class="form-control">{{ $meta->pliegoID }}</span>
                        </div>
                    </div>
    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Pliego <span class="text-danger">*</span></label>
                            <span class="form-control">{{ $meta->pliego }}</span>
                        </div>
                    </div>
    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Código Unidad Ejecutora <span class="text-danger">*</span></label>
                            <span class="form-control">{{ $meta->unidadID }}</span>
                        </div>
                    </div>
    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Unidad Ejecutora <span class="text-danger">*</span></label>
                            <span class="form-control">{{ $meta->unidad_ejecutora }}</span>
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Código Programa <span class="text-danger">*</span></label>
                            <span class="form-control">{{ $meta->programaID }}</span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Código Unidad Ejecutora <span class="text-danger">*</span></label>
                            <span class="form-control">{{ $meta->programa }}</span>
                        </div>
                    </div>
    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Código Función <span class="text-danger">*</span></label>
                            <span class="form-control">{{ $meta->funcionID }}</span>
                        </div>
                    </div>
    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Función <span class="text-danger">*</span></label>
                            <span class="form-control">{{ $meta->funcion }}</span>
                        </div>
                    </div>
    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Código Sub Programa <span class="text-danger">*</span></label>
                            <span class="form-control">{{ $meta->subProgramaID }}</span>
                        </div>
                    </div>
    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Sub Programa <span class="text-danger">*</span></label>
                            <span class="form-control">{{ $meta->sub_programa }}</span>
                        </div>
                    </div>
    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Código Actividad <span class="text-danger">*</span></label>
                            <span class="form-control">{{ $meta->actividadID }}</span>
                        </div>
                    </div>
    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Actividad <span class="text-danger">*</span></label>
                            <span class="form-control">{{ $meta->actividad }}</span>
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