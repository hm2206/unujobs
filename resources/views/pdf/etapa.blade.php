<!DOCTYPE html>
<html lang="es_Es">
<head>
    @php
        $config = App\Models\Config::first();
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Etapa - {{ $etapa->descripcion }}</title>
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
                    <h1 style="font-size: 1.7em;margin-left: 3.3em;">{{ $config->nombre }}</h1>
                </th>
            </tr>
            <tr>
                <th colspan="2" class="text-center">
                    COMISION EVALUADORA DEL CONCURSO PUBLICO EXTERNO <br> CAS N° 004-2019
                </th>
            </tr>
        </table>
            
        
        <h2 class="text-center upper" style="font-size: 1.2em;">
                REPORTE DE LA ETAPA DE {{ $etapa->descripcion }}
                <br> 
                ADMINISTRATIVAS – CAS N° {{ $convocatoria->numero_de_convocatoria }}-{{ $year }}-{{ $config->alias }}
        </h2>

        <br>

        <table class="table">
            @foreach ($personals as $personal)     
                <tr>
                    <th class="text-center upper" colspan="3">
                        PROFESIONAL PARA {{ $personal->deberes }} PARA {{ $personal->dependencia_txt }}
                    </th>
                </tr>
                <tr>
                    <th class="text-center">
                        POSTULANTE
                    </th>
                    <th class="text-center">
                        PUNTAJE
                    </th>
                    <th class="text-center">
                        OBSERVACIONES
                    </th>
                </tr>
                @foreach ($personal->postulantes as $postulante)

                    @php
                        $tmp_etapa = isset($postulante->etapas) ? $postulante->etapas->first() : null;
                    @endphp

                    <tr class="text-center">
                        <td class="capitalize">{{ $postulante->nombre_completo }}</td>
                        <td>{{ $tmp_etapa ? $tmp_etapa->puntaje : 0 }}</td>
                        <td>
                            @if ($tmp_etapa)
                                {{ $tmp_etapa->next ? "Apto, pasa a la siguiente etapa" : "No Apto, no pasa" }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </table>
        
    </div>
    
</body>
</html>