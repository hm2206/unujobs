<?php

namespace App\Http\Controllers;

use App\Models\TypeDescuento;
use Illuminate\Http\Request;
use App\Models\Sindicato;
use App\Models\Afp;

class DescuentoController extends Controller
{


    public function index()
    {
        $descuentos = TypeDescuento::all();
        return view('descuentos.index', compact(['descuentos']));
    }


    public function create()
    {
        return view("descuentos.create");
    }


    public function store(Request $request)
    {
        $this->validate(request(), [
            "descripcion" => "required",
            "key" => "required|unique:type_descuentos,key"
        ]);

        $type = TypeDescuento::create($request->all());
        return back()->with(["success" => "Los datos se guardarón correctamente"]);
    }


    public function show(Descuento $descuento)
    {
        //
    }


    public function edit($id)
    {
        $type = TypeDescuento::findOrFail($id);
        return view("descuentos.edit", \compact('type'));
    }


    public function update(Request $request, $id)
    {
        $this->validate(request(), [
            "key" => "required",
            "descripcion" => "required"
        ]);

        $type = TypeDescuento::findOrFail($id);
        $type->update($request->all());
        return back()->with(["success" => "Los datos se actualizarón correctamente"]);
    }


    public function destroy(Descuento $descuento)
    {
        //
    }

    public function config($id) 
    {
        $type = TypeDescuento::findOrFail($id);
        $config = json_decode($type->config_afp);
        $sindicatos = Sindicato::all();

        $sindicatos->map(function($s) use($type) {
            $checked = $type->sindicatos->where("id", $s->id);
            $s->check = count($checked) > 0 ? true : false;
            return $s;
        });

        $tmps = [
            ["name" => "Flujo", "checked" => false],
            ["name" => "Mixta", "checked" => false],
            ["name" => "Aporte", "checked" => false],
            ["name" => "Prima", "checked" => false]
        ];

        $seguros = [];

        foreach ($tmps as $tmp) {

            $checked = false;
            if(is_array($config)) {
                foreach ($config as $con) {
                    if($con == strtolower($tmp['name'])) {
                        $checked = true;
                        break;
                    }
                }
            }

            $tmp['checked'] = $checked;
            array_push($seguros, $tmp);
        }

        return view('descuentos.config', compact('type', 'sindicatos', 'config', 'seguros'));
    }

    public function configStore(Request $request, $id)
    {
        $type = TypeDescuento::findOrFail($id);

        $sindicatos = $request->input("sindicatos", []);
        $seguros = $request->input("seguros", []);

        $type->sindicatos()->sync($sindicatos);
        $type->update(["config_afp" => json_encode($seguros)]);

        return back()->with(["success" => "Los datos se guardarón correctamente"]);
    }

}
