
    
    <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Meta ID</th>
                    <th>Meta</th>
                    <th>Sector ID</th>
                    <th>Sector</th>
                    <th>Pliego ID</th>
                    <th>Pliego</th>
                    <th>Unidad ID</th>
                    <th>Unidad Ejecutora</th>
                    <th>Programa ID</th>
                    <th>Programa</th>
                    <th>Función ID</th>
                    <th>Función</th>
                    <th>Sub-Programa ID</th>
                    <th>Sub-Programa</th>
                    <th>Actividad ID</th>
                    <th>Actividad</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($metas as $meta)
                    <tr>
                        <td>{{ $meta->id }}</td>
                        <td>{{ $meta->metaID }}</td>
                        <td>{{ $meta->meta }}</td>
                        <td>{{ $meta->sectorID }}</td>
                        <td>{{ $meta->sector }}</td>
                        <td>{{ $meta->pliedoID }}</td>
                        <td>{{ $meta->pliego }}</td>
                        <td>{{ $meta->unidadID }}</td>
                        <td>{{ $meta->unidad_ejecutora }}</td>
                        <td>{{ $meta->programaID }}</td>
                        <td>{{ $meta->programa }}</td>
                        <td>{{ $meta->funcionID }}</td>
                        <td>{{ $meta->funcion }}</td>
                        <td>{{ $meta->subProgramaID }}</td>
                        <td>{{ $meta->sub_programa }}</td>
                        <td>{{ $meta->actividadID }}</td>
                        <td>{{ $meta->actividad }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>