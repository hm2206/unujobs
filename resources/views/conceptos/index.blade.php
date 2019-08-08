@extends('layouts.app')

@section('titulo')
    - Concepto
@endsection

@section('link')
    Planilla
@endsection

@section('content')

<div class="col-md-12 mb-2">
    <a href="{{ route('home') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
    <btn-concepto
        theme="btn-primary"
       redirect="{{ route('concepto.index')}}"
    >
        <i class="fas fa-plus"></i> Nuevo
    </btn-concepto>
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
            <h4 class="card-title">Lista de Conceptos</h4>
            <p class="card-category">Sub Módulo de Gestión de conceptos</p>
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
                        @forelse ($conceptos as $concepto)
                            <tr>
                                <th>{{ $concepto->id }}</th>
                                <th class="uppercase">{{ $concepto->descripcion }}</th>
                                <th>
                                    <div class="btn-group">
                                        <btn-concepto
                                            theme="btn-warning btn-sm"
                                            redirect="{{ route('concepto.index')}}"
                                            :datos="{{ $concepto }}"
                                        >
                                            <i class="fas fa-pencil-alt"></i>
                                        </btn-concepto>
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