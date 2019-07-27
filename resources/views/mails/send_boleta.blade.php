<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte de boleta del {{ $mes }}-{{ $year }}</title>
</head>
<body>
    
    
    Hola {{ strtoupper($work->nombre_completo) }}, La boleta {{ $mes }} del {{ $year }} ya está lista. <br> <br>
    Atte. Oficina de Recursos Humanos.
  

</body>
</html>