<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>AFP NET</title>
</head>
<body>
    
    <table>
        @foreach ($works as $iter => $work)
            <tr>
                <td>{{ $iter + 1 }}</td>
                <td>{{ $work->numero_de_cussp }}</td>
                <td>{{ $work->tipo_documento_id }}</td>
                <td>{{ $work->numero_de_documento }}</td>
                <td>{{ strtoupper($work->ape_paterno) }}</td>
                <td>{{ strtoupper($work->ape_materno) }}</td>
                <td>{{ strtoupper($work->nombres) }}</td>
                <td>S</td>
                <td>N</td>
                <td>N</td>
                <td></td>
                <td>{{ $work->tmp_afp }}</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>N</td>
                <td></td>
            </tr>
        @endforeach
    </table>

</body>
</html>