

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Descripción</th>
            <th>P.A.P</th>
            <th>Planilla</th>
            <th>Categorias</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($cargos as $cargo)
            <tr>
                <td>{{ $cargo->id }}</td>
                <td>{{ $cargo->descripcion }}</td>
                <td>{{ $cargo->tag }}</td>
                <td>{{ $cargo->planilla ? $cargo->planilla->descripcion : '' }}</td> 
                <td>
                    <table>
                        <thead>
                            <tr>
                                <th colspan="2">
                                    Categorias
                                </th>
                            </tr>
                        </thead>
                        @foreach ($cargo->categorias as $categoria)
                            <tbody>
                                <tr>
                                    <td>{{ $categoria->id }}</td>
                                    <td>{{ $categoria->nombre }}</td>
                                </tr>
                            </tbody>
                        @endforeach
                    </table>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>