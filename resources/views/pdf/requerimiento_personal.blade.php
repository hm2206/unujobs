<!DOCTYPE html>
<html lang="es_Es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Termino de Referencia - {{ $personal->numero_de_requerimiento }}</title>
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
    }

    .upper {
        text-transform: uppercase;
    }

</style>

<body>

    <div class="container">
        <p class="text-center">
           <b class="upper">
                TERMINO DE REFERENCIA <br><br>
                CONVOCATORIA PARA LA CONTRATACIÓN ADMINISTRATIVA DE SERVICIOS DE {{ $personal->cantidad > 1 ? $personal->cantidad : "UN (01)" }} 
                {{ $personal->cargo_txt  }} PARA LA {{ $personal->dependencia_txt }}
           </b>
        </p>

        <br><br>
            
        <p><b>I.  GENERALIDADES</b></p>
            
        <ol>
            <li>
                <b>Objeto de la Convocatoria</b>
                <p class="pr-5">
                    Contratar los servicios de {{ $personal->cargo_txt }} para {{ $personal->dependencia_txt }}, 
                    para: {{ $personal->deberes }}
                </p>
            </li>
            <li>
                <b>Dependencia, Unidad Orgánica y/o Área Solicitante</b>
                <p>{{ $personal->dependencia_txt }}</p>
            </li>
            <li>
                <b>Base Legal </b>
                <ul>
                    @foreach ($bases as $base)
                        <li type="disc">
                            {{ $base }}
                        </li>
                    @endforeach
                </ul>
            </li>
        </ol>

        <p><b>II.  PERFIL DEL PUESTO</b></p>

        <br>

        <ul>
            <li type="square">
                <b class="upper">
                    PERFIL PARA EL PUESTO DE  {{ $personal->cargo_txt }} PARA LABORAR EN 
                    {{ $personal->dependencia_txt }} DE LA UNIVERSIDAD NACIONAL DE UCAYALI   
                    ({{ $personal->cantidad }} PERSONAL CAS).  
                </b>
            </li>
        </ul>

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
                                <li>
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
    
</body>
</html>