

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Descripción</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($categorias as $categoria)
            <tr>
                <td>{{ $categoria->id }}</td>
                <td>{{ $categoria->nombre }}</td>
                <td>
                    <table>
                        <thead>
                            <tr>
                                <th colspan="2">Conceptos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categoria->conceptos as $concepto)
                                <tr>
                                    <td>ID: {{ $concepto->id }}</td>
                                    <td>Descripción: {{ $concepto->descripcion }}</td>
                                    <td>Monto: {{ $concepto->pivot ? $concepto->pivot->monto : 0 }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>