<?php
/**
 * Http/Controllers/ConceptoController.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Http\Controllers;

use App\Models\Concepto;
use Illuminate\Http\Request;

/**
 * Class ConceptoController
 * 
 * @category Controllers
 */
class ConceptoController extends Controller
{
    /**
     * Muestra una lista de conceptos
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $conceptos = Concepto::orderBy('key', 'ASC')->get();
        return view('conceptos.index', compact('conceptos'));
    }

    /**
     * Muestra un formulario para crear una nuevo concepto
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('conceptos.create');
    }

    /**
     * Almacena un concepto recien creado
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate(request(), [
            "key" => "required",
            "descripcion" => "required"
        ]);

        try {

            $concepto = Concepto::create($request->all());
            return [
                "status" => true,
                "message" => "El registro se guardo correctamente"
            ];

        } catch (\Throwable $th) {

            return [
                "status" => false,
                "message" => "Ocurrió un error al procesar la operación"
            ];
            
        }
    }

    /**
     * Undocumented function
     *
     * @param Concepto $concepto
     * @return void
     */
    public function show(Concepto $concepto)
    {
        return back();
    }

    /**
     * Muestra un formulario para editar un concepto
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function edit($slug)
    {
        $id = \base64_decode($slug);
        $concepto = Concepto::findOrFail($id);
        return view('conceptos.edit', \compact('concepto'));
    }


    /**
     * Actualiza una categoria recien modificada
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $this->validate(request(), [
            "key" => "required",
            "descripcion" => "required"
        ]);

        try {

            $concepto = Concepto::findOrFail($id);
            $concepto->update($request->all());
            
            return [
                "status" => true,
                "message" => "Los registros fuerón actualizados correctamente"
            ];
            
        } catch (\Throwable $th) {
            
            \Log::info($th);
            
            return [
                "status" => false,
                "message" => "Ocurrió un error al procesar la operación"
            ];

        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Concepto  $concepto
     * @return \Illuminate\Http\Response
     */
    public function destroy(Concepto $concepto)
    {
        return back();
    }
}
