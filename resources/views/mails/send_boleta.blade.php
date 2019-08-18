<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte de boleta del {{ $mes }}-{{ $year }}</title>
</head>
<body>
    
    
    Hola {{ strtoupper($work->nombre_completo) }}, La boleta electrónica {{ $mes }} del {{ $year }} ya está lista. <br> <br>
    Esta boleta es solo informátiva. <br><br>

    @if ($jefe)
    
        Atte. {{ $jefe->profesion }} {{ $jefe->nombre_completo }} 
        
    @endif
  

</body>
</html>