@extends('layouts.app')

@section('titulo')
    - Categoria >> Configuración
@endsection

@section('link')
    Planilla
@endsection

@section('content')

<div class="col-md-12">
    <a href="{{ route('categoria.index') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
</div>

@if (session('success'))
    <div class="col-md-12 mt-3 ">
        <div class="alert alert-success">
            {{ session('success') }}       
        </div>
    </div>
@endif

<div class="col-md-12">

    <div class="card">
        <h4 class="card-header">
            <b>Configuración de categoria <span class="text-danger">>></span> {{ $categoria->nombre }} </b>
        </h4>
        @foreach ($types as $type)
            <hr>
            <form class="card-body" action="{{ route('categoria.config.store', $categoria->id) }}" method="post">
                @csrf
                <h4>{{ $type->key }}. <b>{{ $type->descripcion }}</b></h4>
                <input type="hidden" name="type_remuneracion_id" value="{{ $type->id }}">
                
                <hr>

                <div class="row">
                    @foreach ($type->conceptos as $concepto)
                        <div class="col-md-4">
                            <div class="form-group">
                                <input type="checkbox" name="conceptos[]" value="{{ $concepto->id }}" {!! $concepto->check ? "checked" : null !!}>
                                <label for="">{{ $concepto->descripcion }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>

                <hr>

                <button class="btn btn-primary" type="submit">Guardar <i class="fas fa-save"></i></button>

            </form>
            <hr>
        @endforeach
    </div>

</div>
@endsection