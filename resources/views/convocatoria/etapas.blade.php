@extends('layouts.app')

@section('content')
    
<div class="row">

    <div class="col-md-12 mb-3">
        <a href="{{ route('convocatoria.index') }}" class="btn btn-danger"><i class="fas fa-arrow-left"></i> atrás</a>
    </div>

    <div class="col-md-12">
        
        <div class="card">
            <div class="card-header">
                Convocatoria N° {{ $convocatoria->numero_de_convocatoria }}-{{ $year }}-UNU
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach ($convocatoria->personals as $personal)
                        <div class="col-md-3">
                            <div class="btn btn-{{ isset($current->id) && $personal->id ==  $current->id ? 'primary' : 'outline-primary' }}">
                                <a href="{{ route('convocatoria.etapas', [$convocatoria->id, "personal={$personal->id}"]) }}"
                                    class="{{ isset($current->id) && $personal->id ==  $current->id ? 'text-white' : 'text-primary' }}"    
                                >
                                    {{ $personal->slug }}
                                </a>
                                <a href="{{ route('bolsa.resultados', [$convocatoria->id, $personal->id]) }}" 
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
                        <a href="{{ route('etapa.pdf', [$etapa->id, $convocatoria->id]) }}" target="__blank" class="btn btn-sm btn-outline-warning">
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
                                            @php 
                                                $puntaje = isset($postulante->etapas)
                                                ? $postulante->etapas->where('type_etapa_id', $etapa->id)->first()->puntaje
                                                : 0
                                            @endphp
                                            <div class="col-md-3">
                                                <input type="hidden" {!! $hasExpire ? 'disabled' : null !!} value="{{ $postulante->id }}" name="postulantes[{{ $iter }}][0]">
                                                <input type="number" {!! $hasExpire ? 'disabled' : null !!} value="{{ $puntaje }}" class="form-control" name="postulantes[{{ $iter }}][1]">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">

                                                @php
                                                    $checked = isset($postulante->etapas)
                                                        ? $postulante->etapas->where('type_etapa_id', $etapa->id)->first()->next
                                                        : 0
                                                @endphp

                                                @if ($hasExpire)
                                                    <button class="btn btn-sm btn-{{ $checked ? 'success' : 'outline-danger'}}" disabled>
                                                        @php
                                                            $ok = $etapa->fin ? 'Ganó' : 'Pasó';
                                                            $fail = $etapa->fin ? 'Pedió' : 'No Pasó'
                                                        @endphp
                                                        @if ($checked)
                                                            {{ $ok }}
                                                            <i class="fas fa-check ml-1"></i>
                                                        @else    
                                                            {{ $fail }}
                                                            <i class="fas fa-times ml-1"></i>
                                                        @endif

                                                    </button>
                                                @else
                                                    <label for="" class="btn btn-sm btn-{{ $checked ? 'primary' : 'outline-primary'}}">
                                                        <input type="checkbox" name="nexts[{{ $iter }}]" {!! $checked ? 'checked' : null !!}>
                                                        {{ $etapa->fin ? 'Ganador' : 'Continuar' }}
                                                    </label>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="text-center">
                                        <td colspan="4">
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