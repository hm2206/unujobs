<?php
/**
 * ./app/Http/Controllers/CargoController.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */

namespace App\Http\Controllers;

use App\Models\Cargo;
use App\Models\Categoria;
use App\Models\Planilla;
use App\Models\TypeRemuneracion;
use Illuminate\Http\Request;
use \Hash;

/**
 * Class CargoController
 * 
 * @category Controllers
 */
class CargoController extends Controller
{

    /**
     * Muestra una lista de cargos
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $cargos = Cargo::all();
        return view("cargos.index", compact('cargos'));
    }

    /**
     * Muestra un formulario para crear un nuevo cargo
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $planillas = Planilla::all();
        return view('cargos.create', \compact('planillas'));
    }

    /**
     * Almacena un cargo recien creado
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function store(Request $request)
    {
        $this->validate(request(), [
            "descripcion" => "required|unique:cargos",
            "planilla_id" => "required",
            "tag" => "required"
        ]);

        Cargo::create($request->all());

        return back()->with(["success" => "El registro se guardo correctamente!"]);
    }


    public function show(Cargo $cargo)
    {
        return back();
    }

    /**
     * Muestra un formulario para editar un cargo
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function edit($slug)
    {
        $id = \base64_decode($slug);
        $cargo = Cargo::findOrFail($id);
        $planillas = Planilla::all();
        return view('cargos.edit', \compact('planillas', 'cargo'));
    }

    /**
     * Actualiza un cargo recien modificado
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cargo $cargo
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Cargo $cargo)
    {
        $this->validate(request(), [
            "descripcion" => "required|unique:cargos,id,".$request->descripcion,
            "planilla_id" => "required",
            "tag" => "required"
        ]);

        $cargo->update($request->all());

        return back()->with(["success" => "El registro se actualizó correctamente!"]);
    }


    public function destroy(Cargo $cargo)
    {
        return back();
    }


    /**
     * Muestra un formulario para agregra una categoria al cargo
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function categoria($slug)
    {
        $id = \base64_decode($slug);
        $cargo = Cargo::findOrFail($id);
        $notIn = $cargo->categorias->pluck(["id"]);
        $categorias = Categoria::whereNotIn('id', $notIn)->orderBy('nombre', 'ASC')->get();

        return view("cargos.categoria", compact('cargo', 'categorias'));
    }

    /**
     * Almacena la categoria recien agregada al cargo
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function categoriaStore(Request $request, $id)
    {
        $this->validate(request(), [
            "categorias" => "required"
        ]);

        $categorias = $request->input("categorias", []);

        $cargo = Cargo::findOrFail($id);
        $cargo->categorias()->syncWithoutDetaching($categorias);
        return back()->with(["success" => "La categoria se agrego correctamente"]);
    }


    /**
     * Eliminar una categoria agregada al cargo
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function categoriaDelete(Request $request, $id)
    {
        $auth = auth()->user();
        $password = $request->password;
        $categorias = $request->input('categorias', []);

        $validation = Hash::check($password, $auth->password);

        if ($validation) {

            $cargo = Cargo::findOrFail($id);
            $cargo->categorias()->detach($categorias);

            return back()->with(["success" => "Los datos se eliminarón correctamente!"]);
        }

        return back()->with(["danger" => "La contraseña es incorrecta"]);
    }


    /**
     * Muestra un panel con chechbox para agregar la configuración de los aportes
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function config($slug)
    {
        $id = \base64_decode($slug);
        $cargo = Cargo::findOrFail($id);
        $types = TypeRemuneracion::all();

        // return $cargo->types;

        foreach ($types as $type) {
           if ($cargo->typeRemuneracions->count() > 0) {
                $checked = $cargo->typeRemuneracions->where("id", $type->id)->count() ? true : false;
                $type->checked = $checked;
           } else {
               $type->checked = false;
           }
        }

        return view("cargos.config", compact('cargo', 'types'));
    }


    /**
     * Almacena un nueva configuracion al cargo
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function configStore(Request $request, $id)
    {
        $cargo = Cargo::findOrFail($id);
        $types = $request->input('types', []);
        $cargo->typeRemuneracions()->sync($types);
        return back();
    }

}
