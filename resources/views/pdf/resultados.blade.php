<!DOCTYPE html>
<html lang="es_Es">
<head>
    @php
        $config = App\Models\Config::first();
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Resultados de la convocatoria {{ $convocatoria->numero_de_convocatoria }}-{{ $year }}-{{ $config->alias }}</title>
</head>

<style>

    * {
        font-family: Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif; 
        font-size: 11px;
    }
    
    p {
        margin-bottom: 0.4em;
        line-height: 18px;
    }

    .text-center {
        text-align: center;
    }

    .container {
        padding: 2px 4em;
    }

    .pr-5 {
        padding-right: 5em;
    }
    
    li {
        margin-bottom: 1em;
        margin-top: 1em;
    }

    .table {
        width: 100%;
        border: 1px solid #000;
        border-collapse: collapse;
    }

    .table th {
        border: 1px solid #000;
        padding: 1em;
    }

    .table td {
        border: 1px solid #000; 
        padding: 0.7em;
    }

    .upper {
        text-transform: uppercase;
    }

    .capitalize {
        text-transform: capitalize;
    }

</style>

<body>

    <div class="container">
        
        <table width="100%">
            <tr>
                <th width="60px;">
                    <img width="50px;" src="{{ public_path(). $config->logo }}" alt="">
                </th>
                <th>
                    <h1 style="font-size: 1.7em;margin-left: 3.3em;" class="upper">{{ $config->nombre }}</h1>
                </th>
            </tr>
            <tr>
                <th colspan="2" class="text-center upper">
                    PROFESIONAL PARA {{ $current->deberes }} PARA {{ $current->dependencia_txt }}
                </th>
            </tr>
        </table>
            
        
        <h2 class="text-center upper" style="font-size: 1.2em;">
                ADMINISTRATIVAS – CAS N° {{ $convocatoria->numero_de_convocatoria }}
        </h2>

        <br>

        <table class="table text-center">
            @foreach ($etapas as $etapa)
                <tr>
                    <th colspan="3">Etapa {{ $etapa->descripcion }}</th>
                </tr>
                <tr>
                    <th>Postulante</th>
                    <th>Puntaje</th>
                    <th>Observacion</th>
                </tr>
                @foreach ($etapa->postulantes as $postulante)
                    @php
                        $tmp_etapa = isset($postulante->etapas) ? $postulante->etapas->first() : null; 
                    @endphp
                    
                    @if ($tmp_etapa)
                        <tr>
                            <td>{{ $postulante->nombre_completo }}</td>
                            <td>{{ $tmp_etapa->puntaje }}</td>
                            <td>
                                @php
                                    $ok = $etapa->fin ? "Ganó" : "Pasa, apto";
                                    $fail = $etapa->fin ? "Perdió" : "No Pasa"; 
                                @endphp

                                @if ($tmp_etapa->next)
                                    {{ $ok }}
                                @else
                                    {{ $fail }}
                                @endif
                            </td>
                        </tr>
                    @endif

                @endforeach
            @endforeach
        </table>

        <br>

        <b>ESTADO: {{ $hasExpire ? "CONCLUIDO" : "EN CONCURSO"}}</b>
        
    </div>
    
</body>
</html>