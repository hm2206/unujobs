<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;

use App\Models\Postulante;
use App\Models\Etapa;
use App\Models\TypeEtapa;
use \DB;

class EtapaImport implements ToCollection, WithHeadingRow
{

    use Importable;

    private $type;
    private $name;
    private $personal;

    /**
     * @param \App\Models\TypeEtapa $etapa
     * @param \App\Models\Personal $personal
     * @param string $name
     */
    public function __construct($etapa, $personal, $name)
    {
        $this->etapa = $etapa;
        $this->personal = $personal;
        $this->name = $name;
    }


    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {

        $config = [
            "type" => $this->etapa->id,
            "personal" => $this->personal->id
        ];
        
        $postulantes = Postulante::whereHas("etapas", function($e) use($config) {
                $e->where("etapas.type_etapa_id", $config['type']);
                $e->where("etapas.personal_id", $config['personal']);
            })->get();

        foreach ($collection as $row) {
            
            if ( $postulantes->count() > 0 ) {

                $postulante = $postulantes->where("numero_de_documento", $row['numero_de_documento'])->first();

                if ($postulante) {

                    $etapa = Etapa::updateOrCreate([
                        "postulante_id" => $postulante->id,
                        "type_etapa_id" => $this->etapa->id,
                        "convocatoria_id" => $this->personal->convocatoria_id,
                        "personal_id" => $this->personal->id
                    ]);

                    $etapa->update([
                        "current" => 0,
                        "puntaje" => isset($row['puntaje']) ? (int)$row['puntaje'] : 0,
                        "next" => isset($row['continua']) ? (int)$row['continua'] : 0,
                    ]);

                    if ($etapa->monto > 0 && $etapa->next) {

                        $tmp_type = TypeEtapa::where("id", ">", $request->type_etapa_id)->first();

                        if ($tmp_type) {

                            $newEtapa = Etapa::create([
                                "postulante_id" => $postulante->id,
                                "type_etapa_id" => $this->type->id,
                                "convocatoria_id" => $this->personal->convocatoria_id,
                                "personal_id" => $this->personal->id,
                                "current" => 0,
                                "next" => 0,
                                "puntaje" => 0
                            ]);
                        }

                    }else {

                        $eliminar = DB::table('etapas')->where("postulante_id", $postulante->id)
                        ->where("personal_id", $this->personal->id)
                        ->orderBy('id', 'DESC')
                        ->where('type_etapa_id', '<>', $this->etapa->id)
                        ->where('type_etapa_id', '>', $this->etapa->id)
                        ->delete();

                    }

                    //recalcular current
                    $current = Etapa::where("postulante_id", $postulante->id)
                        ->where("convocatoria_id", $this->personal->convocatoria_id)
                        ->where("personal_id", $this->personal->id)
                        ->orderBy("type_etapa_id", 'DESC')
                        ->first();

                    if ($current) {
                        $current->update(["current" => 1]);
                    }

                }

            }

        }

    }
}
