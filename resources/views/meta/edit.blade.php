@extends('layouts.app')

@section('titulo')
    - Editar Meta presupuestal
@endsection

@section('link')
    Planilla
@endsection

@section('content')

<div class="col-md-12 mb-2">
    <a href="{{ route('meta.index') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
</div>

@if (session('update'))
    <div class="col-md-12 mt-3 ">
        <div class="alert alert-primary">
            {{ session('update') }}       
        </div>
    </div>
@endif
    
<div class="col-md-12">
    <form class="card" method="post" action="{{ route('meta.update', $meta->id) }}">
        @csrf
        @method('PUT')
        <h4 class="card-header">Editar Meta Presupuestal <b><span class="text-danger">>></span> <span class="uppercase">{{ $meta->meta }}</span></b></h4>
        <div class="card-body">
            <div class="row">
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Código de Meta <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="{{ $meta->metaID }}" name="metaID">
                        <b class="text-danger">{{ $errors->first('metaID') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Meta <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="{{ $meta->meta }}" name="meta">
                        <b class="text-danger">{{ $errors->first('meta') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Código de Sector <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="{{ $meta->sectorID }}" name="sectorID">
                        <b class="text-danger">{{ $errors->first('sectorID') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Sector <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="{{ $meta->sector }}" name="sector">
                        <b class="text-danger">{{ $errors->first('sector') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Código de Pliego <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="{{ $meta->pliegoID }}" name="pliegoID">
                        <b class="text-danger">{{ $errors->first('pliegoID') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Pliego <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="{{ $meta->pliego }}" name="pliego">
                        <b class="text-danger">{{ $errors->first('pliego') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Código Unidad Ejecutora <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="{{  $meta->unidadID }}" name="unidadID">
                        <b class="text-danger">{{ $errors->first('unidadID') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Unidad Ejecutora <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="{{ $meta->unidad_ejecutora }}" name="unidad_ejecutora">
                        <b class="text-danger">{{ $errors->first('unidad_ejecutora') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Código Programa <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="{{ $meta->programaID }}" name="programaID">
                        <b class="text-danger">{{ $errors->first('programaID') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Programa <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="{{ $meta->programa }}" name="programa">
                        <b class="text-danger">{{ $errors->first('unidad_ejecutora') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Código Función <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="{{ $meta->funcionID }}" name="funcionID">
                        <b class="text-danger">{{ $errors->first('funcionID') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Función <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="{{ $meta->funcion }}" name="funcion">
                        <b class="text-danger">{{ $errors->first('funcion') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Código Sub Programa <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="{{ $meta->subProgramaID }}" name="subProgramaID">
                        <b class="text-danger">{{ $errors->first('subProgramaID') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Sub Programa <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="{{ $meta->sub_programa }}" name="sub_programa">
                        <b class="text-danger">{{ $errors->first('sub_programa') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Código Actividad <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="{{ $meta->actividadID }}" name="actividadID">
                        <b class="text-danger">{{ $errors->first('actividadID') }}</b>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Actividad <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="{{ $meta->actividad }}" name="actividad">
                        <b class="text-danger">{{ $errors->first('actividad') }}</b>
                    </div>
                </div>


                <div class="col-md-12 mt-4">
                    <button class="btn btn-primary"><i class="fas fa-sync"></i> Actualizar</button>
                </div>

            </div>        
        </div>
    </form>
</div>

@endsection