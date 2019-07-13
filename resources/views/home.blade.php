@extends('layouts.app')

@section('titulo')
    - Inicio
@endsection

@section('link')
    Módulos de Recursos Humanos
@endsection


@section('content')

    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header card-header-warning card-header-icon">
                    <div class="card-icon">
                        <i class="fas fa-user-clock"></i>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('personal.create') }}"><b>Registro de Requerimiento de Personal</b></a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header card-header-danger card-header-icon">
                    <div class="card-icon">
                        <i class="fas fa-user-friends"></i>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('convocatoria.create') }}"><b>Registro de Convocatoria</b></a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header card-header-success card-header-icon">
                    <div class="card-icon">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('postulante.create') }}"><b>Registro de Postulante</b></a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header card-header-primary card-header-icon">
                    <div class="card-icon">
                        <i class="fab fa-get-pocket"></i>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('evaluacion.index') }}"><b>Registro de Evaluación y Selección</b></a>
                </div>
            </div>
        </div>

    </div>
    
@endsection
