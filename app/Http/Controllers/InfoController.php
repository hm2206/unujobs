<?php
/**
 * Http/Controllers/InfoController.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Http\Controllers;

use App\Models\Info;
use \DB;
use Illuminate\Http\Request;
use App\Models\Cronograma;
use App\Models\Remuneracion;
use App\Models\Descuento;
use App\Models\TypeRemuneracion;
use App\Models\TypeDescuento;
use App\Collections\InfoCollection;
use App\Models\Obligacion;

/**
 * Class InfoController
 * 
 * @category Controllers
 */
class InfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->work_id) {
            return Info::with(
                'cargo', 
                'planilla', 
                'categoria', 
                'meta', 
                'sindicato'
            )->where("work_id", request()->work_id)
            ->get();
        }
        return Info::paginate(10);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Info  $info
     * @return \Illuminate\Http\Response
     */
    public function show(Info $info)
    {
        return back();
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Info  $info
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $this->validate(request(), [
            "planilla_id" => "required|max:20",
            "cargo_id" => "required|max:20",
            "categoria_id" => "required|max:20",
            "meta_id" => "required|max:20",
            "pap" => "required|max:2",
            "perfil" => "required|max:200",
        ]);

        try {

            $info = Info::findOrFail($id);
            $info->update($request->all());

            $cronograma_id = $request->cronograma_id;

            if ($cronograma_id) {
                $create = DB::table("info_cronograma")->where("cronograma_id", $cronograma_id)
                    ->where("info_id", $info->id)
                    ->update(["observacion" => $request->observacion]);
            }

            return [
                "status" => true,
                "message" => "Los datos se guardarón correctamente"
            ];

        } catch (\Throwable $th) {
            
            \Log::info($th);
            return [
                "status" => false,
                "message" => "Ocurrió un error"
            ];

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Info  $info
     * @return \Illuminate\Http\Response
     */
    public function destroy(Info $info)
    {
        return back();
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Info  $info
     * @return \Illuminate\Http\Response
     */
    public function remuneracion($id)
    {
        $info = Info::findOrFail($id);
        
        // configuración
        $year = request()->input('year', date('Y'));
        $mes = request()->input('mes', date('m'));
        $adicional = request()->adicional ? 1 : 0;
        $numero = request()->numero ? request()->numero : 1;
        
        // almacenar
        $total = 0;
        $dias = 30;
        $seleccionar = [];
        $cronograma = Cronograma::where('planilla_id', $info->planilla_id)
            ->where('mes', $mes)
            ->where('año', $year)
            ->where("adicional", $adicional);

        if($adicional) {

            $seleccionar = $cronograma->get();
            $cronograma = $cronograma->where("numero", $numero);

        }

        $cronograma = $cronograma->firstOrFail();

        // preguntamos si estamos agregados a la planilla
        $isWork = $cronograma->infos->find($id);

        if (!$isWork) {
            abort(404);
        }

        $remuneraciones = Remuneracion::with('typeRemuneracion')
            ->where("info_id", $id)
            ->where("mes", $mes)
            ->where("año", $year)
            ->where("adicional", $adicional)
            ->where("cronograma_id", $cronograma->id)
            ->get();

        $dias = $cronograma->dias;
        $total = round($remuneraciones->sum('monto'), 2);

        return [
            "cronograma" => $cronograma,
            "numeros" => $seleccionar,
            "remuneraciones" => $remuneraciones,
            "seleccionar" => $seleccionar,
            "dias" => $dias,
            "total" => $total,
            "adicional" => $adicional,
            "year" => $year,
            "mes" => $mes
        ];
    }


    public function remuneracionUpdate(Request $request, $id)
    {

        $this->validate(request(), [
            "cronograma_id" => "required",
            "cargo_id" => "required",
            "categoria_id" => "required",
            "planilla_id" => "required"
        ]);

        try {
        
            $info = Info::where("active", 1)->findOrFail($id);
            $cronograma = Cronograma::findOrFail($request->cronograma_id);

            if ($cronograma->estado == 0) {
                return [
                    "status" => false,
                    "message" => "La planilla está desactivada",
                    "body" => ""
                ];
            }

            $remuneraciones = $info->remuneraciones->where("cronograma_id", $cronograma->id);
            $total = 0;

            foreach ($remuneraciones as $remuneracion) {
                $tmp_remuneracion = $request->input($remuneracion->id);
                if (is_numeric($tmp_remuneracion)) {
                    $remuneracion->monto = round($tmp_remuneracion, 2);
                    $remuneracion->save();
                    $total += $tmp_remuneracion;
                }
            }

            // preguntamos si estamos agregados a la planilla
            $isWork = $cronograma->infos->find($id);

            if (!$isWork) {
                abort(404);
            }


            // configuracion para actualizar los descuentos
            $type_descuentos = TypeDescuento::where("activo", 1)->get();
            $remuneraciones_base = $remuneraciones->where("base", 0);


            // procesar los descuentos
            $collection = new InfoCollection($info);
            $collection->updateOrCreateDescuento($cronograma, $type_descuentos, $remuneraciones_base);
            

            // guardar monto total actual
            $info->update(["total" => round($total, 2)]);

            return [
                "status" => true,
                "message" => "Los datos se actualizarón correctamente!",
                "body" => round($total, 2)
            ];
        } catch (\Throwable $th) {

            \Log::info($th);
            return [
                "status" => false,
                "message" => "Ocurrió un error al procesar la operación",
                "total" => 0
            ];
        }

    }


    public function descuento($id) 
    {

        $info = Info::findOrFail($id);

        // configuración
        $year = request()->input('year', date('Y'));
        $mes = request()->input('mes', date('m'));
        $adicional = request()->adicional ? 1 : 0;
        $numero = request()->numero ? request()->numero : 1;

        // almacenar
        $total = 0;
        $dias = 30;
        $base = 0;
        $seleccionar = [];
        $types = [];
        $cronograma = Cronograma::where("planilla_id", $info->planilla_id)
                ->where('mes', $mes)
                ->where('año', $year)
                ->where("adicional", $adicional);


        if($adicional) {

            $seleccionar = $cronograma->get();
            $cronograma = $cronograma->where("numero", $numero);
        
        }
        
        $cronograma = $cronograma->firstOrFail();

        $descuentos = Descuento::with(['typeDescuento' => function($q){
                $q->orderBy("key", "ASC");
        }])->where("info_id", $id)
            ->where("mes", $mes)
            ->where("año", $year)
            ->where("adicional", $adicional)
            ->where("cronograma_id", $cronograma->id)
            ->get();

        $remuneraciones = Remuneracion::where("info_id", $id)
            ->where("mes", $mes)
            ->where("año", $year)
            ->where("adicional", $adicional)
            ->where("cronograma_id", $cronograma->id)
            ->get();

        $base = round($remuneraciones->where("base", 0)->sum('monto'), 2);
        $total = round($descuentos->where("base", 0)->sum('monto'), 2);
        $dias = $cronograma->dias;

        $total_neto =  round($remuneraciones->sum('monto') - $total, 2);

        $aportaciones = $descuentos->where("base", 1);

        return [
            "cronograma" => $cronograma,
            "numeros" => $seleccionar,
            "descuentos" => $descuentos,
            "aportaciones" => $aportaciones,
            "dias" => $dias,
            "total" => $total,
            "adicional" => $adicional,
            "year" => $year,
            "mes" => $mes,
            "base" => $base,
            "total_neto" => $total_neto
        ];
    }


    public function descuentoUpdate(Request $request, $id)
    {
        $this->validate(request(), [
            "cronograma_id" => "required",
            "categoria_id" => "required",
            "categoria_id" => "required",
            "planilla_id" => "required"
        ]);
        
        try {
            
            $info = Info::findOrFail($id);
            $cronograma = Cronograma::findOrFail($request->cronograma_id);

            if ($cronograma->estado == 0) {
                return [
                    "status" => false,
                    "message" => "La planilla está desactivada",
                    "body" => ""
                ];
            }

            $remuneraciones = $info->remuneraciones->where("cronograma_id", $cronograma->id);

            $descuentos = Descuento::where("info_id", $info->id)
                ->where("cronograma_id", $cronograma->id)
                ->get();

            foreach ($descuentos as $descuento) {
                $tmp_descuento = $request->input($descuento->id);
                if (is_numeric($tmp_descuento) && $descuento->edit) {
                        $descuento->monto = round($tmp_descuento, 2);
                        $descuento->save();
                }
            }

            $total = round($descuentos->where('base', 0)->sum('monto'), 2);
            $base = round($remuneraciones->where('base', 0)->sum('monto'), 2);
            $total_neto = round($remuneraciones->sum('monto') - $total, 2);

            return [
                "status" => true,
                "message" => "Los datos se actualizarón correctamente!",
                "body" => [
                    "total" => $total,
                    "total_neto" => $total_neto,
                    "base" => $base
                ]
            ];
        } catch (\Throwable $th) {
            return [
                "status" => false,
                "message" => "Ocurrió un error al procesar la operación",
                "body" => ""
            ];
        }
    }


    public function observacion($id) 
    {
        $mes = request()->mes ? request()->mes : date('mes');
        $year = request()->year ? request()->year : date('Y');
        $adicional = request()->adicional ? 1 : 0;
        $numero = request()->numero ? request()->numero : 1;
        $seleccionar = [];

        $cronograma = Cronograma::where("mes", $mes)
            ->where("año", $year)
            ->where("adicional", $adicional);
            
        if ($adicional) {
            $seleccionar = $cronograma->get();
            $cronograma = $cronograma->where("numero", $numero);
        }

        $cronograma = $cronograma->firstOrFail();

        $observacion = DB::table("info_cronograma")
            ->where("cronograma_id", $cronograma->id)
            ->where("info_id", $id)
            ->select("observacion")
            ->first();

        $obs = isset($observacion->observacion) ? $observacion->observacion : '';

        return $obs;
    }   


    public function obligacion($id) 
    {
        $cronograma_id = request()->cronograma_id;
        $info = Info::findOrFail($id);
        $obligaciones = Obligacion::where("cronograma_id", $cronograma_id)
            ->where("info_id", $info->id)
            ->get();

        return $obligaciones;
    }

}
