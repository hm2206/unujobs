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
use App\Models\TypeRemuneracion;
use App\Models\TypeDescuento;
use App\Models\Historial;
use App\Models\Cargo;

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
        $historial = Historial::with('work')
            ->where("numero_de_cuenta", "<>", '')
            ->where("cronograma_id", $cronograma->id)
            ->orderBy('orden', 'ASC')
            ->get();

        if ($planilla->especial) {
            return $this->formatSpecial($historial, $cronograma, $planilla);
        }else {
            
            $payload = [];

            foreach ($historial as $history) {
                // almacenar en el payload solo si el monto es mayor a cero
                array_push($payload, [
                    "numero_de_documento" => $history->work->numero_de_documento,
                    "monto" => $history->total_neto
                ]);
            }
    
            // generar archivo de exportación para el siaf
            $file = FormatTXT::init();
            $file->setPlanilla($planilla->key);
            $file->setHeader($cronograma->año, $cronograma->mes);
            // verificamos si es una planilla adicional
            if ($cronograma->adicional) {
                $file->setNormal((int)$cronograma->numero + 1);
            }
            $file->setBody($payload);
            $file->generateFile("/txt");
            return $file->download();
        }

    }


    private function formatSpecial($historial, $cronograma) 
    {
        $historialSimple = $historial->where("especial", 0);
        $historialSpecials = $historial->where("especial", 1);
        $cargos = Cargo::whereIn("id", $historial->pluck(['cargo_id']))->get();

        $outputs = [];
        
        foreach ($cargos as $cargo) {
            
            $payload = [];
            $newHistorial = $historial->where("cargo_id", $cargo->id);

            foreach ($newHistorial as $history) {
                // obtener el neto
                array_push($payload, [
                    "numero_de_documento" => $history->work->numero_de_documento,
                    "monto" => round($history->total_neto, 2)
                ]);
            }

            // generar archivo de exportación para el siaf
            $file = FormatTXT::init();
            $file->setActivo($cargo->plame_activo);
            $file->setPlanilla($cargo->plame_key);
            $file->setHeader($cronograma->año, $cronograma->mes);
            $file->setBody($payload);
            $file->generateFile("/txt");
            
            $file->save();
            array_push($outputs, [
                "titulo" => $cargo->descripcion,
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
        // tipo de remuneraciones
        $typeRemuneraciones = TypeRemuneracion::where("enc", "<>", "")
            ->where("enc", "<>", null)
            ->get();
        // tipo de descuentos
        $typeDescuentos = TypeDescuento::where("enc", "<>", "")
            ->where("enc", "<>", null)
            ->get();
        // obtener remuneraciones
        $remuneraciones = Remuneracion::where("show", 1)
            ->whereIn("type_remuneracion_id", $typeRemuneraciones->pluck(['id']))
            ->whereIn("cronograma_id", $cronogramas->pluck(['id']))
            ->get();
        // obtener descuentos
        $descuentos = Descuento::whereIn("cronograma_id", $cronogramas->pluck(['id']))
            ->whereIn("type_descuento_id", $typeDescuentos->pluck(['id']))
            ->get();
        // obtener boletas
        $types = [
            [ "type" =>  $typeRemuneraciones, "key" =>  "type_remuneracion_id", "body" => $remuneraciones],
            [ "type" => $typeDescuentos, "key" => "type_descuento_id", "body" => $descuentos ],
        ];

        $boletas = Boleta::with(['work' => function($w) {
                $w->orderBy("nombre_completo", 'DESC')->select(["works.id", "works.numero_de_documento"]);
            }])
            ->whereIn("cronograma_id", $cronogramas->pluck(['id']))
            ->get();
        // obtener planillas
        $planillas = Planilla::whereIn("id", $cronogramas->pluck(['planilla_id']))->get();
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
            $base = $remuneraciones->where('info_id');
            
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

            foreach ($types as $type) {
                foreach ($type['type'] as $tipo) {
                    // monto
                    $monto = $type['body']->where($type['key'], $tipo->id)->sum('monto');
                    // configuración
                    $config = [
                        "tipo_de_documento" => "01",
                        "numero_de_documento" => $boleta->work ? $boleta->work->numero_de_documento : '',
                        "codigo" => $tipo->enc,
                        "n1" => $monto,
                        "n2" => $monto
                    ];
                }
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


        return dd($format_2);

        return implode("<br/>", $format_2);
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


    public function testing(Request $request, $id) {

        $cronograma = Cronograma::findOrFail($id);
        storage::disk('public')->putFileAs("/files", $request->file('file-test'), "file.txt");
        $file = Storage::disk('public')->get('/files/file.txt');
        $total = 0;

        $payload = explode("\n", $file);
        $success = collect();
        $error = [];
        $iter = 0;

        foreach ($payload as $index => $load) {
            
            if ($index % 2 == 0) {
                $tmp = explode(";", $load);
            
                $documento = isset($tmp[1]) ? $tmp[1] : null;
                $monto = isset($tmp[6]) ? (double)$tmp[6] : 0;
                $total += $monto;
                
                if ($documento != null) {
                    $newFormat = collect([
                        "numero_de_documento" => $documento,
                        "monto" => $monto
                    ]);
    
                    $success->push($newFormat);
                }
            }

        }

        /*
        $works = Work::whereIn("numero_de_documento", $success->pluck(['numero_de_documento']))
            ->select("id", "numero_de_documento")
            ->get();
        */

        $works = Work::where("numero_de_cuenta", "<>", "")
            ->where("numero_de_cuenta", "<>", null)
            ->whereHas("banco")
            ->whereHas("boletas", function($b) use($cronograma) {
                $b->where("cronograma_id", $cronograma->id);
            })->select(['id', 'numero_de_cuenta', 'numero_de_documento', 'especial'])
            ->get();

        $notExisten = $works->filter(function($arr) use($success) {
            return $success->where("numero_de_documento", $arr->numero_de_documento)->count() == 0;
        });

        return $notExisten->count();

        // configuración
        /* $works = Work::where("numero_de_cuenta", "<>", "")
            ->where("numero_de_cuenta", "<>", null)
            ->whereHas("banco")
            ->whereHas("boletas", function($b) use($cronograma) {
                $b->where("cronograma_id", $cronograma->id);
            })->select(['id', 'numero_de_cuenta', 'numero_de_documento', 'especial'])
                ->get();
        */

        // remuneraciones
        $remuneraciones = Remuneracion::where("show", 1)->where("cronograma_id", $cronograma->id)
            ->whereIn("work_id", $works->pluck(['id']))
            ->get();
        // descuentos
        $descuentos = Descuento::where("base", 0)->where("cronograma_id", $cronograma->id)
            ->whereIn("work_id", $works->pluck(['id']))
            ->get();

        return $remuneraciones->sum("monto") - $descuentos->sum("monto");

        // validar
        foreach ($success as $res) {
            
            $oldDni = $res['documento'];
            $oldMonto = $res['monto'];

            $work = $works->where("numero_de_documento", $oldDni)->first();

        }

    }
}
