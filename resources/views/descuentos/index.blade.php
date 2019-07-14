@extends('layouts.app')

@section('titulo')
    - Descuentos
@endsection

@section('link')
    Planilla
@endsection

@section('content')

<div class="col-md-12 mb-2">
    <a href="{{ route('planilla') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
    <a href="{{ route('descuento.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> nuevo</a>
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
        <div class="card-header card-header-danger">
            <h4 class="card-title">Lista de Descuentos</h4>
            <p class="card-category">Sub Módulo de Gestión de descuentos</p>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead class="text-primary">
                        <tr>
                            <th>#ID</th>
                            <th>Descripción</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($descuentos as $descuento)
                            <tr id="categoria-{{ $descuento->id }}">
                                <th>{{ $descuento->id }}</th>
                                <th class="uppercase">{{ $descuento->descripcion }}</th>
                                <th>
                                    <div class="btn-group">
                                        <a href="{{ route('descuento.edit', $descuento->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <a target="blank" href="{{ route('descuento.config', $descuento->id) }}" class="btn btn-sm btn-success">
                                            <i class="fas fa-cog"></i>
                                        </a>
                                    </div>
                                </th>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">No hay registros</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>        

</div>
@endsection