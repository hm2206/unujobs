@extends('layouts.app')

@section('titulo')
    - Categoria << Concepto
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

<div class="col-md-7">

    <div class="card">
        <div class="card-header">
            CATEGORIA <span class="text-danger">>></span> <b class="uppercase">{{ $categoria->nombre }}</b>
        </div>
        <form class="card-body" action="{{ route('categoria.concepto.store', $categoria->id) }}" method="post">
            @csrf
            <div class="form-group">
                <label for="" class="form-control-label">Concepto <span class="text-danger">*</span></label>
                <select name="concepto_id" class="form-control">
                    <option value="">Seleccionar...</option>
                    @foreach ($conceptos as $concepto)
                        <option value="{{ $concepto->id }}">{{ $concepto->descripcion }}</option>
                    @endforeach
                </select>
                <b class="text-danger">{{ $errors->first('concepto_id') }}</b>
            </div>

            <div class="form-group">
                <label class="form-control-label">Monto <span class="text-danger">(Opcional)</span></label>
                <input type="text" name="monto" class="form-control">
                <b class="text-danger">{{ $errors->first('monto') }}</b>
            </div>

            <button class="btn btn-primary" type="submit">Guardar <i class="fas fa-save"></i></button>

        </form>
    </div>

</div>
@endsection