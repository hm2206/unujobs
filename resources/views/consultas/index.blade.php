@extends('layouts.bolsa')


@section('title', 'CONSULTAS')

@section('content')
    
<div class="row mt-5 justify-content-center">
    <div class="col-md-5 mt-5">

        @if (session('danger'))
            <div class="alert alert-danger">
                {{ session('danger') }}
            </div>
        @endif

        <form action="{{ route('consulta.validar') }}" method="POST" class="card">
            @csrf
            <div class="card-header">
                Consulta de Boletas Informátivas de Pago
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="">Tipo de Documento <span class="text-danger">*</span></label>
                    <select name="tipo_de_documento" class="form-control">
                        <option value="1">DNI</option>
                    </select>
                    <small class="text-danger">{{ $errors->first('tipo_de_documento') }}</small>
                </div>
                <div class="form-group">
                    <label for="">Numero de Identidad <span class="text-danger">*</span></label>
                    <input type="text" name="numero_de_identidad" class="form-control" placeholder="Ingrese su número de DNI">
                    <small class="text-danger">{{ $errors->first('tipo_de_identidad') }}</small>
                </div>
                <div class="form-group">
                   <button class="btn btn-primary">
                       Consultar <i class="fas fa-file-pdf"></i>
                   </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection