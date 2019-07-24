@extends('layouts.app')

@section('titulo')
    - Trabajadores
@endsection

@section('link')
    Planilla
@endsection

@section('content')

<div class="col-md-12 mb-2">
    <a href="{{ route('job.edit', $work->id) }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
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
            <form class="row"  method="GET" id="form-cargo">
                <div class="col-md-3">
                    <label for="">Cargo <span class="text-danger">*</span></label>
                    <select-change name="cargo_id" :datos="{{ json_encode($cargos) }}" 
                        value="id" text="descripcion" for="form-cargo"
                        request="{!! request()->cargo_id !!}"
                    >
                    </select-change>
                </div>
                <div class="col-md-3">
                    <label for="">Categoria <span class="text-danger">*</span></label>
                    <select-change name="categoria_id" :datos="{{ json_encode($categorias) }}" 
                        value="id" text="nombre" for="form-cargo"
                        request="{!! request()->categoria_id  !!}"
                    >
                    </select-change>
                </div>
            </form>
            
            <form action="{{ route('job.config.store', $work->id) }}" method="POST" class="mt-3">
                @csrf
                <input type="hidden" name="cargo_id" value="{{ request()->cargo_id }}">
                <input type="hidden" name="categoria_id" value="{{ request()->categoria_id }}">

                <button class="btn btn-success">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </form>

        </div>
        <hr>
        <div class="card-body">
            <div class="row">
                @foreach ($work->infos as $info)
                    <div class="col-md-4">
                        <div class="btn btn-primary">
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
                        <input type="checkbox" name="sindicatos[]" 
                            value="{{ $sindicato->id }}"
                            {!! $sindicato->check ? 'checked' : null !!}
                        > 
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