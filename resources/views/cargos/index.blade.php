@extends('layouts.app')

@section('titulo')
    - Cargos
@endsection

@section('link')
    Planilla
@endsection

@section('content')

<div class="col-md-12 mb-2">
    <a href="{{ route('home') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
    <a href="{{ route('cargo.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> nuevo</a>
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
        <form class="card-header" method="POST" action="{{ route('export.cargo') }}">
            @csrf
            <h4 class="card-title">Lista de Cargos</h4>
            <p class="card-category">Sub Módulo de Gestión de cargos</p>
            <button class="btn btn-success">
                <i class="fas fa-file-excel"></i> Exportar Cargos
            </button>
        </form>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead class="text-primary">
                        <tr>
                            <th>#ID</th>
                            <th>Descripción</th>
                            <th>Categorias</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cargos as $cargo)
                            <tr>
                                <th>{{ $cargo->id }}</th>
                                <th class="uppercase">{{ $cargo->descripcion }}</th>
                                <th>
                                    <a href="{{ route('cargo.categoria', $cargo->id) }}" class="btn mt-1 btn-sm btn-primary"><i class="fas fa-plus"></i></a>
                                    @foreach ($cargo->categorias as $categoria)
                                        <a href="#" class="btn btn-sm btn-danger uppercase mt-1">
                                            {{ $categoria->nombre }}
                                        </a>
                                    @endforeach
                                </th>
                                <th>
                                    <div class="btn-group">
                                        <a href="{{ route('cargo.edit', $cargo->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <a href="{{ route('cargo.config', $cargo->id) }}" class="btn btn-sm btn-dark">
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