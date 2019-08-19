<?php
/**
 * Http/Controllers/TypeRemuneracionController.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Http\Controllers;

use App\Models\TypeRemuneracion;
use Illuminate\Http\Request;

/**
 * Class TypeRemuneracionController
 * 
 * @category Controllers
 */
class TypeRemuneracionController extends Controller
{

    /**
     * Muestra una lista de los tipos de remuneracion
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $remuneraciones = TypeRemuneracion::all();

        return view('remuneraciones.index', compact('remuneraciones'));
    }

    /**
     * Muestra un formulario para crear un nuevo tipo de remuneración
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('remuneraciones.create');
    }


    /**
     * Almacena un tipo de remuneración recien creado
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $payload = $this->validate(request(), [
            "key" => "required|unique:type_remuneracions",
            "descripcion" => "required",
        ]);
        
        try {
            
            $base = $request->base ? 1 : 0;
            $activo = $request->activo ? 1 : 0;

            $type = TypeRemuneracion::create($payload);
            $type->update([
                "base" => $base
            ]);

            return [
                "status" => true,
                "message" => "Los datos se guardarón correctamente"
            ];

        } catch (\Throwable $th) {
            
            return [
                "status" => true,
                "message" => "Ocurrió un error al procesar la operación"
            ];

        }
    }

    /**
     * Undocumented function
     *
     * @param TypeRemuneracion $typeRemuneracion
     * @return void
     */
    public function show(TypeRemuneracion $typeRemuneracion)
    {
        return back();
    }


    /**
     * Muestra un formulario para editar un tipo de remuneración
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function edit($slug)
    {
        $id = \base64_decode($slug);
        $remuneracion = TypeRemuneracion::findOrFail($id);

        return view('remuneraciones.edit', compact('remuneracion'));
    }

    /**
     * Actualiza un tipo de remuneración recien modificado
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $this->validate(request(), [
            "descripcion" => "required",
        ]);

        try {

            $remuneracion = TypeRemuneracion::findOrFail($id);

            $base = $request->base ? 1 : 0;
            $activo = $request->activo ? 1 : 0;

            $remuneracion->update([
                "descripcion" => $request->descripcion,
                "base" => $base,
                "activo" => $activo
            ]);

            return [
                "status" => true,
                "message" => "Los datos se actualizarón correctamente"
            ];

        } catch (\Throwable $th) {
            
            return [
                "status" => true,
                "message" => "Ocurrió un error al procesar la operación"
            ];

        }
    }


    /**
     * Undocumented function
     *
     * @param TypeRemuneracion $typeRemuneracion
     * @return void
     */
    public function destroy(TypeRemuneracion $typeRemuneracion)
    {
        return back();
    }
}
