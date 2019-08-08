@extends('layouts.app')

@section('titulo')
    - Cronogramas
@endsection

@section('link')
    Planilla
@endsection

@section('content')


<div class="col-md-12 mb-2">
    <a href="{{ route('cronograma.index') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
    @if ($cronograma->adicional)
        <add-work
            theme="btn-primary"
            class="text-left"
            :cronograma="{{ $cronograma }}"
        >
            Agregar Tragajadores
        </add-work>
    @endif
</div>

<h3 class="text-center uppercase">
    @if ($cronograma->adicional)
        Adicional 
        <span class="btn btn-sm btn-primary">{{ $cronograma->numero }}</span>
        <span class="text-danger"> >> </span>
    @endif
    {{ $cronograma->planilla ? $cronograma->planilla->descripcion : null }}
</h3>

@if (session('success'))
    <div class="col-md-12 mt-3 ">
        <div class="alert alert-success">
            {{ session('success') }}       
        </div>
    </div>
@endif


@if (session('danger'))
    <div class="col-md-12 mt-3 ">
        <div class="alert alert-danger">
            {{ session('danger') }}       
        </div>
    </div>
@endif


<div class="col-md-12 mb-2">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Importar Remuneraciones 
                    <a href="{{ url('/formatos/remuneracion_import.xlsx') }}" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-file-excel"></i> Formato
                    </a>
                </div>
                <form class="card-body" method="POST" 
                    action="{{ route('import.remuneracion', $cronograma->slug()) }}"
                    enctype="multipart/form-data"
                >
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <label for="import_remuneracion" class="btn btn-sm btn-block btn-outline-primary">
                                <input type="file" name="import_remuneracion" id="import_remuneracion" hidden>
                                <i class="fas fa-upload"></i> Subir Archivo de Excel
                            </label>
                            <small class="text-danger">
                                {{ $errors->first('import_remuneracion') }}
                            </small>
                        </div>
                        <div class="col-md-6">
                            <validacion btn_text="Importar"></validacion>
                        </div>
                    </div>

                </form>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Importar Descuentos
                    <a href="{{ url('/formatos/descuento_import.xlsx') }}" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-file-excel"></i> Formato
                    </a>
                </div>
                <form class="card-body" method="POST" 
                    action="{{ route('import.descuento', $cronograma->slug()) }}"
                    enctype="multipart/form-data"
                >
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <label for="import_descuento" class="btn btn-block btn-sm btn-outline-primary">
                                <input type="file" name="import_descuento" id="import_descuento" hidden>
                                <i class="fas fa-upload"></i> Subir Archivo de Excel
                            </label>
                            <small class="text-danger">
                                {{ $errors->first('import_descuento') }}
                            </small>
                        </div>
                        <div class="col-md-6">
                            <validacion btn_text="Importar"></validacion>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="col-md-12">

    @if ($jobs)
        {{ $jobs->links() }}
    @endif

    <div class="card">
        <div class="card-header card-header-danger">
            <h4 class="card-title">Lista de Trabajadores </h4>
            <p class="card-category">Que pertenecen a esta planilla</p>
        </div>
        <div class="card-body">
            <div class="table-responsive">

                <form class="col-md-12 mb-3" method="GET">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="query_search" value="{{ $like }}">
                        </div>
        
                        <div class="col-md-3">
                            <button class="btn btn-info">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>    
                    </div>
                </form>


                <table class="table">
                    <thead class="text-primary">
                        <tr>
                            <th>#ID</th>
                            <th>Nombre Completo</th>
                            <th>Categorias</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jobs as $job)
                            <tr>
                                <th>{{ $job->id }}</th>
                                <th class="uppercase">{{ $job->nombre_completo }}</th>
                                <th class="uppercase">
                                    @foreach ($job->infos as $info)
                                        <div class="btn btn-sm btn-danger">
                                            {{ $info->categoria ? $info->categoria->nombre : '' }}
                                        </div>
                                    @endforeach
                                </th>
                                <th>
                                    <div class="btn-group">
                                        <a href="{{ route('job.show', $job->slug()) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a title="remuneracion"
                                            href="{{ route('job.remuneracion', [$job->slug(), "mes={$cronograma->mes}&year={$cronograma->año}&adicional={$cronograma->adicional}"]) }}" 
                                            class="btn btn-sm btn-warning"
                                        >
                                            <i class="fas fa-coins"></i>
                                        </a>
                                        <a title="descuento"
                                            href="{{ route('job.descuento', [$job->slug(), "mes={$cronograma->mes}&year={$cronograma->año}&adicional={$cronograma->adicional}"]) }}" 
                                            class="btn btn-sm btn-danger"
                                        >
                                            <i class="fab fa-creative-commons-nc"></i>
                                        </a>
                                    </div>
                                </th>
                            </tr>
                        @empty
                            <tr>
                                <th colspan="5" class="text-center">No hay registros disponibles</th>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>        

</div>
@endsection