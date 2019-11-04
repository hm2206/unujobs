<?php

use Illuminate\Database\Seeder;
use App\Models\Work;
use App\Tools\Essalud;

class ValidarDatosReniec extends Seeder
{


    public $timeout = 0;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $works = Work::all();
        // recorremos y actualizamos la fecha de nacimiento
        foreach ($works as $work) {
            // obtener datos de reniec
            $essalud = new Essalud();
            $result = $essalud->search($work->numero_de_documento);
            // validar resultados
            if ($result) {
                // obtener fecha de nacimiento
                $newNacimiento = $result->result ? $result->result->nacimiento_parse : $work->fecha_de_nacimiento;
                if ($newNacimiento) {
                    // actualizar fecha de nacimiento del trabajador
                    $work->update([
                        "fecha_de_nacimiento" => $newNacimiento
                    ]);
                }
            }
        }

    }
}
