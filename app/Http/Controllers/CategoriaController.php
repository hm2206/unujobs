<?php
/**
 * Http/Controllers/CategoriaController.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Concepto;
use Illuminate\Http\Request;
use App\Models\TypeRemuneracion;
use \DB;

/**
 * Class CategoriaController
 * 
 * @category Controllers
 */
class CategoriaController extends Controller
{
    /**
     * Muestra una lista de categorias
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $categorias = Categoria::orderBy('nombre', 'ASC')->paginate(20);
        return view("categorias.index", \compact('categorias'));
    }


    /**
     * Muestra un formulario para crear una nueva categoria
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('categorias.create');
    }

    
    /**
     * Almacena una categoria recien creada
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate(request(), [
            "key" => "required",
            "nombre" => "required"
        ]);

        try {

            $categoria = Categoria::create($request->all());

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
     * Redirige a la ruta origen
     * 
     * @param Categoria $categoria
     * @return void
     */
    public function show(Categoria $categoria)
    {
        return back();
    }


    /**
     * Muestra un formulario para editar una categoria
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function edit($slug)
    {
        $id = \base64_decode($slug);
        $categoria = Categoria::findOrFail($id);
        return view("categorias.edit", compact('categoria'));
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
        $payload = $this->validate(request(), [
            "nombre" => "required",
        ]); 

        try {

            $categoria = Categoria::findOrFail($id);
            $categoria->update($payload);

            return [
                "status" => true,
                "message" => "El registro se actualizó correctamente"
            ];

        } catch (\Throwable $th) {

            return [
                "status" => false,
                "message" => "Ocurrió un error al procesar la operación"
            ];

        }
    }


    /**
     * Redirige a la ruta origen
     * 
     * @param Categoria $categoria
     * @return void
     */
    public function destroy(Categoria $categoria)
    {
        return back();
    }


