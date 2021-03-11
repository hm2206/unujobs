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
        @foreach ($historial as $iter => $history)
            <tr>
                <td>{{ $iter + 1 }}</td>
                <td>{{ $history->numero_de_cussp }}</td>
                <td>{{ $history->tipo_documento_id }}</td>
                <td>{{ $history->work ? $history->work->numero_de_documento : '' }}</td>
                <td>{{ $history->work ? $history->work->ape_paterno : '' }}</td>
                <td>{{ $history->work ? $history->work->ape_materno : '' }}</td>
                <td>{{ $history->work ? $history->work->nombres : '' }}</td>
                <td>S</td>
                <td>N</td>
                <td>N</td>
                <td></td>
                <td>{{ $history->base }}</td>
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