@extends('layouts.app')

@section('content')
    
<div class="row">

    <div class="col-md-12 mb-3">
        <a href="{{ route('convocatoria.index') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
    </div>

    <div class="col-md-12">

        @if (session('danger'))
            <div class="alert alert-danger">
                {{ session('danger') }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->first('personal_id'))
            <div class="alert alert-danger">
               {{ $errors->first('personal_id') }}
            </div>
        @endif
        
        <div class="card">
            <div class="card-header">
                Convocatoria N° {{ $convocatoria->numero_de_convocatoria }}
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach ($convocatoria->personals as $personal)
                        <div class="col-md-3">
                            <div class="btn btn-{{ isset($current->id) && $personal->id ==  $current->id ? 'primary' : 'outline-primary' }}">
                                <a href="{{ route('convocatoria.etapas', [$convocatoria->slug(), 'personal=' . $personal->slug]) }}"
                                    class="{{ isset($current->id) && $personal->id ==  $current->id ? 'text-white' : 'text-primary' }}"    
                                >
                                    {{ $personal->cargo_txt }}
                                </a>
                                <a href="{{ route('bolsa.resultados', [$convocatoria->slug(), $personal->slug]) }}" 
                                    class="btn btn-sm btn-circle btn-danger"
                                    target="__blank"    
                                >
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>


        @foreach ($etapas as $etapa)
            <form class="card mt-3" method="POST" action="{{ route('etapa.store') }}">
                @csrf
                <div class="card-header bg-dark text-white">
                    Etapa <i class="fas fa-arrow-right text-warning"></i> {{ $etapa->descripcion }} 
                    @if ($etapa->postulantes->count() > 0)
                        <a href="{{ route('etapa.pdf', [$etapa->slug(), $convocatoria->slug()]) }}" target="__blank" class="btn btn-sm btn-outline-warning">
                            <i class="fas fa-file-pdf"></i> ver reporte
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="responsive-table">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#ID</th>
                                    <th>Postulante</th>
                                    <th>CV</th>
                                    <th>Puntaje</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($etapa->postulantes as $iter => $postulante)
                                    <tr>
                                        <td>
                                            {{ $postulante->id }}
                                        </td>
                                        <td>
                                            {{ $postulante->nombre_completo }}
                                        </td>
                                        <td>
                                            @if ($postulante->cv)
                                                <a href="{{ url($postulante->cv) }}" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="col-md-3">
                                                <input type="hidden" {!! $hasExpire ? 'disabled' : null !!} value="{{ $postulante->id }}" name="postulantes[{{ $iter }}][0]">
                                                <input type="number" {!! $hasExpire ? 'disabled' : null !!} value="{{ $postulante->puntaje }}" class="form-control" name="postulantes[{{ $iter }}][1]">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                @if ($hasExpire)
                                                    <button class="btn btn-sm btn-{{ $postulante->next ? 'success' : 'outline-danger'}}" disabled>
                                                        @php
                                                            $ok = $etapa->fin ? 'Ganó' : 'Pasó';
                                                            $fail = $etapa->fin ? 'Pedió' : 'No Pasó'
                                                        @endphp
                                                        @if ($postulante->next)
                                                            {{ $ok }}
                                                            <i class="fas fa-check ml-1"></i>
                                                        @else    
                                                            {{ $fail }}
                                                            <i class="fas fa-times ml-1"></i>
                                                        @endif

                                                    </button>
                                                @else
                                                    <label for="" class="btn btn-sm btn-{{ $postulante->next ? 'primary' : 'outline-primary'}}">
                                                        <input type="checkbox" name="postulantes[{{ $iter }}][2]" {!! $postulante->next ? 'checked' : null !!}>
                                                        {{ $etapa->fin ? 'Ganador' : 'Continuar' }}
                                                    </label>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="text-center">
                                        <td colspan="5">
                                            No hay registros
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                @if ($etapa->postulantes->count() > 0)
                    @if (!$hasExpire)
                        <div class="card-footer">
                            <button class="btn btn-success">
                                <i class="fas fa-save"></i> Guardar
                            </button>

                            <validacion 
                                btn_text="Importar"
                                theme="btn-outline-success btn-md"
                                method="post"
                                token="{{ csrf_token() }}"
                                url="{{ route('import.etapa', $etapa->id) }}"
                                id="etapa-import-{{ $etapa->id }}"
                            >
        
                                <input type="hidden" name="personal_id" value="{{ $current->id }}">

                                <div class="form-group">
                                    <a href="{{ url('/formatos/etapa_import.xlsx') }}" class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-file-excel"></i> Formato de importación
                                    </a>
                                </div>
                                        
                                <div class="form-group">
                                    <label for="etapa-{{ $etapa->id }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-upload"></i> Subir Archivo de Excel
                                        <input type="file" name="import" hidden id="etapa-{{ $etapa->id }}">
                                    </label>
                                    <small class="text-danger">{{ $errors->first('import') }}</small>
                                </div>
        
                            </validacion>
                        </div>
                    @endif
                @endif

                <input type="hidden" value="{{ $etapa->id }}" name="type_etapa_id">
                <input type="hidden" name="personal_id" value="{{ isset($current->id) ? $current->id : '' }}">
                <input type="hidden" name="convocatoria_id" value="{{ $convocatoria->id }}">

            </form>
        @endforeach

    </div>
</div>

@endsection