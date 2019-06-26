@extends('layouts.app')

@section('titulo')
    - Registro de Requerimiento de Personal
@endsection


@section('link')
    Recursos Humanos
@endsection

@section('content')

<div class="col-md-12">
    <a href="{{ route('job.show', $job->id) }}" class="btn btn-warning"><i class="fas fa-arrow-left"></i> atrás</a>
</div>


<div class="col-md-12">
    <div class="card">
        <h5 class="card-header">
            <i class="fas fa-filter"></i> Filtro:
        </h5>
        <hr>
        <form class="card-body" method="GET">
            <div class="row">

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="" class="form-control-label">Mes</label>
                        <input type="number" name="mes" class="form-control" min="1" max="12" value="{{ $mes }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="" class="form-control-label">Año</label>
                        <input type="number" name="year" class="form-control" min="{{ date('Y') - 2 }}" max="{{ date('Y') }}" value="{{ $year }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="" class="form-control-label">Adicional</label> <br>
                        <input type="checkbox" name="adicional" {!! request()->input('adicional') ? 'checked' : null !!}>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <button class="btn btn-info">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>


<div class="col-md-12">

    <div class="card">
        <h4 class="card-header">
            Remuneración 
            <span class="text-danger"> >> </span> 
            <span class="uppercase">{{ $job->nombre_completo }}</span>
        </h4>
        <hr>
        <div class="card-body">

            <div class="mb-4">
                Categoria: <span class="text-primary">{{ $categoria->nombre }}</span> <br>
                <div class="row align-items-center">
                    <div class="col-md-1"> Dias: </div>
                    <input class="form-control col-md-3" type="number" value="30">
                </div>
            </div>

            <hr>

            <div class="row mt-4">
                @forelse ($remuneraciones as $remuneracion)
                    <div class="col-md-4">
                        <div class="row align-items-center">
                            <h6 class="col mb-0 text-primary uppercase text-right">
                                {{ $remuneracion->concepto ? $remuneracion->concepto->descripcion . " : " : "" }}
                            </h6>
                            S./ 
                            <div class="col-md-4">
                                <input type="number" class="form-control" value="{{ $remuneracion->monto }}">
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-md-12">
                        <div class="text-center text-danger">
                            No hay registros disponibles
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <hr>

        <div class="card-footer">
            <hr>
            <h4 class="text-left">
                Total:  <b class="text-primary">S./ {{ $total }}</b>
            </h4>
        </div>
    </div>

</div>
    

@endsection