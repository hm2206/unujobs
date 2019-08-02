@extends('layouts.app')

@section('titulo')
    - Trabajadores
@endsection

@section('link')
    Planilla
@endsection

@section('content')

<div class="col-md-12 mb-2">
    <a href="{{ route('job.edit', $work->slug()) }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
</div>

<h4 class="col-md-12 mt-3">
    Trabajador 
    <i class="fas fa-arrow-right text-danger ml-1"></i> <span class="capitalize">{{ strtolower($work->nombre_completo) }}</span>
    <i class="fas fa-arrow-right text-danger ml-1"></i> Configuración
</h4>

@if (session('success'))
    <div class="col-md-12 mt-3 ">
        <div class="alert alert-success">
            {{ session('success') }}       
        </div>
    </div>
@endif


<div class="col-md-12">

    <div class="card mt-3">
        <div class="card-header">
            <span class="btn btn-sm btn-dark btn-circle">
                <i class="fas fa-cog"></i>
            </span> Configurar Cargos
        </div>
        <div class="card-body">
            <config-work token="{{ csrf_token() }}" :errors="{{ $errors }}" work_id="{{ $work->id }}"></config-work>
        </div>
        <hr>
        <div class="card-body">
            <div class="row">
                @foreach ($work->infos as $info)
                    <div class="col-md-4">
                        <div class="btn btn-primary mb-1">
                            {{ $info->cargo ? $info->cargo->descripcion : '' }} 
                            <i class="fas fa-arrow-right text-warning"></i>
                            <span class="btn btn-sm btn-danger">
                                {{ $info->categoria ? $info->categoria->nombre : '' }}    
                            </span> 
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <form class="card mt-3" method="POST" action="{{ route('job.sindicato.store', $work->id) }}">
        @csrf
        <div class="card-header">
            <span class="btn btn-sm btn-dark btn-circle">
                <i class="fas fa-cog"></i>
            </span> Configurar Sindicatos
        </div>
        <div class="card-body">
            <div class="row">
                @foreach ($sindicatos as $sindicato)
                    <div class="col-md-4">
                        @if ($sindicato->check)
                            <input type="checkbox" name="sindicatos[]" value="{{ $sindicato->id }}" checked> 
                        @else
                            <input type="checkbox" name="sindicatos[]" value="{{ $sindicato->id }}"> 
                        @endif
                        
                        {{ $sindicato->nombre }}
                    </div>
                @endforeach
            </div>
        </div>
        <div class="card-footer">
            <button class="btn btn-success">
                <i class="fas fa-save"></i> Guardar
            </button>
        </div>
    </form>

</div>
@endsection