    /**
     * Muestra un formulario para agregra un concepto a la categoria
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function concepto($slug)
    {
        $id = \base64_decode($slug);
        $categoria = Categoria::findOrFail($id);
        $notIn = $categoria->conceptos->pluck(["id"]);
        $conceptos = Concepto::whereNotIn("id", $notIn)->get();
        return view("categorias.concepto", compact('categoria', 'conceptos'));
    }


    /**
     * Almacena el concepto recien agregado a la categoria
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function conceptoStore(Request $request, $id)
    {
        $this->validate(request(), [
            "concepto_id" => "required",
            "monto" => "required|numeric"
        ]);

        $categoria = Categoria::findOrFail($id);
        $concepto = Concepto::findOrFail($request->concepto_id);
        $monto = $request->monto;

        $categoria->conceptos()->syncWithoutDetaching($concepto->id);
        $categoria->conceptos()->updateExistingPivot($concepto->id, ["monto" => $monto]);
        return back()->with(["success" => "El concepto se añadio correctamente"]);
    }


    /**
     * Muestra un panel con chechbox para agregar la configuración los tipos de remuneraciones por categoria
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function config($slug)
    {
        $id = \base64_decode($slug);
        $categoria = Categoria::with(['conceptos' => function($c) {
            $c->orderBy('conceptos.key', 'ASC');
        }])->findOrFail($id);
        $types = TypeRemuneracion::where("show", 1)
            ->whereHas('categorias', function($c) use($categoria) {
                $c->where('categorias.id', $categoria->id);
            })->get();

        $tmpType = DB::table('concepto_type_remuneracion')->where('categoria_id', $id)->get();
        $checked = \collect();

        $conceptos = $categoria->conceptos->filter(function ($con) {
            $con->check = false;
            return $con;
        });

        foreach ($types as $type) {
            $tmpConceptos = collect();
            foreach ($conceptos as $concepto) {
                $tmp = $tmpType->where("type_remuneracion_id", $type->id)->where("concepto_id", $concepto->id);
                $concepto->check = $tmp->count();
                $tmpConceptos->push([
                    "id" => $concepto->id,
                    "descripcion" => $concepto->descripcion,
                    "key" => $concepto->key,
                    "check" => $concepto->check
                ]);

                if($concepto->check) {
                    $checked->push(["key" => $concepto->key]);
                }

            }
            
            $type->conceptos = collect($tmpConceptos);
        }


        //validar y ocultar
        foreach ($types as $type) {
            $tmp = $type->conceptos->whereIn("key", $checked->pluck(["key"]))->where("check", 0);
            $type->conceptos = $type->conceptos->except($tmp->keys());
        }

        return view('categorias.remuneracion', compact('categoria', 'types'));
    }


    /**
     * Almacena un nueva configuracion para los tipos de remuneraciones por categoria
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function configStore(Request $request, $id)
    {
        $this->validate(request(), [
            "type_remuneracion_id" => "required"
        ]);

        $categoria = Categoria::findOrFail($id);
        $conceptos = $request->input('conceptos', []);
        $payload = [];

        DB::table('concepto_type_remuneracion')->where("categoria_id", $categoria->id)
            ->where('type_remuneracion_id', $request->type_remuneracion_id)
            ->delete();

        foreach ($conceptos as $concepto) {
            $tmp = $categoria->conceptos->find($concepto);
            $monto = $tmp->pivot->monto;
            array_push($payload, [
                "concepto_id" => $tmp->id,
                "type_remuneracion_id" => $request->type_remuneracion_id,
                "categoria_id" => $categoria->id,
                "monto" => $monto
            ]);
        }

        DB::table('concepto_type_remuneracion')->insert($payload);
        $config = "page={$request->page}#type-{$request->type_remuneracion_id}";

        return redirect()->route("categoria.config", [$categoria->slug(), $config]);

    }



    public function updateConcepto(Request $request, $id)
    {
        $this->validate(request(), [
            "monto" => "required|min:1|numeric"
        ]);

        return $request->all();

        try {
            $concepto = DB::table("categoria_concepto")
            ->where("id", $id)
            ->update([
                "monto" => $request->monto
            ]);

            return [
                "status" => true,
                "message" => "El monto fué actualizado."
            ];
        } catch (\Throwable $th) {
            \Log::info($th);
            return [
                "status" => false,
                "message" => "Ocurrió un error :("
            ];
        }

        
    }


    public function deleteConcepto(Request $request, $id) 
    {
        $categoria = Categoria::findOrFail($id);

        try {

            $conceptos = $request->input('conceptos', []);
            $categoria->conceptos()->detach($conceptos);

            return [
                "status" => true,
                "message" => "Los conceptos fuerón eliminado correctamente!"
            ];
        } catch (\Throwable $th) {
            \Log::info($th);
            return [
                "status" => false,
                "message" => "Ocurrio un error al eliminar el concepto"
            ];
        }
    }



    public function type_remuneracion($id)
    {
        $categoria = Categoria::findOrFail($id);
        return TypeRemuneracion::whereHas('categorias', function($car) use($categoria) {
            $car->where('categoria_id', $categoria->id);
        })->orderBy('orden', 'ASC')
            ->get();
    }


    public function assignTypeRemuneracion(Request $request, $id) 
    {
        $categoria = Categoria::findOrFail($id);
        try {

            $type_remuneraciones = $request->input('type_remuneraciones', []);
            $categoria->type_remuneraciones()->syncWithoutDetaching($type_remuneraciones);

            return [
                "status" => true,
                "message" => "Los cargos se agregarón correctamente!"
            ];
        } catch (\Throwable $th) {
            return [
                "status" => false,
                "message" => "Ocurrio un error al procesar la información"
            ];
        }
    }


    public function detachTypeRemuneracion(Request $request, $id) 
    {
        $categoria = Categoria::findOrFail($id);
        $this->validate(request(), [
            "type_remuneracion_id" => "required"
        ]);

        try {

            $type_remuneracion = $request->input('type_remuneracion_id', null);
            $categoria->type_remuneraciones()->detach($type_remuneracion);

            return [
                "status" => true,
                "message" => "Los cargos se agregarón correctamente!"
            ];
        } catch (\Throwable $th) {
            return [
                "status" => false,
                "message" => "Ocurrio un error al procesar la información"
            ];
        }
    }
}
