<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Alta y Bajas {{ $inicio }} al {{ $final }}</title>
</head>
<body>
    

    <table>
        <tr>
            <td>
                Altas de Personal
            </td>
        </tr>
        <tr>
            <td>N° de documento</td>
            <td>Nombre Completo</td>
        </tr>
        @foreach ($altas as $alta)   
            <tr>
                <td>{{ $alta->numero_de_documento }}</td>
                <td>{{ $alta->nombre_completo }}</td>
            </tr>
        @endforeach
        <tr>
            <td>
                Total de altas: {{ $altas->count() }}
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <td>
                Bajas de Personal
            </td>
        </tr>
        <tr>
            <td>N° de documento</td>
            <td>Nombre Completo</td>
        </tr>
        @foreach ($bajas as $baja)   
            <tr>
                <td>{{ $baja->work ? $baja->numero_de_documento : '' }}</td>
                <td>{{ $alta->work ? $baja->nombre_completo : '' }}</td>
            </tr>
        @endforeach
        <tr>
            <td>Total de bajas: {{ $altas->count() }}</td>
        </tr>
    </table>

</body>
</html>