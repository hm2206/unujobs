@extends('layouts.bolsa')

@section('content')
    
    <div class="row mt-4">

        <div class="col-md-12">
            <a href="{{ route('bolsa.show', [$convocatoria->slug(), $personal->slug]) }}" class="btn btn-danger">
                <i class="fas fa-arrow-left"></i> atrás
            </a>
        </div>

        <div class="col-md-9 mt-4">

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    Postular a la convocatoria N° {{ $convocatoria->numero_de_convocatoria }}-{{ $year }}-UNU
                    <span class="ml-3 text-danger mr-3"><i class="fas fa-arrow-right"></i></span>
                    <b>{{ $personal->cargo_txt }}</b>
                </div>
                <form class="card-body" action="{{ route('postulante.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Apellido Paterno<b class="text-danger">*</b></label>
                                <input type="text" class="form-control" name="ape_paterno" value="{{ old('ape_paterno') }}">
                                <b class="text-danger">{{ $errors->first('ape_paterno') }}</b>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Apellido Materno<b class="text-danger">*</b></label>
                                <input type="text" class="form-control" name="ape_materno" value="{{ old('ape_materno') }}">
                                <b class="text-danger">{{ $errors->first('ape_materno') }}</b>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Nombres<b class="text-danger">*</b></label>
                                <input type="text" class="form-control" name="nombres" value="{{ old('nombres') }}">
                                <b class="text-danger">{{ $errors->first('nombres') }}</b>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Fecha de nacimiento<b class="text-danger">*</b></label>
                                <input type="date" class="form-control" name="fecha_de_nacimiento" value="{{ old('fecha_de_nacimiento') }}">
                                <b class="text-danger">{{ $errors->first('fecha_de_nacimiento') }}</b>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Sexo<b class="text-danger">*</b></label>
                                <select name="sexo" class="form-control">
                                    <option value="1" {!! old('sexo') == 1 ? 'checked' : null !!}>Masculino</option>
                                    <option value="0" {!! old('sexo') == 0 ? 'checked' : null !!}>Femenino</option>
                                </select>
                                <b class="text-danger">{{ $errors->first('sexo') }}</b>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Numero de Documento<b class="text-danger">*</b></label>
                                <input type="text" class="form-control" name="numero_de_documento" value="{{ old('numero_de_documento') }}">
                                <b class="text-danger">{{ $errors->first('numero_de_documento') }}</b>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <locacion :errors="{{ $errors }}" :ubigeos="{{ json_encode($ubigeos) }}"></locacion>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Dirección<b class="text-danger">*</b></label>
                                <input type="text" class="form-control" name="direccion" value="{{ old('direccion') }}">
                                <b class="text-danger">{{ $errors->first('direccion') }}</b>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Teléfono<b class="text-danger">*</b></label>
                                <input type="tel" class="form-control" name="phone" value="{{ old('phone') }}">
                                <b class="text-danger">{{ $errors->first('phone') }}</b>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Email<b class="text-danger">*</b></label>
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                                <b class="text-danger">{{ $errors->first('email') }}</b>
                            </div>
                        </div>

                        <input type="hidden" name="personal_id" value="{{ $personal->id }}">
                        <input type="hidden" name="redirect" 
                            value="{{ route('bolsa.show', [$convocatoria->slug(), $personal->slug]) }}"
                        >

                        <div class="col-md-6">
                            <button class="btn btn-success">
                                <i class="fas fa-save"></i> Postular
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


    </div>

@endsection