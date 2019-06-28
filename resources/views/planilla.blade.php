@extends('layouts.app')


@section('titulo')
    - Módulos de Planillas
@endsection


@section('link')
    Módulos de Planilla
@endsection


@section('content')
  
<div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header card-header-danger card-header-icon">
                    <div class="card-icon">
                        <i class="fas fa-users-cog"></i>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('job.index') }}"><b>Trabajadores</b></a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header card-header-warning card-header-icon">
                    <div class="card-icon">
                        <i class="fas fa-meteor"></i>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('meta.index') }}"><b>Metas</b></a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header card-header-success card-header-icon">
                    <div class="card-icon">
                        <i class="fas fa-calendar-week"></i>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('cronograma.index') }}"><b>Crear Planilla x Mes</b></a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header card-header-primary card-header-icon">
                    <div class="card-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('cargo.index') }}"><b>Cargos</b></a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header card-header-info card-header-icon">
                    <div class="card-icon">
                        <i class="fas fa-certificate"></i>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('categoria.index') }}"><b>Categorias</b></a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header card-header-dark card-header-icon">
                    <div class="card-icon">
                        <i class="fas fa-cube"></i>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('concepto.index') }}"><b>Conceptos</b></a>
                </div>
            </div>
        </div>

    </div>

@endsection
