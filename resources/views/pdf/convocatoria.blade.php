<!DOCTYPE html>
<html lang="es_Es">
<head>
    @php
        $config = App\Models\Config::first();
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Convocatoria N° {{ $convocatoria->numero_de_convocatoria }}-{{ $year }}-{{ $config->alias }}</title>
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

    .ml-3 {
        margin-left: 3em;
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
        <p class="text-center">
           <b class="upper">
                CONVOCATORIA N° {{ $convocatoria->numero_de_convocatoria }} <br><br>
                CONVOCATORIA PÚBLICA DE PERSONAL BAJO EL REGIMEN ESPECIAL DE CONTRATACION ADMINISTRATIVA DE SERVICIOS (CAS)
           </b>
        </p>

        <br>

        <ul>
            <li type="disc"><b>PERFIL DE PLAZAS VACANTES</b>
                <ul style="padding-left: 0px;">
                    @foreach ($convocatoria->personals as $personal)
                        <div>
                            <li type="disc" class="ml-3">
                                <b>
                                    {{ $personal->cargo_txt }} para laborar en {{ $personal->dependencia_txt }} en la ciudad de <span class="capitalize">{{ $personal->sede->descripcion }}</span>
                                </b>
                            </li>
                            <li type="disc" class="ml-3">
                                <b>
                                    Perfil para {{ $personal->cantidad > 1 ? $personal->cantidad : "un (01)" }} personal de {{ $personal->cargo_txt }}
                                </b>
                            </li>
                            <table class="table">
                                <tr class="text-center">
                                    <th>REQUISITOS</th>
                                    <th>DETALLE</th>
                                </tr>
                                @foreach ($personal->questions as $question)
                                    <tr>
                                        <td class="text-center"> 
                                            {{ $question->requisito }}
                                        </td>
                                        <td>
                                            @php
                                                $body = json_decode($question->body);
                                            @endphp
                                            <ul>
                                                @foreach ($body as $content)
                                                    <li type="disc">
                                                        {{ $content }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td class="text-center">Periodo de Contrato</td>
                                    <td>
                                        <p style="margin-left: 0.5em;">{{ $personal->fecha_inicio }} hasta el {{ $personal->fecha_final }}, sujeto a renovación.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center">Sueldo Mensual</td>
                                    <td>
                                       <p style="margin-left: 0.5em;"> {{ $personal->honorarios }} </p> 
                                    </td>
                                </tr>
                            </table> 
                        </div>
                    @endforeach
                </ul>
            </li>

            <li>
                <b>CRONOGRAMA</b>
                <br><br>

                <table class="table">
                    <tr class="text-center">
                        <td>N°</td>
                        <td>ACTIVIDAD</td>
                        <td>FECHA</td>
                        <td>RESPONSABLE</td>
                    </tr>
                    @foreach ($convocatoria->actividades as $iter => $actividad)
                        <tr>
                            <td class="text-center">{{ ($iter + 1) < 10 ? "0" . ($iter + 1) : ($iter + 1) }}</td>
                            <td>{{ $actividad->descripcion }}</td>
                            <td class="text-center">{{ $actividad->fecha_inicio }} {{ $actividad->fecha_final ? "al {$actividad->fecha_final}" : null }}</td>
                            <td class="text-center">{{ $actividad->responsable }}</td>
                        </tr>
                    @endforeach
                </table>
                <div style="text-align:right; margin-bottom: 1em;">
                    <b>LA COMISION</b>
                </div>

                Nota: <br>
                {{ $convocatoria->observacion }}
            </li>
        </ul>
            
    </div>
    
</body>
</html>