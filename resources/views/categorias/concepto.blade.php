@extends('layouts.app')

@section('titulo')
    - Categoria << Concepto
@endsection

@section('link')
    Planilla
@endsection

@section('content')

<div class="row">
    <div class="col-md-12 mb-2">
        <a href="{{ route('categoria.index') . "?page=" . request()->page . "#categoria-{$categoria->id}" }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
    </div>
</div>

@if (session('success'))
    <div class="col-md-12 mt-3 ">
        <div class="alert alert-success">
            {{ session('success') }}       
        </div>
    </div>
@endif

<div class="row">
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
                            <option value="{{ $concepto->id }}">{{ $concepto->key }} => {{ $concepto->descripcion }}</option>
                        @endforeach
                    </select>
                    <b class="text-danger">{{ $errors->first('concepto_id') }}</b>
                </div>
    
                <div class="form-group">
                    <label class="form-control-label">Monto</label>
                    <input type="text" name="monto" class="form-control">
                    <b class="text-danger">{{ $errors->first('monto') }}</b>
                </div>
    
                <button class="btn btn-primary" type="submit">Guardar <i class="fas fa-save"></i></button>
    
            </form>
        </div>
    
    </div>
    
    <div class="col-md-5">
        <div class="card-body">
            <div class="row justify-content-start">
                @foreach ($categoria->conceptos as $concepto)
                @php
                    $concepto->pivot
                @endphp
                    {{ $concepto }}
                    <edit-concepto :concepto="{{ $concepto }}"></edit-concepto>
                @endforeach
            </div>
        </div>
    </div>

</div>
@endsection