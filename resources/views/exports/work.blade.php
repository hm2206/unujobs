
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Apellido Paterno</th>
                <th>Apellido Materno</th>
                <th>Nombres</th>
                <th>Nombre Completo</th>
                <th>Correo</th>
                <th>Fecha de Nacimiento</th>
                <th>Fecha de Ingreso</th>
                <th>Dirección</th>
                <th>Tipo Documento</th>
                <th>N° Documento</th>
                <th>Teléfono</th>
                <th>Sexo</th>
                <th>Profesión</th>
                <th>Plaza</th>
                <th>N° Essalud</th>
                <th>Banco</th>
                <th>N° Cuenta</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($works as $work)
                <tr>
                    <td>{{ $work->id }}</td>
                    <td>{{ $work->ape_paterno }}</td>
                    <td>{{ $work->ape_materno }}</td>
                    <td>{{ $work->nombres }}</td>
                    <td>{{ $work->nombre_completo }}</td>
                    <td>{{ $work->email }}</td>
                    <td>{{ $work->fecha_de_nacimiento }}</td>
                    <td>{{ $work->fecha_de_ingreso }}</td>
                    <td>{{ $work->direccion }}</td>
                    <td>DNI</td>
                    <td>{{ $work->numero_de_documento }}</td>
                    <td>{{ $work->phone }}</td>
                    <td>{{ $work->sexo ? 'Masculino' : 'Femenino' }}</td>
                    <td>{{ $work->profesion }}</td>
                    <td>{{ $work->plaza }}</td>
                    <td>{{ $work->numero_de_essalud }}</td>
                    <td>{{ $work->banco ? $work->banco->nombre : '' }}</td>
                    <td>{{ $work->numero_de_cuenta }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>