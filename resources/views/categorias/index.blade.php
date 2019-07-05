@extends('layouts.app')

@section('titulo')
    - Categorias
@endsection

@section('link')
    Planilla
@endsection

@section('content')

<div class="col-md-12">
    <a href="{{ route('planilla') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
    <a href="{{ route('categoria.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> nuevo</a>
</div>

@if (session('success'))
    <div class="col-md-12 mt-3 ">
        <div class="alert alert-success">
            {{ session('success') }}       
        </div>
    </div>
@endif

<div class="col-md-12 mt-4">
    {!! $categorias->links() !!}
</div>

<div class="col-md-12">

    <div class="card">
        <div class="card-header card-header-danger">
            <h4 class="card-title">Lista de Categorias</h4>
            <p class="card-category">Sub Módulo de Gestión de categorias</p>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead class="text-primary">
                        <tr>
                            <th>#ID</th>
                            <th>Descripción</th>
                            <th>Conceptos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categorias as $categoria)
                            <tr id="categoria-{{ $categoria->id }}">
                                <th>{{ $categoria->id }}</th>
                                <th class="uppercase">{{ $categoria->nombre }}</th>
                                <th>
                                    <a href="{{ route('categoria.concepto', [$categoria->id, "page={$categorias->currentPage()}"]) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                    @foreach ($categoria->conceptos as $concepto)
                                        <a href="#" class="btn btn-sm uppercase">
                                            {{ $concepto->descripcion }}  <i class="fas fa-arrow-right text-dark ml-1 mr-1"></i>
                                            <b class="badge badge-danger">
                                               S./ {{ $concepto->pivot ? $concepto->pivot->monto : $concepto->monto }}
                                            </b>
                                        </a>
                                    @endforeach
                                </th>
                                <th>
                                    <div class="btn-group">
                                        <a href="{{ route('categoria.edit', $categoria->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <a target="__blank" href="{{ route('categoria.config', [$categoria->id, "page={$categorias->currentPage()}"]) }}" class="btn btn-sm btn-success">
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