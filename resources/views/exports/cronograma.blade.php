
<table>
    <thead>
        <tr>
            <th>concepto</th>
            <th>adicional</th>
            <th>meta</th>
            <th>ext_pptto</th>
            <th>actividad</th>
            <th>escuela</th>
            <th>paterno</th>
            <th>materno</th>
            <th>nombres</th>
            <th>cod_doc</th>
            <th>ruc</th>
            <th>fuente</th>
            <th>fuente_nom</th>
            <th>doc_tipo</th>
            <th>doc_nro</th>
            <th>plaza</th>
            <th>cod_rem</th>
            <th>categoria</th>
            <th>condicion1</th>
            <th>condicion2</th>
            <th>dedicacion</th>
            <th>cargo</th>
            <th>codigo_cta</th>
            <th>cuenta</th>
            <th>banco</th>
            <th>nro_autoge</th>
            <th>nro_cuspp</th>
            <th>afp</th>
            <th>afp_nom</th>
            <th>afp_fecha</th>
            <th>afp_tipo</th>
            <th>sexo</th>
            <th>dirección</th>
            <th>teléfono</th>
            <th>fec_nac</th>
            <th>fec_ingreso</th>
            <th>dias</th>
            @foreach ($type_remuneraciones as $type_rem)
                <th>
                    x{{ $type_rem->key }}
                </th>
            @endforeach
            <th>total_bruto</th>
            @foreach ($type_descuentos as $type_des)
                <th>
                    x{{ $type_des->key }}
                </th>
            @endforeach
            <th>total_descuentos</th>
            <th>base_imponible</th>
            <th>total_neto</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($works as $work)
            @foreach ($work->infos as $info)
                <tr>
                    <td>{{ $planilla->key }}</td>
                    <td>{{ $cronograma->adicional }}</td>
                    <td>{{ $info->meta ? $info->meta->metaID : '' }}</td>
                    <td>{{ $info->cargo ? $info->cargo->ext_pptto : '' }}</td>
                    <td>{{ $info->meta ? $info->meta->actividadID : '' }}</td>
                    <td>{{ $info->escuela }}</td>
                    <td>{{ $work->ape_paterno }}</td>
                    <td>{{ $work->ape_materno }}</td>
                    <td>{{ $work->nombres }}</td>
                    <td>01</td>
                    <td>{{ $info->ruc }}</td>
                    <td>{{ $info->fuente_id }}</td>
                    <td>{{ $info->fuente_nombre }}</td>
                    <td>DNI/LE</td>
                    <td>{{ $work->numero_de_documento }}</td>
                    <td>{{ $info->plaza }}</td>
                    <td>{{ $info->categoria ? $info->categoria->key : '' }}</td>
                    <td>{{ $info->categoria ? $info->categoria->nombre : '' }}</td>
                    <td>{{ $info->cargo ? $info->cargo->descripcion : '' }}</td>
                    <td>{{ $info->cargo ? $info->cargo->tag : '' }}</td>
                    <td></td>
                    <td>{{ $info->perfil }}</td>
                    <td></td>
                    <td>{{ $work->numero_de_cuenta }}</td>
                    <td>{{ $work->banco ? $work->banco->nombre : '' }}</td>
                    <td>{{ $work->numero_de_essalud }}</td>
                    <td>{{ $work->numero_de_cuspp }}</td>
                    <td>{{ $work->afp_id }}</td>
                    <td>{{ $work->afp ? $work->afp->nombre : '' }}</td>
                    <td>{{ $work->fecha_de_afiliacion }}</td>
                    <td>{{ $work->type_afp }}</td>
                    <td>{{ $work->sexo ? 'M' : 'F' }}</td>
                    <td>{{ $work->direccion }}</td>
                    <td>{{ $work->phone }}</td>
                    <td>{{ $work->fecha_de_nacimiento }}</td>
                    <td>{{ $work->fecha_de_ingreso }}</td>
                    <td>{{ $cronograma->dias }}</td>
                    @foreach ($info->remuneraciones as $remuneracion)
                        <td>{{ $remuneracion->monto }}</td>
                    @endforeach
                    <td>{{ $info->total_bruto }}</td>
                    @foreach ($info->descuentos as $descuento)
                        <td>{{ $descuento->monto }}</td>
                    @endforeach
                    <th>{{ $info->total_descuento }}</th>
                    <th>{{ $info->base }}</th>
                    <th>{{ $info->total_neto }}</th>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>