@extends('layouts.app')

@section('titulo')
    - Trabajadores
@endsection

@section('link')
    Planilla
@endsection

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
</div>

<div class="row">
    <div class="col-md-12">
        <a href="{{ route('home') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
        <a href="{{ route('job.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> nuevo</a>
    </div>
    
    @if (session('success'))
        <div class="col-md-12 mt-3 ">
            <div class="alert alert-success">
                {{ session('success') }}       
            </div>
        </div>
    @endif
    
    <div class="col-md-12 mt-3">
    
        <form method="GET" class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" placeholder="Buscar..." name="query_search" value="{{ request('query_search') }}" class="form-control" autofocus>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-info">Buscar <i class="fas fa-search"></i></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <div class="col-md-12 mt-5">
    
        {!! $jobs->appends(['query_search' => request('query_search')])->links() !!}
    
        <div class="card">
            <div class="card-header card-header-danger">
                <h4 class="card-title">Lista Rápida de Trabajadores</h4>
                <p class="card-category">Sub Módulo de Trabajadores</p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead class="text-primary">
                            <tr>
                                <th>Nombre Completo</th>
                                <th>Número de documento</th>
                                <th>Profesión</th>
                                <th>teléfono</th>
                                <th>Número de cuenta</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($jobs as $job)
                                <tr class="uppercase capitalize">
                                    <th>{{ $job->nombre_completo }}</th>
                                    <th>{{ $job->numero_de_documento }}</th>
                                    <th>{{ $job->profesion }}</th>
                                    <th>{{ $job->phone }}</th>
                                    <th>{{ $job->numero_de_cuenta }}</th>
                                    <th>
                                        <div class="btn-group">
                                            <a href="{{ route('job.edit', $job->id) }}" class="btn btn-circle btn-xs btn-warning">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <a href="{{ route('job.show', $job->id) }}" class="btn btn-xs btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('job.boleta', $job->id) }}" target="blank" title="Boleta" class="btn btn-circle btn-xs btn-dark">
                                                <i class="fas fa-file"></i>
                                            </a>
                                        </div>
                                    </th>
                                </tr>
                            @empty
                                <tr class="text-center">
                                    <td colspan="7">No hay registros disponibles</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>        
    
    </div>
</div>

@endsection