@extends('layouts.app')


@section('titulo')
    - Registro de Postulante
@endsection

@section('link')
    Recursos Humanos
@endsection

@section('content')

<div class="col-md-12">
    <a href="{{ route('home') }}" class="btn btn-success"><i class="material-icons">undo</i> atrás</a>
</div>
    
<div class="col-md-12">

    <form method="GET" class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Número de Identidad</label>
                        <input type="number" name="documento" value="{{ request()->documento }}" class="form-control" autofocus>
                        @if ($result->success == false)
                            <span class="text-danger">
                                {{ $result->message }}
                            </span>
                        @endif  
                    </div>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-info">Buscar <i class="material-icons">search</i></button>
                </div>
            </div>
        </div>
    </form>

    @if ($result->success)
        <form class="card" action="{{ route('postulante.store') }}" method="POST">
            @csrf
            <h4 class="card-header">Registro de Postulante</h4>
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
                            <label for="" class="form-control-label">Apellido paterno <span class="text-danger">*</span></label>
                            <span class="form-control">{{ $result->body->ape_paterno }}</span>
                            <input type="hidden" name="ape_paterno" value="{{ $result->body->ape_paterno }}">
                            <b class="text-danger">{{ $errors->first('ape_paterno') }}</b>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Apellido materno <span class="text-danger">*</span></label>
                            <span class="form-control">{{ $result->body->ape_materno }}</span>
                            <input type="hidden" name="ape_materno" value="{{ $result->body->ape_materno }}">
                            <b class="text-danger">{{ $errors->first('ape_materno') }}</b>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Nombres <span class="text-danger">*</span></label>
                            <span class="form-control">{{ $result->body->primer_nombre . " " . $result->body->segundo_nombre }}</span>
                            <input type="hidden" name="nombres" value="{{ $result->body->primer_nombre . " " . $result->body->segundo_nombre }}">
                            <b class="text-danger">{{ $errors->first('nombres') }}</b>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Número de documento <span class="text-danger">*</span></label>
                            <span class="form-control">{{ $result->body->numero_de_documento }}</span>
                            <input type="hidden" name="numero_de_documento" value="{{ $result->body->numero_de_documento }}">
                            <b class="text-danger">{{ $errors->first('numero_de_documento') }}</b>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Departamento <span class="text-danger">*</span></label>
                            <span class="form-control">{{ $result->body->departamento }}</span>
                            <input type="hidden" name="departamento" value="{{ $result->body->departamento }}">
                            <b class="text-danger">{{ $errors->first('departamento') }}</b>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Provincia <span class="text-danger">*</span></label>
                            <span class="form-control">{{ $result->body->provincia }}</span>
                            <input type="hidden" name="provincia" value="{{ $result->body->provincia }}">
                            <b class="text-danger">{{ $errors->first('provincia') }}</b>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Distrito <span class="text-danger">*</span></label>
                            <span class="form-control">{{ $result->body->distrito }}</span>
                            <input type="hidden" name="distrito" value="{{ $result->body->distrito }}">
                            <b class="text-danger">{{ $errors->first('distrito') }}</b>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Fecha de Nacimiento <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_de_nacimiento" value="{{ old('fecha_de_nacimiento') }}" class="form-control">
                            <b class="text-danger">{{ $errors->first('fecha_de_nacimiento') }}</b>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Número de celular o Teléfono <span class="text-danger">*</span></label>
                            <input type="text" value="{{ old('phone') }}" name="phone" class="form-control">
                            <b class="text-danger">{{ $errors->first('phone') }}</b>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-control-label">Email</label>
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
    @endif

</div>

@endsection