@extends('layouts.app')

@section('titulo')
    - Registro de Requerimiento de Personal
@endsection


@section('link')
    Recursos Humanos
@endsection

@section('content')

<div class="col-md-12">
    <a href="{{ route('job.edit', $id) }}" class="btn btn-warning"><i class="material-icons">undo</i> atrás</a>
</div>


<div class="col-md-12">

    <form class="card" action="{{ route('postulante.store') }}" method="POST">
            @csrf
            <h4 class="card-header">Afectación presuestal <b class="text-danger">>></b> <b>Nombre</b></h4>
            <div class="card-body">

                <span>Campo obligatorio (<span class="text-danger">*</span>)</span>
                <hr>

                <div class="row">

                    @if(session('success'))
                        <div class="col-md-12">
                            <div class="alert alert-success" role="alert">
                                <b>{{ session('success') }}</b>
                            </div>
                        </div>
                    @endif

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Meta <span class="text-danger">*</span></label>
                            <select name="meta_id" class="form-control">
                                <option value="">Seleccionar...</option>
                            </select>
                            <b class="text-danger">{{ $errors->first('fecha_de_nacimiento') }}</b>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Actividad <span class="text-danger">*</span></label>
                            <input type="text" value="{{ old('phone') }}" name="phone" class="form-control">
                            <b class="text-danger">{{ $errors->first('phone') }}</b>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">P.E.A <span class="text-danger">*</span></label>
                            <input type="text" value="{{ old('phone') }}" name="phone" class="form-control">
                            <b class="text-danger">{{ $errors->first('phone') }}</b>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Categoria <span class="text-danger">*</span></label>
                            <select name="meta_id" class="form-control">
                                <option value="">Seleccionar...</option>
                            </select>
                            <b class="text-danger">{{ $errors->first('fecha_de_nacimiento') }}</b>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Dedicación</label>
                            <input type="text" value="" class="form-control" name="email" value="{{ old('email') }}">
                            <b class="text-danger">{{ $errors->first('email') }}</b>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Selección <span class="text-danger">*</span></label>
                            <select name="meta_id" class="form-control">
                                <option value="">Seleccionar...</option>
                            </select>
                            <b class="text-danger">{{ $errors->first('fecha_de_nacimiento') }}</b>
                        </div>
                    </div>

                     <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Condición P.A.P</label>
                            <input type="text" value="" class="form-control" name="email" value="{{ old('email') }}">
                            <b class="text-danger">{{ $errors->first('email') }}</b>
                        </div>
                    </div>
    
                    <div class="col-md-12 mt-4">
                        <button class="btn btn-primary">Guardar <i class="material-icons">save</i></button>
                    </div>
    
                </div>        
            </div>
        </form>

</div>
    

@endsection