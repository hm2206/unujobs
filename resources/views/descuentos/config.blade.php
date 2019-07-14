@extends('layouts.app')

@section('titulo')
    - Categoria >> Configuración
@endsection

@section('link')
    Planilla
@endsection

@section('content')

<div class="col-md-12 mb-2">
    <a href="{{ route('descuento.index') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
</div>

@if (session('success'))
    <div class="col-md-12 mt-3 ">
        <div class="alert alert-success">
            {{ session('success') }}       
        </div>
    </div>
@endif

<div class="col-md-12">

    <form class="card" action="{{ route('descuento.config.store', $type->id) }}" method="post">
        @csrf
        <h4 class="card-header">
            <b>Configuración de descuento <span class="text-danger">>></span> {{ $type->descripcion }} </b>
        </h4>
        <hr>
        <div class="card-body">
            <h4><b>Sindicatos</b></h4>
                    
            <hr>

            <div class="row">
                @foreach ($sindicatos as $sindicato)
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="checkbox" name="sindicatos[]" value="{{ $sindicato->id }}" {!! $sindicato->check ? 'checked' : null !!}>
                            <i class="fa fa-arrow-right text-danger"></i>
                            <b>{{ $sindicato->nombre }}</b>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <hr>

        <div class="card-body">
            <h4><b>Configuración de AFP</b></h4>
                    
            <hr>

            <div class="row">

                @foreach ($seguros as $tmp)
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="checkbox" name="seguros[]" value="{{ strtolower($tmp['name']) }}" 
                                {!! $tmp['checked'] ? 'checked' : null !!}
                            >
                            <i class="fa fa-arrow-right text-danger"></i>
                            <b>{{ $tmp['name'] }}</b>
                        </div>
                    </div>
                @endforeach

            </div>

            <hr>

            <button class="btn btn-primary" type="submit">Guardar <i class="fas fa-save"></i></button>

        </div>
    </form>

</div>
@endsection