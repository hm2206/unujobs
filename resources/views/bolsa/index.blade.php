@extends('layouts.bolsa')


@section('content-fluid')
    <div id="my-carousel" class="carousel slide bg-dark" data-ride="carousel">
        <ol class="carousel-indicators">
            <li class="active" data-target="#my-carousel" data-slide-to="0" aria-current="location"></li>
            <li data-target="#my-carousel" data-slide-to="1"></li>
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="d-block w-100" src="{{ asset('img/bolsa.png') }}" style="object-fit:contain" height="500px">
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="{{ asset('img/macbook.png') }}" style="object-fit:contain" height="500px" alt="">
            </div>
        </div>
        <a class="carousel-control-prev" href="#my-carousel" data-slide="prev" role="button">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#my-carousel" data-slide="next" role="button">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>

        <div class="bg-absolute">
            <div class="container">
                <form action="" method="get" class="f-row">
                    <select class="form-control ml-5" style="flex: 3" name="year">
                        @for ($i = date('Y'); $i > date('Y') - 10; $i--)
                            <option value="{{ $i }}" {!! request('year') == $i ? 'selected' : null !!}>Año de Convocatoria - {{ $i }}</option>
                        @endfor
                    </select>
                    <div  style="flex:1;" class="ml-3">
                        <button class="btn btn-info"><i class="fas fa-search"></i> Buscar</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection


@section('content')
    <h4 class="mt-4 mb-5">Convocatorias {{ $year }}</h4>

    
    <div id="accordianId" role="tablist" aria-multiselectable="true">
        @foreach ($convocatorias as $iter => $convocatoria)
            <div class="card mb-3">
                <div class="card-header" role="tab" id="section{{ $iter }}HeaderId">
                    <h5 class="mb-0">
                        <a  aria-expanded="true" aria-controls="section{{ $iter }}ContentId"
                            data-toggle="collapse" data-parent="#accordianId" href="#section{{ $iter }}ContentId" 
                        >

                            @php
                                $tmp_year = isset(explode("-",$convocatoria->fecha_inicio)[0]) ? explode("-",$convocatoria->fecha_inicio)[0] : $year;
                            @endphp

                            Convocatoria N° {{ $convocatoria->numero_de_convocatoria }}-{{ $tmp_year }}-UNU 
                            <a target="__blank" href="{{ route('convocatoria.pdf', $convocatoria->slug()) }}" class="ml-3 btn btn-sm btn-danger">
                                <i class="fas fa-file-pdf"></i> PDF
                            </a>
                        </a>
                    </h5>
                </div>
                <div id="section{{ $iter }}ContentId" class="collapse in {{ $iter == 0 ? 'show': '' }}" role="tabpanel" aria-labelledby="section{{ $iter }}HeaderId">
                    <div class="card-body">
                        <ol>
                            @foreach ($convocatoria->personals as $personal)
                                <li>
                                    <a href="{{ route('bolsa.show', 
                                        [
                                            $convocatoria->slug(), 
                                            $personal->slug, 
                                            'postulante=' . request()->postulante
                                        ]) }}"
                                    >
                                        {{ $personal->cargo_txt }}
                                    </a>
                                    <ul>
                                        <li type="square">Lugar: <b>{{ $personal->lugar_txt }}</b></li>
                                        <li type="square">Cantidad de Personal: <b>{{ $personal->cantidad }}</b></li>
                                        <li type="square">Honorarios: <b>{{ $personal->honorarios }}</b></li>
                                    </ul>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{ $convocatorias->links() }}

@endsection