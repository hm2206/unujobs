@extends('layouts.app')

@section('titulo')
    - Descuentos
@endsection

@section('link')
    Planilla
@endsection

@section('content')

<div class="col-md-12 mb-2">
    <a href="{{ route('home') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
    <btn-descuento
        theme="btn-primary"
        redirect="{{ route('descuento.index')}}"
    >
        <i class="fas fa-plus"></i> Nuevo
    </btn-descuento>
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
                            <th>Tipo</th>
                            <th>Activo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($descuentos as $descuento)
                            <tr id="categoria-{{ $descuento->id }}">
                                <th>{{ $descuento->key }}</th>
                                <th class="uppercase">{{ $descuento->descripcion }}</th>
                                <th class="uppercase">
                                    @if ($descuento->base)
                                        <button class="btn btn-sm btn-primary">
                                            Aportación
                                        </button>
                                    @endif

                                    @if ($descuento->retencion)
                                        <button class="btn btn-sm btn-danger">
                                            Retención
                                        </button>
                                    @endif
                                </th>
                                <th>
                                    {{ $descuento->activo ? 'Si' : 'No' }}
                                </th>
                                <th>
                                    <div class="btn-group">
                                        <btn-descuento
                                            theme="btn-warning btn-sm"
                                            redirect="{{ route('descuento.index')}}"
                                            :datos="{{ $descuento }}"
                                        >
                                            <i class="fas fa-pencil-alt"></i>
                                        </btn-descuento>
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