<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>información para el portal de transparencia</title>
</head>
<body>
    

    <table>
        <thead>
            <tr>
                <td>Bajas de Personal</td>
                <td>Rango salarial</td>
                <td>Bonificaciones</td>
                <td>Cantidad de Personal</td>
                <td>Mes</td>
                <td>Año</td>
            </tr>
            <tr>
                <td>{{ $count_bajas }}</td>
                <td>{{ $salario_min }} - {{ $salario_max }}</td>
                <td>{{ $bonificaciones }}</td>
                <td>{{ $count_personal }}</td>
                <td>{{ $mes }}</td>
                <td>{{ $year }}</td>
            </tr>
        </thead>
    </table>


</body>
</html>