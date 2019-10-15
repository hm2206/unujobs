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
use App\Models\Obligacion;

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


    public function judicial($id)
    {
        set_time_limit(0);
        // obtener el primer cronograma
        $cronograma = Cronograma::findOrFail($id);
        $planilla = Planilla::findOrFail($cronograma->planilla_id);
        // obtener obligaciones
        $obligaciones = Obligacion::where("cronograma_id", $cronograma->id)
            ->where("numero_de_documento", ">", 0)
            ->where("numero_de_cuenta", ">", 0)
            ->where("monto", ">", 0)
            ->orderBy("beneficiario", 'ASC')
            ->get();

        $payload = [];

        foreach ($obligaciones as $obligacion) {
            $key = $obligacion->numero_de_documento;
            $monto = $obligaciones->where('numero_de_documento', $key)->sum('monto');
            $payload[$key] = [
                "numero_de_documento" => $key,
                "monto" => $monto
            ];
        }

        // prefijo
        $prefix = strtoupper($planilla->descripcion);
        // generar archivo de exportación para el siaf
        $file = FormatTXT::init();
        $file->setActivo(4);
        $file->setPlanilla(12);
        $file->setHeader($cronograma->año, $cronograma->mes);
        $file->setBody($payload);
        $file->setPrefix($prefix)->generateFile("/test");
        return $file->download();

    }


    /**
     * Undocumented function
     *
     * @param [type] $year
     * @param [type] $mes
     * @return void
     */
    public function txtEnc($year, $mes)
    {
        set_time_limit(0);
        $cronogramas = Cronograma::where("año", $year)
            ->where("mes", $mes)
            ->get();
        // obtener boletas
        $boletas = Boleta::with(['work' => function($w) {
                $w->orderBy("nombre_completo", 'DESC')->select(["works.id", "works.numero_de_documento"]);
            }])
            ->whereIn("cronograma_id", $cronogramas->pluck(['id']))
            ->get();
        // obtener planillas
        $planillas = Planilla::whereIn("id", $cronogramas->pluck(['planilla_id']))->get();
        // obtener remuneraciones
        $remuneraciones = Remuneracion::whereIn("cronograma_id", $cronogramas->pluck(['id']))
            ->get();
        // obtener descuentos
        $descuentos = Descuento::whereIn("cronograma_id", $cronogramas->pluck(['id']))
            ->get();
        // almacen de datos
        $format_1 = [];
        $format_2 = [];

        foreach ($boletas as $boleta) {
            // codigo de la planilla para el enc
            $planilla_enc = $planillas->find($boleta->planilla_id) ? $planillas->find($boleta->planilla_id)->enc : '';
            // obtener el monto bruto
            $bruto = $remuneraciones->where("info_id", $boleta->info_id)->where("base", 0)->sum('monto');
            // obtener el total de descuentos
            $dscto_base = $descuentos->where("info_id", $boleta->info_id)->where("base", 0)->sum('monto');
            // almacenar la base imponible
            $base = $bruto - $dscto_base;
            
            $codigos = [
                ["key" => $planilla_enc, "monto" => $base],
            ];


            foreach ($codigos as $cod) {
                $config = [
                    "tipo_de_documento" => "01",
                    "numero_de_documento" => $boleta->work ? $boleta->work->numero_de_documento : '',
                    "codigo" => $cod['key'],
                    "n1" => $cod['monto'],
                    "n2" => $cod['monto']
                ];


                $formato_2 = implode("|", $config) . "|";
                array_push($format_2, $formato_2);
            }

            $config = [
                "tipo_de_documento" => "01",
                "numero_de_documento" => $boleta->work ? $boleta->work->numero_de_documento : '',
                "mes" => $boleta->cronograma ? $boleta->cronograma->mes : '',
                "cero" => "0"
            ];

            
            $formato_1 = implode("|", $config) . "|";
            array_push($format_1, $formato_1);
        }

        return implode("<br/>", $format_1);
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
