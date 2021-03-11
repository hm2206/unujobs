<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Work;
use App\Models\Historial;

class ConsultaController extends Controller
{
    
    public function index() 
    {
        return view('consultas.index');
    }

    public function validar(Request $request) 
    {
        $this->validate(request(), [
            "tipo_de_documento" => "required|max:10",
            "numero_de_identidad" => "required|min:8|max:20"
        ]);

        $work = Work::where('tipo_documento_id', $request->tipo_de_documento)
            ->where('numero_de_documento', $request->numero_de_identidad)
            ->first();

        if (!$work) {
            return back()->with('error', 'El trabajador no existe!');
        }

        return \redirect()->route('consulta.work', $work->slug());

    }


    public function work($slug)
    {
        $id = \base64_decode($slug);
        $work = Work::findOrFail($id);
        $meses = ["ENE", "FEB", "MAR", "ABR", "MAY", "JUN", "JUL", "AGO", "SEP", "OCT", "NOV", "DIC"];
        $year = request()->input('year', date('Y'));
        $historial = Historial::where('work_id', $work->id)
            ->whereHas('cronograma', function($cro) use($year) {
                $cro->where('año', $year);
            })->get();

        return view('consultas.work', compact('work', 'meses', 'year', 'historial'));
    }

}
