<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Convocatoria;
use App\Models\Personal;
use App\Models\Ubigeo;
use App\Models\Postulante;
use App\Models\TypeEtapa;
use Illuminate\Support\Facades\Cache;

class BolsaController extends Controller
{
    
    public function index()
    {
        $year = request()->input('year', date('Y'));

        $convocatorias = Convocatoria::where("aceptado", 1)
            ->where("fecha_final", "<=", "$year-12-31")
            ->orderBy('fecha_final', 'DESC')->paginate(20);

        return view("bolsa.index", compact('year', 'convocatorias'));
    }

    public function show($numero, $titulo)
    {
        $convocatoria = Convocatoria::findOrFail($numero);
        $types = [];

        $idParse = request()->postulante ? \base64_decode(request()->postulante) : Cache::get('postulante');
        $postulante = Postulante::find($idParse);
        $auth = false;
        $current = "";


        if ($postulante) {
            $auth = true;
            $types = TypeEtapa::all();

            $personal = $postulante->personals->where('slug', $titulo)->first();

            if ($personal) {
                $current = $personal->pivot->type_etapa_id;
            }else {
                $personal = Personal::where("slug", $titulo)->firstOrFail();
            }

        }else {
            $personal = Personal::where("slug", $titulo)->firstOrFail();
        }


        $isExpire = $convocatoria->fecha_final < date('Y-m-d') ? true : false;
        $mas = $convocatoria->personals->where("id", "<>", $personal->id);

        return view("bolsa.show", compact('convocatoria', 'personal', 'mas', 'postulante', 'auth', 'types', 'current', 'idParse', 'isExpire'));
    }

    public function postular($numero, $titulo)
    {
        $ubigeos = Ubigeo::with(['departamentos' => function($d) {
            $d->with('provincias');
        }])->where("departamento_id", null)
            ->where("provincia_id", null)
            ->get();

        $convocatoria = Convocatoria::where("aceptado", 1)
            ->where("fecha_final", ">=", date('Y-m-d'))
            ->firstOrFail();

        $personal = $convocatoria->personals->where("aceptado", 1)->first();
        $year = isset(explode("-", $convocatoria->fecha_inicio)[0]) ? explode("-", $convocatoria->fecha_inicio)[0] : null;

        return view("bolsa.postular", compact('convocatoria', 'year', 'personal', 'ubigeos'));
    }

    public function authenticar(Request $request)
    {
        $this->validate(request(), [
            "numero_de_documento" => "required",
            "personal_id" => "required"
        ]);

        $postulante = Postulante::where("numero_de_documento", $request->numero_de_documento)->first();

        if ($postulante) {
            $idParse = base64_encode($postulante->id);
            Cache::put("postulante", $idParse, 600);

            $personal = $postulante->personals->where("id", $request->personal_id)->first();

            if ($personal) { 
                if ($request->redirect) {
                    return redirect($request->redirect . "?postulante={$idParse}");
                }
            }
        }

        return back()->with(["auth" => "EL postulante no está registrado"]);
    }


}
