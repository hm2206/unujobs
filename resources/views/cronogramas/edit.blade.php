@extends('layouts.app')

@section('titulo')
    - Editar Cronograma >> {{ base64_encode($cronograma->id) }}
@endsection

@section('link')
    Planilla
@endsection

@section('content')

<div class="col-md-12 mb-2">
    <a href="{{ route('cronograma.index') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
</div>


@if (session('success'))
    <div class="col-md-12 mt-3 ">
        <div class="alert alert-success">
            {{ session('success') }}       
        </div>
    </div>
@elseif(session('danger'))
    <div class="col-md-12 mt-3 ">
        <div class="alert alert-danger">
            {{ session('danger') }}       
        </div>
    </div>
@endif

<div class="col-md-7">

    <div class="card">
        <div class="card-header">
            Editar Cronograma</b>
        </div>
        <form class="card-body" action="{{ route('cronograma.update', $cronograma->id) }}" method="post">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="" class="form-control-label">Planilla <span class="text-danger">*</span></label>
                <span class="form-control">{{ $cronograma->planilla ? $cronograma->planilla->descripcion : null }}</span>
            </div>

            <div class="form-group">
                <label for="" class="form-control-label">Observación</label>
                <textarea name="observacion" class="form-control">{{ old('observacion') ? old('observacion') : $cronograma->observacion }}</textarea>
            </div>

            <button class="btn btn-primary mt-4" type="submit"><i class="fas fa-sync"></i> Actualizar</button>

        </form>
    </div>

</div>
@endsection