<?php

namespace App\Http\Controllers;

use App\Models\Meta;
use Illuminate\Http\Request;
use App\Http\Requests\MetaRequest;

class MetaController extends Controller
{

    /**
     * Muestra una lista de las metas presupuestales
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $like = request()->input("query_search", null);
        $metas = Meta::orderBy("created_at", 'DESC');

        if($like) {
            $metas = self::query($like, $metas);
        }

        $metas =  $metas->paginate(1);
        return view("meta.index", compact('metas'));
    }


    /**
     * Muestra un formulario para crear una nueva meta presupuestal
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('meta.create');
    }


    /**
     * Almacena una meta presupuestal recien creado
     *
     * @param  \App\Http\Request\MetaRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(MetaRequest $request)
    {
        $meta = Meta::create($request->all());
        return redirect()->route('meta.index')->with(["success" => "El registro se guardo correctamente"]);
    }


    public function show(Meta $meta)
    {
        return back();
    }


    /**
     * Muestra un formulario para editar una meta presupuestal
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function edit($slug)
    {
        $id = \base64_decode($slug);
        $meta = Meta::where("metaID", $id)->firstOrFail();
        return view('meta.edit', \compact('meta'));
    }

    /**
     * Actualiza una meta presupuestal recien modificado
     *
     * @param  \App\Http\Request\MetaRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(MetaRequest $request, $id)
    {
        $meta = Meta::find($id);
        $meta->update($request->all());
        return back()->with(["update" => "El registro se actualizo correctamente"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Meta  $meta
     * @return \Illuminate\Http\Response
     */
    public function destroy(Meta $meta)
    {
        return back();
    }

    /**
     * Realiza una busqueda de las metas presupuestales
     * 
     * @param  string  $like
     * @param  \Illuminate\Database\Eloquent\Builder  $metas
     * @return  \Illuminate\Database\Eloquent\Builder
     */
    public function query($like,\Illuminate\Database\Eloquent\Builder $metas) 
    {
        return $metas->where("metaID", "like", "%{$like}%")
            ->orwhere("meta", "like", "%{$like}%")
            ->orWhere("sectorID", "like", "%{$like}%")
            ->orWhere("sector", "like", "%{$like}%")
            ->orWhere("actividadID", "like", "%{$like}%")
            ->orWhere("actividad", "like", "%{$like}%");
    }
    
}
