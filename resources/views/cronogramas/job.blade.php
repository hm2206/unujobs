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
    <btn-reporte theme="btn-primary"
        :cronograma="{{ $cronograma }}"
        :type_reports="{{ $typeReports }}"
    >
        <i class="fas fa-file-pdf"></i> Reportes
    </btn-reporte>

</div>

<div class="row">
    <h3 class="ml-2 col-md-10 uppercase">
        @if ($cronograma->adicional)
            Adicional 
            <span class="btn btn-sm btn-primary">{{ $cronograma->numero }}</span>
            <span class="text-danger"> >> </span>
        @endif
        {{ $cronograma->planilla ? $cronograma->planilla->descripcion : null }} 
        del {{ $cronograma->mes }} - {{ $cronograma->año }}
    </h3>
    <h2 class="text-right">
        <i class="fas fa-users fa-sm text-primary"></i>
        {{ $infos->total() }}
    </h2>
</div>

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



<div class="col-md-12">

    @if ($infos)
        {{ $infos->links() }}
    @endif

    <div class="card">
        <div class="card-header card-header-danger">
            <h4 class="card-title">Lista de Trabajadores</h4>
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

                                <import id="import-remuneracion"
                                    class="ml-1"
                                    formato="{{ url('/formatos/remuneracion_import.xlsx') }}"
                                    url="/remuneracion/{{ $cronograma->slug() }}"
                                    formulario="form-import-remuneracion"
                                    param="{{ auth()->user()->id }}"
                                >
                                    <i class="fas fa-file-excel"></i> Imp. Remuneraciones
                                </import>

                                <import id="import-descuento"
                                    class="ml-1"
                                    formato="{{ url('/formatos/descuento_import.xlsx') }}"
                                    url="/descuento/{{ $cronograma->slug() }}"
                                    formulario="form-import-descuento"
                                    param="{{ auth()->user()->id }}"
                                >
                                    <i class="fas fa-file-excel"></i> Imp. Descuentos
                                </import>

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
                        @forelse ($infos as $info)
                            @php
                                $job = $info->work;
                            @endphp
                            <tr>
                                <th>{{ $info->id }}</th>
                                <th class="capitalize">{{ $job->nombre_completo }}</th>
                                <th>{{ $job->numero_de_documento }}</th>
                                <th class="uppercase">
                                    <div class="btn btn-sm btn-danger">
                                        {{ $info->categoria ? $info->categoria->nombre : '' }}
                                    </div>
                                </th>
                                <th>
                                    <div class="btn-group">

                                        <btn-boleta theme="btn-danger btn-sm"
                                            param="{{ $job->id }}"
                                            url="{{ route('job.boleta.store', $job->id) }}"
                                            nombre_completo="{{ $job->nombre_completo }}"
                                            token="{{ csrf_token() }}"
                                        >
                                            <i class="fas fa-file-alt"></i>
                                        </btn-boleta>

                                        <btn-detalle theme="btn-warning btn-sm"
                                            param="{{ $info->id }}"
                                            nombre_completo="{{ $job->nombre_completo }}"
                                            categoria="{{ $info->categoria_id }}"
                                            :paginate="{{ $indices }}"
                                            planilla_id="{{ $cronograma->planilla_id }}"
                                            tmp_adicional="{{ $cronograma->adicional }}"
                                            tmp_numero="{{ $cronograma->numero }}"
                                            :tmp_info="{{ $info }}"
                                            :cronograma="{{ $cronograma }}"
                                        >
                                            <i class="fas fa-wallet"></i>
                                        </btn-detalle>

                                        <btn-work-config theme="btn-dark btn-sm"
                                                param="{{ $job->id }}"
                                                nombre_completo="{{ $job->nombre_completo }}"
                                                :sindicatos="{{ $job->sindicatos }}"
                                            >
                                                <i class="fas fa-cog"></i>
                                        </btn-work-config>
                                        
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