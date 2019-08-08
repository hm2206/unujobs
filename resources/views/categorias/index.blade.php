@extends('layouts.app')

@section('titulo')
    - Categorias
@endsection

@section('link')
    Planilla
@endsection

@section('content')

<div class="col-md-12">
    <a href="{{ route('home') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
    <btn-categoria
        theme="btn-primary"
        redirect="{{ route('categoria.index')}}"
    >
        <i class="fas fa-plus"></i> Nuevo
    </btn-categoria>
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
        <form class="card-header" method="POST" action="{{ route('export.categoria') }}">
            @csrf
            <h4 class="card-title">Lista de Categorias</h4>
            <p class="card-category">Sub Módulo de Gestión de categorias</p>
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
                                    <a href="{{ route('categoria.concepto', [$categoria->slug(), "page={$categorias->currentPage()}"]) }}" class="mb-1 btn btn-sm btn-primary">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                    @foreach ($categoria->conceptos as $concepto)
                                        <a href="#" class="btn btn-dark btn-sm uppercase mb-1">
                                            {{ $concepto->descripcion }}  <i class="fas fa-arrow-right text-dark ml-1 mr-1"></i>
                                            <b class="badge badge-danger">
                                               S./ {{ $concepto->pivot ? $concepto->pivot->monto : $concepto->monto }}
                                            </b>
                                        </a>
                                    @endforeach
                                </th>
                                <th>
                                    <div class="btn-group">
                                        <btn-categoria
                                            theme="btn-warning btn-sm"
                                            redirect="{{ route('categoria.index')}}"
                                            :datos="{{ $categoria }}"
                                        >
                                            <i class="fas fa-pencil-alt"></i>
                                        </btn-categoria>

                                        <a href="{{ route('categoria.config', [$categoria->slug(), "page={$categorias->currentPage()}"]) }}" class="btn btn-sm btn-success">
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