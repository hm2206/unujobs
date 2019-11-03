<!DOCTYPE html>
<html lang="en">
<head>
    @php
        $config = App\Models\Config::first();
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ public_path() . "/css/app.css" }}">
    <link rel="stylesheet" href="{{ public_path() . "/css/pdf.css" }}">
    <title>Resumen de Ingreso</title>
</head>
   
    <body class="bg-white text-negro p-relative">
                
        <table>
            <thead>
                <tr>
                    <th>
                        <img src="{{ public_path() . $config->logo }}" width="50" alt="">
                    </th>
                    <th>
                        <div><b>{{ $config->nombre }}</b></div>
                        <div class="ml-1 text-sm">OFICINA GENERAL DE RECURSOS HUMANOS</div>
                        <div class="ml-1 text-sm">OFICINA EJECUTIVA DE REMUNERACIONES Y PENSIONES</div>
                    </th>
                </tr>
            </thead>
        </table>    

        <div class="boleta-header mt-3 w-100 text-dark">
            <table class="table-boleta table-sm w-100">
                <thead> 
                    <tr>
                        <td class="py-0 pl-3 pt-1">000000</td>
                        <td class="py-0 pt-1" colspan="2">A.F.P:</td>
                        <td class="py-0 pt-1">N° CUSSP:</td>
                        <td class="py-0 pt-1">N° ESSALUD:</td>
                        <td class="py-0 pt-1" width="10%">D.N.I:</td>
                        <td class="py-0 pt-1">OBSERVACIONES:</td>
                    </tr>
                    <tr>
                        <td class="py-0 pb-1 pl-3" colspan="4">Nombres y Apellidos:</td>
                        <td colspan="3" class="py-0">Condición Laboral:</td>
                        <td class="py-0" colspan="2">Cargo:</td>
                    </tr>
                    <tr>
                        <td class="py-1 pl-3 bbt-1" width="8%"><b>Mes</b></td>
                        <td class="py-2 bbt-1" width="8%"><b>Año</b></td>
                        <td class="py-1 bbt-1" width="8%"><b>Categoría</b></td>
                    </tr>
                </thead>
            </table>
        </div>

        <table class="w-100" style="height: 50%;">

        </table>


        <div class="w-100 text-dark p-absolute bottom-0">
            <hr>
            <table class="table-sm">
                <thead>
                    <tr>
                        <td class="py-0 pl-3 pt-1">000000</td>
                    </tr>
                </thead>
            </table>
        </div>
                
    </body>
   
</html>