<?php
/**
 * ./app/Http/Controllers/BolsaController.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Convocatoria;
use App\Models\Personal;
use App\Models\Ubigeo;
use App\Models\Postulante;
use App\Models\Etapa;
use App\Models\TypeEtapa;
use \PDF;
use Illuminate\Support\Facades\Cache;

/**
 * Class BolsaController
 * 
 * @category Controllers
 */
class BolsaController extends Controller
{
    /**
     * Muestra una lista de recursos
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $year = request()->input('year', date('Y'));

        $convocatorias = Convocatoria::with(['personals' => function($p) {
            $p->where('personals.aceptado', 1);
        }])->where("aceptado", 1)
            ->where("fecha_final", "<=", "$year-12-31")
            ->orderBy('fecha_final', 'DESC')->paginate(20);

        return view("bolsa.index", compact('year', 'convocatorias'));
    }


    /**
     * Muestra un recurso específico
     *
     * @param  string $slugConvocatoria
     * @param  string $slug
     * @return \Illuminate\View\View
     */
    public function show($slugConvocatoria, $slug)
    {

        //recuperar ids
        $convocatoriaID = \base64_decode($slugConvocatoria);

        $convocatoria = Convocatoria::findOrFail($convocatoriaID);
        $personal = Personal::where("slug", $slug)->where('aceptado', 1)->firstOrFail();
        $types = [];

        $idParse = request()->postulante ? \base64_decode(request()->postulante) : Cache::get('postulante');
        $postulante = Postulante::find($idParse);
        $auth = false;
        $current = "";


        if ($postulante) {
            $auth = true;
            $types = TypeEtapa::all();

            $current = Etapa::where("postulante_id", $postulante->id)
                ->where("personal_id", $personal->id)
                ->where("current", 1)
                ->first();

        }

        $isExpire = $convocatoria->fecha_final < date('Y-m-d') ? true : false;
        $mas = $convocatoria->personals->where("id", "<>", $personal->id)->where('aceptado', 1);

        return view("bolsa.show", compact('convocatoria', 'personal', 'mas', 'postulante', 'auth', 'types', 'current', 'idParse', 'isExpire'));
    }


    /**
     * Muestra un formulario de registro de postulacion
     *
     * @param  int $numero
     * @param  string $titulo
     * @return \Illuminate\Http\Response|Illuminate\View\View
     */
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

        if ($personal) {
            return view("bolsa.postular", compact('convocatoria', 'year', 'personal', 'ubigeos'));
        }

        return back();

    }


    /**
     * Valida y autentica a un postulante
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
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
            

            $personal = $postulante->etapas->where("personal_id", $request->personal_id)->first();

            if ($personal) { 
                if ($request->redirect) {
                    return redirect($request->redirect . "?postulante={$idParse}");
                }
            }
        }

        return back()->with(["auth" => "EL postulante no está registrado"]);
    }


    /**
     * Muestra un resultado determinado de las postulaciones
     *
     * @param  string $slugConvocatoria
     * @param  string $slugPersonal
     * @return PDF
     */
    public function resultados($slugConvocatoria, $slugPersonal)
    {

        $id = \base64_decode($slugConvocatoria);

        $convocatoria = Convocatoria::findOrFail($id);
        $year = isset(explode("-", $convocatoria->fecha_final)[0]) ? explode("-", $convocatoria->fecha_final)[0] : date('Y');
        $etapas = TypeEtapa::all();
        $current = Personal::where("slug", $slugPersonal)->firstOrFail();


        foreach ($etapas as $etapa) {
            $etapa->postulantes = Postulante::whereHas("etapas", function($e) use($current){
                        $e->where("personal_id", isset($current->id) ? $current->id : 0);
                    })->whereHas("etapas", function($e) use($etapa) {
                        $e->where("type_etapa_id", $etapa->id);
                    })->with(["etapas" => function($q) use($etapa) {
                        $q->where("etapas.type_etapa_id", $etapa->id);
                    }])->get(); 
        }
        
        $hasExpire = $convocatoria->fecha_final < date('Y-m-d') ? true : false;
        
        $pdf = PDF::loadView('pdf.resultados', compact('convocatoria', 'year', 'etapas', 'current', 'hasExpire'));
        return $pdf->stream();

    }


}
