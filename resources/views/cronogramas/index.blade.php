@extends('layouts.app')

@section('titulo')
    - Cronogramas
@endsection

@section('link')
    Planilla
@endsection

@section('content')

<div class="col-md-12">
    <a href="{{ route('planilla') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
    <a href="{{ route('cronograma.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> nuevo</a>
</div>

<div class="col-md-12">
    <div class="card">
        <h4 class="card-header">
            Cronograma
        </h4>
        <hr>
        <form class="card-body" action="" method="GET">
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="">Mes</label>
                        <input type="number" name="mes" class="form-control" min="1" max="12" value="{{ $mes }}">
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label for="">Año</label>
                        <input type="number" name="year" max="{{ date('Y') }}" min="{{ date('Y') - 2 }}" class="form-control" value="{{ $year }}">
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label for="" class="form-control-label">Adicional</label> <br>
                        <input type="checkbox" name="adicional" {!! $adicional ? 'checked' : null !!}>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <button class="btn btn-info">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@if (session('success'))
    <div class="col-md-12 mt-3 ">
        <div class="alert alert-success">
            {{ session('success') }}       
        </div>
    </div>
@endif

<div class="col-md-12">

    {{ $cronogramas->links() }}

    <div class="card">
        <div class="card-header card-header-danger">
            <h4 class="card-title">Lista de Cronogramas</h4>
            <p class="card-category">Sub Módulo de Gestión de cronogramas</p>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead class="text-primary">
                        <tr>
                            <th>#ID</th>
                            <th>Descripción</th>
                            <th>Sede</th>
                            <th>Planilla</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cronogramas as $cronograma)
                            <tr>
                                <th>{{ $cronograma->id }}</th>
                                <th class="uppercase">{{ $cronograma->descripcion ? $cronograma->descripcion : "No tiene" }}</th>
                                <th class="uppercase">{{ $cronograma->sede ? $cronograma->sede->descripcion : null }}</th>
                                <th class="uppercase">{{ $cronograma->planilla ? $cronograma->planilla->descripcion : null }}</th>
                            </tr>
                        @empty
                            <tr class="text-center">
                                <td colspan="5">No hay registros</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>        

</div>
@endsection