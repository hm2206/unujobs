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
use \PDF;


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
        $type_descuentos = TypeDescuento::all();
        $remuneraciones = Remuneracion::where('cronograma_id', $cronograma->id)->get();

        $results = collect();
        $resumen = collect();
        $totales = 0;
        $rows = 0;
        $rows = (int)$type_descuentos->count() - (int)$type_remuneraciones->count();

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


        $pdf = PDF::loadView('pdf.cronograma', \compact(
            'type_remuneraciones', 'results', 'resumen', 
            'meses', 'cronograma', 'type_categorias',
            'totales', 'type_descuentos', 'rows'
        ));

        $pdf->setPaper('a4', 'landscape')->setWarnings(false);
        $ruta = "/pdf/planilla_{$cronograma->id}_{$cronograma->mes}_{$cronograma->año}.pdf";
        $pdf->save(public_path() . $ruta);
        $cronograma->update(["pdf" => $ruta, "pendiente" => 0]);

    }

}
