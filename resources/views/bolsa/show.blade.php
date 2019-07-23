@extends('layouts.bolsa')

@section('content')
    
    <h3 class="mt-5 mb-3">{{ $personal->cargo_txt }}</h3>

    <div class="row">
        <div class="col-md-12 mb-3">
            <a href="{{ route('bolsa.index', 'postulante=' . request()->postulante) }}" class="btn btn-danger">
                <i class="fas fa-arrow-left"></i> atrás
            </a>
            <a href="{{ route('personal.pdf', $personal->id) }}" class="btn btn-dark" target="__blank">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
        </div>

        <div class="col-md-9">

            @if ($auth)
                @if ($current)
                    <div class="card mb-3">
                        <div class="card-header">
                            Postulante <i class="fas fa-arrow-right text-danger"></i> <b class="capitalize">{{ $postulante->nombre_completo }}</b>
                            @if ($postulante->cv)
                                <a href="{{ url($postulante->cv) }}" target="__blank" class="ml-5 btn btn-sm btn-outline-danger">
                                    <i class="fas fa-file-pdf"></i> Mi CV
                                </a>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="row justify-content-around">
                                @foreach ($types as $count => $type)
                                    <div class="col-md-4">
                                        <div class="row justify-content-center">
                                            @if (isset($current->id) && $type->id == $current->id)
                                                <div class="col-md-12 text-center">
                                                    <button class="btn btn-circle btn-lg btn-success">
                                                        <i class="{{ $type->icono }}"></i>
                                                    </button>
                                                </div>
                                                <i class="col-md-12 text-center"><b>{{ $type->descripcion }}</b></i>
                                            @else
                                                <div class="col-md-12 text-center">
                                                    <button class="btn btn-circle btn-lg btn-warning" disabled>
                                                        <i class="{{ $type->icono }}"></i>
                                                    </button>
                                                </div>
                                                <i class="col-md-12 text-center disabled">{{ $type->descripcion }}</i>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="card-footer">
                            <b>Estado:</b>
                            @if ($isExpire)
                                <span class="btn btn-sm btn-danger">
                                    Terminado <i class="fas fa-times"></i>
                                </span>
                            @else
                                <span class="btn btn-sm btn-success">
                                    En Curso <i class="fas fa-clock"></i>
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            @endif

            <div class="card">
                <div class="card-header">
                    <span class="btn btn-sm btn-info"><i class="fas fa-info"></i> Información</span> 
                </div>
                <div class="card-body">
                    <ul>
                        <li>
                            <b>Cargo: </b> {{ $personal->cargo_txt }}
                        </li>
                        <li>
                           <b>Objetivo:</b> 
                            Contratar los servicios de {{ $personal->cargo_txt }}, para {{ $personal->deberes }}
                        </li>
                        <li>
                            <b>Ciudad:</b> {{ $personal->sede ? $personal->sede->descripcion : null }}
                        </li>
                        <li>
                            <b>Lugar:</b> {{ $personal->lugar_txt }}
                        </li>
                        <li>
                            <b>Unidad Supervisora:</b> <span class="text-primary">{{ $personal->supervisora_txt }}</span>
                        </li>
                        <li>
                            <b>Fecha de Inicio:</b> <span class="btn btn-sm btn-dark"><small>{{ $personal->fecha_inicio }}</small></span>
                        </li>
                        <li>
                            <b>Fecha Final:</b> <span class="btn btn-sm btn-dark mt-1"><small>{{ $personal->fecha_final }}</small></span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <span class="btn btn-sm btn-dark">
                        <i class="fas fa-user"></i> Perfil del Puesto
                    </span> 
                </div>
                <div class="card-body">
                    <div class="responsive-table">
                        <table class="table table-striped">
                            <thead>
                                <tr class="text-center">
                                    <th>Requisitos</th>
                                    <th>Detalle</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($personal->questions as $question)
                                    <tr>
                                        <th class="text-center">{{ $question->requisito }}</th>
                                        <td>
                                            <ul>
                                                @php
                                                    $bodies = is_array(json_decode($question->body)) ? json_decode($question->body) : [];
                                                @endphp
                                                @foreach ($bodies as $body)
                                                    <li>{{ $body }}</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <span class="btn btn-sm btn-warning">
                        <i class="fas fa-clock"></i> Cronogramas de Actividades
                    </span>
                </div>
                <div class="card-body">
                    <div class="responsive-table">
                        <table class="table table-striped">
                            <thead>
                                <tr class="text-center">
                                    <th>Actividad</th>
                                    <th>Fecha</th>
                                    <th>Responsable</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach ($convocatoria->actividades as $actividad)
                                    <tr class="{{ $actividad->fecha_final < date('Y-m-d') ? 'bg-danger text-white' : null }}">
                                        <th>{{ $actividad->descripcion }}</td>
                                        <td>
                                            <span class="btn btn-sm btn-dark">
                                                <small>{{ $actividad->fecha_inicio }}</small>
                                            </span>

                                            @if ($actividad->fecha_final)
                                                    <i class="fas fa-arrow-right text-danger"></i>
                                                    <span class="btn btn-sm btn-dark">
                                                        <small>{{ $actividad->fecha_final }}</small>
                                                    </span>
                                            @endif
                                        </td>
                                        <th>{{ $actividad->responsable }}</th>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-md-3">

            @if ($auth && $current)
                <div class="card">
                    <div class="card-header">
                        Cerrar Cuenta
                    </div>
                    <div class="card-body">
                        <a href="{{ route('bolsa.index') }}" class="btn btn-danger btn-block">
                            Salir
                        </a>
                    </div>
                </div>

                @if (session('cv'))
                    <div class="alert alert-success mt-3">
                        {{ session('cv') }}
                    </div>
                @endif

                <div class="card mt-3">
                    <div class="card-header">
                        Subir o Actualizar CV
                    </div>
                    <form class="card-body" method="POST" action="{{ route('postulante.cv', $postulante->id) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="cv" class="btn btn-block btn-outline-danger">
                                <i class="fas fa-file-pdf"></i> PDF
                                <input type="file" name="cv" hidden id="cv">
                            </label>
                            <b class="text-danger"><small>{{ $errors->first('cv') }}</small></b>
                        </div>
                        <button class="btn btn-block btn-primary">
                            <i class="fas fa-upload"></i> Subir
                        </button>
                    </form>
                </div>
            @else
                <div class="card">
                    <div class="card-header">
                        Postula aquí
                    </div>
                    <div class="card-body">
                        @if ($isExpire)
                            <button class="btn btn-primary btn-block" disabled>
                                Postular
                            </button>
                        @else
                            <a href="{{ route('bolsa.postular', [$convocatoria->id, $personal->slug]) }}" class="btn btn-primary btn-block">Postular</a>
                        @endif
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        Entrar para ver mi postulación
                    </div>
                    <form class="card-body" method="POST" action="{{ route('bolsa.auth') }}">
                        @csrf

                        @if (session('auth'))
                            <div class="alert alert-warning">
                                {{ session('auth') }}
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="">Ingrese su documento</label>
                            <input type="text" name="numero_de_documento" value="{{ old('numero_de_documento') }}" 
                                placeholder="Número" class="form-control"
                            >
                            <small class="text-danger">{{ $errors->first('numero_de_documento') }}</small>
                            <input type="hidden" name="redirect" 
                                value="{{ route('bolsa.show', [$convocatoria->id, $personal->slug]) }}"
                            >
                            <input type="hidden" name="personal_id" value="{{ $personal->id }}">
                        </div>
                        <div class="text-right">
                            <button class="btn btn-success">
                                Entrar <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            @if ($mas->count() > 0)
                <div class="card mt-3">
                    <div class="card-header">
                        Más Ofertas
                    </div>
                    <div class="card-body">
                        <ul>
                            @foreach ($mas as $per)
                                <li>
                                    <a href="{{ route('bolsa.show', [$convocatoria->numero_de_convocatoria, $per->slug]) }}">
                                        {{ $per->cargo_txt }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        
        </div>

    </div>

@endsection