<?php
/**
 * Http/Controllers/EtapaController.php
 * 
 * @author Hans <twd2206@gmail.com>
 */
namespace App\Http\Controllers;

use App\Models\Etapa;
use Illuminate\Http\Request;
use App\Models\TypeEtapa;
use App\Models\Convocatoria;
use \DB;
use \PDF;
use App\Models\Postulante;

/**
 * Class EtapaController
 * 
 * @category Controllers
 */
class EtapaController extends Controller
{

    /**
     * Undocumented function
     *
     * @return void
     */
    public function index()
    {
        return back();
    }

    
    /**
     * Undocumented function
     *
     * @return void
     */
    public function create()
    {
        return back();
    }


    /**
     * Almacena una etapa recien creada
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        $this->validate(request(), [
            "type_etapa_id" => "required",
            "convocatoria_id" => "required",
            "personal_id" => "required"
        ]);

        foreach ($request->input('postulantes', []) as $key => $pos) {

            $tmp_postulante = isset($pos[0]) ? $pos[0] : 0;
            $tmp_puntaje = isset($pos[1]) ? $pos[1] : 0;
            $tmp_next = isset($pos[2]) ? 1 : 0;

            if($tmp_postulante) {

                // Set up para etapas de usuario actual
                $tmp_etapas = Etapa::where("postulante_id", $tmp_postulante)
                    ->where("convocatoria_id", $request->convocatoria_id)
                    ->where("personal_id", $request->personal_id);


                // Obteniendo la etapa actual del postulante
                $etapa = $tmp_etapas->where("type_etapa_id", $request->type_etapa_id)->first();

                //verificamos que el postulate este en la base de datos
                if ($etapa) {

                    // actualizando la etapa actual
                    $etapa->update([
                        "puntaje" => $tmp_puntaje,
                        "next" => $tmp_puntaje > 0 ? $tmp_next : 0,
                        "current" => 0
                    ]);

                }else {
                    // creamos una nueva etapa
                    $etapa = Etapa::create([
                        "postulante_id" => $tmp_postulante,
                        "convocatoria_id" => $request->convocatoria_id,
                        "personal_id" => $request->personal_id,
                        "type_etapa_id" => $request->type_etapa_id,
                        "puntaje" => $tmp_puntaje,
                        "next" => $tmp_next,
                        "current" => 0
                    ]);
                }


                if ($etapa->next) {

                    $type = TypeEtapa::where("id", ">", $request->type_etapa_id)->first();

                    $newEtapa = Etapa::create([
                        "postulante_id" => $tmp_postulante,
                        "type_etapa_id" => $type->id,
                        "convocatoria_id" => $request->convocatoria_id,
                        "personal_id" => $request->personal_id,
                        "current" => 0,
                        "next" => 0,
                        "puntaje" => 0
                    ]);

                }else {

                    $eliminar = DB::table('etapas')->where("postulante_id", $tmp_postulante)
                        ->where("personal_id", $etapa->personal_id)
                        ->orderBy('id', 'DESC')
                        ->where('type_etapa_id', '<>', $request->type_etapa_id)
                        ->where('type_etapa_id', '>', $request->type_etapa_id)
                        ->delete();
                }

                //recalcular current
                $current = Etapa::where("postulante_id", $tmp_postulante)
                                ->where("convocatoria_id", $request->convocatoria_id)
                                ->where("personal_id", $request->personal_id)
                                ->orderBy("type_etapa_id", 'DESC')
                                ->first();

                $current->update([
                    "current" => 1
                ]);

            }

        }

        return back()->with(["Los datos se guardarón correctamente"]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Etapa  $etapa
     * @return \Illuminate\Http\Response
     */
    public function show(Etapa $etapa)
    {
        return back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Etapa  $etapa
     * @return \Illuminate\Http\Response
     */
    public function edit(Etapa $etapa)
    {
        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Etapa  $etapa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Etapa $etapa)
    {
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Etapa  $etapa
     * @return \Illuminate\Http\Response
     */
    public function destroy(Etapa $etapa)
    {
        return back();
    }


    /**
     * Genera un archivo PDF de la etapa
     *
     * @param  string  $slug
     * @param  string  $slugConvocatoria   
     * @return \PDF
     */
    public function pdf($slug, $slugConvocatoria)
    {
        //recuperar id
        $id = \base64_decode($slug);
        $convocatoriaID = \base64_decode($slugConvocatoria);


        $etapa = TypeEtapa::findOrFail($id);
        $convocatoria = Convocatoria::findOrFail($convocatoriaID);

        $year = isset(explode("-", $convocatoria->fecha_final)[0]) ? explode("-", $convocatoria->fecha_final)[0] : date('Y');

        $personals = $convocatoria->personals;

        foreach ($personals as $key => $personal) {

            $personal->postulantes = Postulante::whereHas("etapas", function($e) use($personal){
                                        $e->where("personal_id", $personal->id);
                                    })->whereHas("etapas", function($e) use($etapa) {
                                        $e->where("type_etapa_id", $etapa->id);
                                    })->with(['etapas' => function($q) use($etapa) {
                                        $q->where('etapas.type_etapa_id', $etapa->id);
                                    }])->get(); 

        }

        $pdf = PDF::loadView('pdf.etapa', compact('convocatoria', 'etapa', 'year', 'personals'));
        return $pdf->stream();
    }
}
