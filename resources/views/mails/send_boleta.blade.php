<!DOCTYPE html>
<html lang="es_Es">
<head>
    @php
        $config = App\Models\Config::first();
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte de boleta del {{ $cronograma->mes }}-{{ $cronograma->año }}</title>
</head>
<body>
    
    
    Hola {{ strtoupper($work->nombre_completo) }}, La boleta electrónica {{ $cronograma->mes }} del {{ $cronograma->año }} ya está lista. <br> <br>
    Esta boleta es solo informátiva. <br>
    Para que la boleta tenga valides, debe tener la firma del Jefe(a) de la Oficina Ejecutiva de Remuneraciones y Pensiones

    <br>
    <br>
    <br>

    <b>Atte. Oficina Ejecutiva de Remuneraciones y Pensiones - {{ $config->alias }}</b>
   
  

</body>
</html>