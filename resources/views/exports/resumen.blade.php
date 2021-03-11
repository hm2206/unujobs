<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Resumen</title>
</head>
<body>

    <table>
        <tr>
            <td>N° de Documento</td>
            <td>Nombre Completo</td>
            <td>Monto Bruto</td>
        </tr>
        @foreach ($works as $work)
            <tr>
                <td>{{ $work->numero_de_documento }}</td>
                <td>{{ $work->nombre_completo }}</td>
                <td>{{ $work->monto_bruto }}</td>
            </tr>
        @endforeach
    </table>
    
</body>
</html>