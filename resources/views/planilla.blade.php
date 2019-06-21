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
                        <i class="fas fa-users"></i>
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
                        <i class="fas fa-circle"></i>
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
                        <i class="fas fa-coins"></i>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="#pablo"><b>Pagos</b></a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header card-header-primary card-header-icon">
                    <div class="card-icon">
                        <i class="fas fa-trending"></i>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="#pablo"><b>Descuentos</b></a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header card-header-info card-header-icon">
                    <div class="card-icon">
                        <i class="fas fa-ban"></i>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="#pablo"><b>Liquidaciones</b></a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header card-header-dark card-header-icon">
                    <div class="card-icon">
                        <i class="fas fa-setting-alt"></i>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="#pablo"><b>Configuración</b></a>
                </div>
            </div>
        </div>

    </div>

@endsection
