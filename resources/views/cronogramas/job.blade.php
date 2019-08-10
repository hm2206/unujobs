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

@if ($errors->first('import_remuneracion'))
    <div class="col-md-12 mt-3 ">
        <div class="alert alert-danger">
            {{ $errors->first('import_remuneracion') }}       
        </div>
    </div>
@endif

@if ($errors->first('import_descuento'))
    <div class="col-md-12 mt-3 ">
        <div class="alert alert-danger">
            {{ $errors->first('import_descuento') }}       
        </div>
    </div>
@endif



<div class="col-md-12 mb-2">
    <btn-report-cuenta 
        :cronograma="{{ $cronograma }}"
        theme="btn-primary btn-sm"
    >
        Reporte de cuenta o cheques
    </btn-report-cuenta>

    <btn-afp 
        :id="{{ $cronograma->id  }}"
        theme="btn-primary btn-sm"
    >
        Reporte para AFPNET | DPT-PLAME
    </btn-afp>
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
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="query_search" value="{{ $like }}">
                        </div>
        
                        <div class="col-md-2">
                            <button class="btn btn-info">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>    

                        <div class="col-md-6">

                            <div class="row">
                                
                                <validacion 
                                    btn_text="Imp. Remuneraciones"
                                    method="post"
                                    token="{{ csrf_token() }}"
                                    url="{{ route('import.remuneracion', $cronograma->slug()) }}"
                                >
    
                                    <div class="form-group">
                                        <a href="{{ url('/formatos/remuneracion_import.xlsx') }}" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-file-excel"></i> Formato de importación
                                        </a>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="import_remuneracion" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-upload"></i> Subir Archivo de Excel
                                            <input type="file" name="import_remuneracion" id="import_remuneracion" hidden>
                                        </label>
                                        <small class="text-danger">{{ $errors->first('import_remuneracion') }}</small>
                                    </div>
    
                                </validacion>

                                <validacion 
                                    class="ml-1"
                                    btn_text="Imp. Descuentos"
                                    method="post"
                                    token="{{ csrf_token() }}"
                                    url="{{ route('import.descuento', $cronograma->slug()) }}"
                                >
    
                                    <div class="form-group">
                                        <a href="{{ url('/formatos/descuento_import.xlsx') }}" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-file-excel"></i> Formato de importación
                                        </a>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="import_descuento" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-upload"></i> Subir Archivo de Excel
                                            <input type="file" name="import_descuento" id="import_descuento" hidden>
                                        </label>
                                        <small class="text-danger">{{ $errors->first('import_descuento') }}</small>
                                    </div>
    
                                </validacion>

                                @if ($cronograma->pdf)
                                    <a target="__blank" title="PDF, resumen de todas las metas" 
                                        href="{{ url($cronograma->pdf) }}" 
                                        class="btn btn-sm btn-outline-danger ml-1"
                                    >
                                        <i class="far fa-file-pdf" aria-hidden="true"></i>
                                    </a>
                                @endif
    
                                @if ($cronograma->pdf_meta)
                                    <a target="__blank" title="PDF, resumen metas x metas" 
                                        href="{{ url($cronograma->pdf_meta) }}" 
                                        class="btn btn-sm btn-outline-danger ml-1"
                                    >
                                        <i class="far fa-file-pdf" aria-hidden="true"></i>
                                    </a>
                                @endif

                            </div>

                        </div>
                    </div>
                </form>


                <table class="table">
                    <thead class="text-primary">
                        <tr>
                            <th>#ID</th>
                            <th>Nombre Completo</th>
                            <th>N° Documento</th>
                            <th>Categorias</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jobs as $job)
                            <tr>
                                <th>{{ $job->id }}</th>
                                <th class="capitalize">{{ $job->nombre_completo }}</th>
                                <th>{{ $job->numero_de_documento }}</th>
                                <th class="uppercase">
                                    @foreach ($job->infos as $info)
                                        <div class="btn btn-sm btn-danger">
                                            {{ $info->categoria_id }} <small class="fas fa-arrow-right"></small>
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
                                        <btn-boleta theme="btn-dark btn-sm"
                                            param="{{ $job->id }}"
                                            url="{{ route('job.boleta.store', $job->id) }}"
                                            nombre_completo="{{ $job->nombre_completo }}"
                                            token="{{ csrf_token() }}"
                                        >
                                            <i class="fas fa-file-alt"></i>
                                        </btn-boleta>
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