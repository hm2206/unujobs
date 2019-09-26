<?php
/**
 * Http/Controllers/ObligacionController.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Http\Controllers;

use App\Models\Obligacion;
use App\Models\TypeDescuento;
use App\Models\Descuento;
use App\Models\Cronograma;
use Illuminate\Http\Request;

/**
 * Class ObligacionController
 * 
 * @category Controllers
 */
class ObligacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return back();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return back();
    }

    /**
     * Almacena una obligacion recien creada
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate(request(), [
            "beneficiario" => "required",
            "numero_de_documento" => "required",
            "numero_de_cuenta" => "required",
            "monto" => "required|numeric",
            "work_id" => "required",
            "cronograma_id" => "required",
            "info_id" => "required"
        ]);

        try {
            // verificamos que el cronograma este activo
            $cronograma = Cronograma::where("estado", 1)->findOrFail($request->cronograma_id);
            $obligacion = Obligacion::create($request->all());
            $monto = Obligacion::where("info_id", $request->info_id)
                ->where("cronograma_id", $request->cronograma_id)
                ->sum("monto");

            $type = TypeDescuento::where("obligatorio", 1)->get()->pluck(['id']);
            $descuento = Descuento::where("cronograma_id", $request->cronograma_id)
                    ->where("info_id", $request->work_id)
                    ->whereIn("type_descuento_id", $type)
                    ->update([
                        "monto" => $monto,
                        "edit" => 0
                    ]);

            return [
                "status" => true,
                "message" => "Los datos se guardarón correctamente!",
                "body" => $obligacion
            ];

        } catch (\Throwable $th) {
            
            \Log::info($th);
            return [
                "status" => false,
                "message" => "Ocurrio un error al procesar la operación",
                "body" => "error"
            ];

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Obligacion  $obligacion
     * @return \Illuminate\Http\Response
     */
    public function show(Obligacion $obligacion)
    {
        return back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Obligacion  $obligacion
     * @return \Illuminate\Http\Response
     */
    public function edit(Obligacion $obligacion)
    {
        return back();
    }

    /**
     * Actualiza una obligacion recien modificada
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $this->validate(request(), [
            "up_beneficiario" => "required",
            "up_numero_de_documento" => "required",
            "up_numero_de_cuenta" => "required",
            "up_monto" => "required|numeric",
        ]);

        try {
            
            $obligacion = Obligacion::findOrFail($id);
            $obligacion->update([
                "beneficiario" => $request->input("up_beneficiario"),
                "numero_de_documento" => $request->input("up_numero_de_documento"),
                "numero_de_cuenta" => $request->input("up_numero_de_cuenta"),
                "monto" => $request->input("up_monto"),
            ]);

            $monto = Obligacion::where("work_id", $obligacion->work_id)
                ->where("cronograma_id", $obligacion->cronograma_id)
                ->where("categoria_id", $obligacion->categoria_id)
                ->sum("monto");

            $type = TypeDescuento::where("obligatorio", 1)->get()->pluck(['id']);
            $descuento = Descuento::where("cronograma_id", $obligacion->cronograma_id)
                    ->where("work_id", $obligacion->work_id)
                    ->where("categoria_id", $obligacion->categoria_id)
                    ->whereIn("type_descuento_id", $type)
                    ->update([
                        "monto" => $monto,
                        "edit" => 0
                    ]);

            return [
                "status" => true,
                "message" => "Los datos se guardarón correctamente!",
                "body" => $obligacion
            ];

        } catch (\Throwable $th) {
            
            \Log::info($th);

            return [
                "status" => false,
                "message" => "Ocurrio un error al procesar la operación",
                "body" => ""
            ];

        }

        return back()->with(["update" => "Los datos se actualizarón correctamente!"]);
    }

    /**
     * Remueve una obligacion específica
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {

            $obligacion = Obligacion::findOrFail($id);
            $obligacion->delete();

            $monto = Obligacion::where("work_id", $obligacion->work_id)
                ->where("cronograma_id", $obligacion->cronograma_id)
                ->where("categoria_id", $obligacion->categoria_id)
                ->sum("monto");

            $type = TypeDescuento::where("obligatorio", 1)->get()->pluck(['id']);
            $descuento = Descuento::where("cronograma_id", $obligacion->cronograma_id)
                    ->where("work_id", $obligacion->work_id)
                    ->where("categoria_id", $obligacion->categoria_id)
                    ->whereIn("type_descuento_id", $type)
                    ->update([
                        "monto" => $monto,
                        "edit" => 0
                    ]);
    
            return [
                "status" => true,
                "message" => "Los datos se eliminarón correctamente"
            ];

        } catch (\Throwable $th) {
            
            return [
                "status" => false,
                "message" => "Ocurrio un error al procesar la operación"
            ];

        }

    }
}
