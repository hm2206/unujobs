@extends('layouts.app')

@section('titulo')
    - Cargos + Categoria
@endsection

@section('link')
    Planilla
@endsection

@section('content')

<div class="col-md-12 mb-2">
    <a href="{{ route('cargo.index') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
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
            Registro de cargo</b>
        </div>
        <form class="card-body" action="{{ route('cargo.update', $cargo->id) }}" method="post">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="" class="form-control-label">Descripción <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="descripcion" value="{{ old('descripcion') ? old('descripcion') : $cargo->descripcion }}">
                <b class="text-danger">{{ $errors->first('descripcion') }}</b>
            </div>

            <div class="form-group">
                <label for="" class="form-control-label">Planilla <span class="text-danger">*</span></label>
                <select name="planilla_id" class="form-control">
                    <option value="">Seleccionar...</option>
                    @foreach ($planillas as $planilla)
                        @if (old('planilla_id'))
                            <option value="{{ $planilla->id }}" {!! old('planilla_id') == $planilla->id ? 'selected' : '' !!}>{{ $planilla->descripcion }}</option>
                        @else
                            <option value="{{ $planilla->id }}" {!! $cargo->planilla_id == $planilla->id ? 'selected' : '' !!}>{{ $planilla->descripcion }}</option>
                        @endif
                    @endforeach
                </select>
                <b class="text-danger">{{ $errors->first('planilla_id') }}</b>
            </div>

            <button class="btn btn-primary" type="submit"><i class="fas fa-sync"></i> Actualizar</button>

        </form>
    </div>

</div>
@endsection