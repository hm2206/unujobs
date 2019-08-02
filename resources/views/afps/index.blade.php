@extends('layouts.app')

@section('titulo')
    - AFP
@endsection

@section('link')
    Planilla
@endsection

@section('content')

<div class="col-md-12 mb-2">
    <a href="{{ route('home') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
    <a href="{{ route('afp.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> nuevo</a>
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
            <h4 class="card-title">Lista de Afps</h4>
            <p class="card-category">Sub Módulo de Gestión de afps</p>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead class="text-primary">
                        <tr>
                            <th>#ID</th>
                            <th>Descripción</th>
                            <th>Flujo</th>
                            <th>Mixta</th>
                            <th>Aporte</th>
                            <th>Prima</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($afps as $afp)
                            <tr id="categoria-{{ $afp->id }}">
                                <th>{{ $afp->id }}</th>
                                <th class="uppercase">{{ $afp->nombre }}</th>
                                <th class="uppercase">{{ $afp->flujo }} %</th>
                                <th class="uppercase">{{ $afp->mixta }} %</th>
                                <th class="uppercase">{{ $afp->aporte }} %</th>
                                <th class="uppercase">{{ $afp->prima }} %</th>
                                <th>
                                    <div class="btn-group">
                                        <a href="{{ route('afp.edit', $afp->slug()) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-pencil-alt"></i>
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