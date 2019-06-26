@extends('layouts.app')


@section('titulo')
    - Configuración del módulo planillas
@endsection


@section('link')
    Módulos de Planilla
@endsection


@section('content')
  
<div class="row">

        <div class="col-md-12">
            <a href="{{ route('planilla') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atras</a>
        </div>


        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header card-header-danger card-header-icon">
                    <div class="card-icon">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('cargo.index') }}"><b>Gestión de cargos</b></a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header card-header-danger card-header-icon">
                    <div class="card-icon">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('categoria.index') }}"><b>Gestión de categorias</b></a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header card-header-danger card-header-icon">
                    <div class="card-icon">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('concepto.index') }}"><b>Gestión de conceptos</b></a>
                </div>
            </div>
        </div>

    </div>

@endsection
