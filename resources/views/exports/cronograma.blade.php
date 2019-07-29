

<table>
    <thead>
        <tr>
            <th>Mes</th>
            <td>{{ $cronograma->mes }}</td>
            <th>Año</th>
            <td>{{ $cronograma->año }}</td>
            <th>Planilla</th>
            <td>{{ $cronograma->planilla ? $cronograma->planilla->descripcion : '' }}</td>
        </tr>
    </thead>
</table>


@foreach ($works as $work)
    <table>
        <thead>
            <tr>
                <th>Nombre Completo</th>
                <th>{{ $work->nombre_completo }}</th>
            </tr>
        </thead>
    </table>
        
    <table>
        <tbody>
            @foreach ($work->infos as $info)
                <tr>
                    <td>
                        <table>
                            <thead>
                                <tr>
                                    <th colspan="6">
                                        Remuneraciones >> 
                                        {{ $info->cargo ? $info->cargo->descripcion : '' }} >> 
                                        {{ $info->categoria ? $info->categoria->nombre : '' }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($info->remuneraciones->chunk(6) as $body)
                                    <tr>
                                        @foreach ($body as $remuneracion)
                                            <td colspan="2">{{ $remuneracion->typeRemuneracion ? $remuneracion->typeRemuneracion->descripcion : '' }}</td>
                                            <td>{{ $remuneracion->monto }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <br> <br>
    @endforeach