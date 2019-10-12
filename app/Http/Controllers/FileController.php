<?php

namespace App\Http\Controllers;

use App\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Cronograma;
use App\Models\Boleta;
use App\Models\Remuneracion;
use App\Models\Descuento;
use App\Models\Planilla;
use App\Models\Work;
use App\Tools\FormatTXT;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $disk = request()->input('disk', 'public');
        $path = request()->input('path');

        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->download($path);
        }

        return 'el archivo no existe';
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\File  $file
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        set_time_limit(0);
        // obtener el primer cronograma
        $cronograma = Cronograma::findOrFail($id);
        $planilla = Planilla::findOrFail($cronograma->planilla_id);
        // obtener las boletas de los trabajadores
        $works = Work::whereHas("boletas", function($b) use($cronograma) {
            $b->where("cronograma_id", $cronograma->id);
            $b->whereHas("work", function($w) {
                $w->where("numero_de_cuenta", "<>", "");
            });
        })->select(['id', 'numero_de_cuenta', 'numero_de_documento', 'especial'])->get();
        // remuneraciones
        $remuneraciones = Remuneracion::where("cronograma_id", $cronograma->id)
            ->whereIn("work_id", $works->pluck(['id']))->get();
        // descuentos
        $descuentos = Descuento::where("base", 0)->where("cronograma_id", $cronograma->id)
            ->whereIn("work_id", $works->pluck(['id']))->get();

        if ($planilla->especial) {
            return $this->formatSpecial($works, $cronograma, $planilla, $remuneraciones, $descuentos);
        }else {
            
            $payload = [];

            foreach ($works as $work) {
                // obtener el neto
                $bruto = $remuneraciones->where("work_id", $work->id)->sum('monto');
                $dscto = $descuentos->where("work_id", $work->id)->sum('monto');
                $monto = round($bruto, 2) - round($dscto, 2);
                // almacenar en el payload solo si el monto es mayor a cero
                if ($monto > 0) {
                    array_push($payload, [
                        "numero_de_documento" => $work->numero_de_documento,
                        "monto" => $monto
                    ]);
                }
            }
    
            // generar archivo de exportación para el siaf
            $file = FormatTXT::init();
            $file->setPlanilla($planilla->key);
            $file->setHeader($cronograma->año, $cronograma->mes);
            $file->setBody($payload);
            $file->generateFile("/txt");
            return $file->download();
        }

    }


    private function formatSpecial($works, $cronograma, $planilla, $remuneraciones, $descuentos) 
    {
        $workSimple = $works->where("especial", 0);
        $workSpecials = $works->where("especial", 1);

        $config = [
            ["body" => $workSimple, "activo" => 2, "key" => 7, 'titulo' => 'Pensionistas'],
            ["body" => $workSpecials, "activo" => 3, "key" => 8, 'titulo' => 'Pensionistas - Sobrevivientes'],
        ];

        $outputs = [];
        
        foreach ($config as $conf) {
            
            $payload = [];

            foreach ($conf['body'] as $work) {
                // obtener el neto
                $bruto = $remuneraciones->where("work_id", $work->id)->sum('monto');
                $dscto = $descuentos->where("work_id", $work->id)->sum('monto');
                $monto = round($bruto, 2) - round($dscto, 2);
                // almacenar en el payload solo si el monto es mayor a cero
                if ($monto > 0) {
                    array_push($payload, [
                        "numero_de_documento" => $work->numero_de_documento,
                        "monto" => $monto
                    ]);
                }
            }

            // generar archivo de exportación para el siaf
            $file = FormatTXT::init();
            $file->setActivo($conf['activo']);
            $file->setPlanilla($conf['key']);
            $file->setHeader($cronograma->año, $cronograma->mes);
            $file->setBody($payload);
            $file->generateFile("/txt");
            
            $file->save();
            array_push($outputs, [
                "titulo" => $conf['titulo'],
                "file" => $file->getPath()
            ]);

            
        }

        foreach ($outputs as $output) {
            echo "<a target='__blank' href='/api/v1/file?path={$output['file']}'>
                {$output['file']} -> ({$output['titulo']})
            </a> </br>";
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\File  $file
     * @return \Illuminate\Http\Response
     */
    public function edit(File $file)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\File  $file
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, File $file)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\File  $file
     * @return \Illuminate\Http\Response
     */
    public function destroy(File $file)
    {
        //
    }
}
