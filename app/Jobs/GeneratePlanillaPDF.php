<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Exports\PlanillaExport;
use App\Models\TypeRemuneracion;
use App\Models\TypeCategoria;
use App\Models\TypeDescuento;
use App\Models\Cargo;
use App\Models\Cronograma;
use App\Models\Remuneracion;
use App\Models\Descuento;
use App\Models\Afp;
use App\Models\Work;
use App\Notifications\ReportNotification;
use \PDF;
use App\Models\User;


class GeneratePlanillaPDF implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $cronograma;

    public function __construct($cronograma)
    {
        $this->cronograma = $cronograma;
    }


    public function handle()
    {
        $meses = [
            "Enero", "Febrero", "Marzo", "Abril", 
            "Mayo", 'Junio', 'Julio', 'Agosto', 
            'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];

        $cronograma = $this->cronograma;

        $type_remuneraciones = TypeRemuneracion::all();
        $type_categorias = TypeCategoria::with('cargos')->get();
        $type_descuentos = TypeDescuento::where("config_afp", null)->get();
        $remuneraciones = Remuneracion::where('cronograma_id', $cronograma->id)->get();
        $descuentos = Descuento::where('cronograma_id', $cronograma->id)->get();
        $afps = Afp::all();
        $works = Work::whereIn("id", $remuneraciones->pluck(['work_id']))->get();

        $results = collect();
        $resumen = collect();
        $totales = 0;
        $rows = 0;

        foreach ($type_remuneraciones as $type) {

            $cats = collect();
            $total = 0;

            foreach($type_categorias as $categoria) {

                $tags = collect();

                foreach ($categoria->cargos as $cargo) {
                    $tmp = $remuneraciones->where('cargo_id', $cargo->id)->where('type_remuneracion_id', $type->id)->sum('monto');

                    $tags->push([
                        "id" => $cargo->id,
                        "descripcion" => $cargo->descripcion,
                        "monto" => $tmp
                    ]);

                    $total += $tmp;
                }

                $cats->push([
                    "id" => $categoria->id,
                    "descripcion" => $categoria->descripcion,
                    "tags" => $tags
                ]);
            }

            $results->push([
                "id" => $type->id,
                "descripcion" => $type->descripcion,
                "categorias" => $cats,
                "total" => $total
            ]);
        }

        foreach ($type_categorias as $categoria) {
            $cars = collect();
            $suma = 0;

            foreach ($categoria->cargos as $cargo) {
                $suma = $remuneraciones->where('cargo_id', $cargo->id)->sum('monto');
                $cars->push([
                    "id" => $cargo->descripcion,
                    "total" => $suma 
                ]);

                $totales += $suma;
            }

            $resumen->push([
                "id" => $categoria['id'],
                "descripcion" => $categoria['descripcion'],
                "cargos" => $cars
            ]);
        }

        foreach ($type_descuentos as $desc) {
            $desc->monto = $descuentos->where("type_descuento_id", $desc->id)->sum('monto');
        }

        foreach($afps as $afp) {
            $whereIn = TypeDescuento::where("config_afp", "<>", null)->get();
            $desct = Descuento::where("cronograma_id", $cronograma->id)
                ->whereIn("type_descuento_id", $whereIn->pluck(['id']))
                ->whereIn("work_id", $works->where("afp_id", $afp->id)->pluck(['id']))
                ->get();
            $afp->monto = $desct->sum('monto');
        }

        $total_descuentos = $afps->sum('monto') + $type_descuentos->sum('monto');
        $total_essalud = 0;
        $total_ies = 0;
        $total_dlfp = 0;
        $total_accidentes = 0;

        foreach ($works as $work) {
            $tmp_essalud = 0;
            $tmp_accidente = 0;
            $base = 0;
            $remuneraciones = Remuneracion::where("cronograma_id", $cronograma->id)
                ->where("work_id", $work->id)
                ->where('base', 0)
                ->get();

            $base = $remuneraciones->sum('monto');

            //aportaciones current
            $tmp_essalud = $base < 930 ? 83.7 : $base * 0.09;
            $tmp_accidente = $work->accidentes ? ($base * 1.55) / 100 : 0;

            //totales
            $total_essalud += $tmp_essalud;        
            $total_accidentes += $tmp_accidente;
        }

        //calcular total de aportaciones
        $total_aportaciones = $total_accidentes + $total_essalud + $total_dlfp + $total_ies;


        $sub_titulo = "RESUMEN GENERAL DE TODAS LAS METAS DE MES " . $meses[$cronograma->mes - 1] . " - " . $cronograma->año;
        $titulo = "";


        $pdf = PDF::loadView('pdf.cronograma', \compact(
            'type_remuneraciones', 'results', 'resumen', 
            'meses', 'cronograma', 'type_categorias',
            'totales', 'type_descuentos', 'rows', 
            'sub_titulo','titulo', 'afps', 'total_descuentos',
            'total_essalud', 'total_ies', 'total_dlfp', 
            'total_accidentes', 'total_aportaciones'
        ));


        $pdf->setPaper('a4', 'landscape')->setWarnings(false);
        $ruta = "/pdf/planilla_{$cronograma->id}_{$cronograma->mes}_{$cronograma->año}.pdf";
        $pdf->save(storage_path('app/public') . $ruta);
        $cronograma->update(["pdf" => "storage{$ruta}", "pendiente" => 0]);

        $users = User::all();

        foreach ($users as $user) {
            $user->notify(new ReportNotification("storage" . $ruta, "La Planilla general se genero correctamente"));
        }


    }

}
