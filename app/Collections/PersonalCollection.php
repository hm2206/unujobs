<?php

namespace App\Collections;


use \PDF;
use App\Models\User;
use App\Models\Info;
use App\Models\Work;
use App\Models\Historial;
use App\Models\Remuneracion;
use App\Models\Descuento;
use App\Models\Report;
use App\Models\TypeDescuento;
use App\Notifications\ReportNotification;
use App\Tools\Money;
use App\Models\Cronograma;

class PersonalCollection
{

    private $year;
    private $mes;
    private $storage = [];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($year, $mes)
    {
        $this->year = $year;
        $this->mes = $mes;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function generate()
    {
        $year = $this->year;
        $mes = $this->mes;
        $pages = [];
        // config totales
        $beforeBruto = 0;
        $beforeX43 = 0;
        $beforeSNP = 0;
        $beforeRenta = 0;
        $beforeEssalud = 0;
        $beforeIES = 0;
        $beforeDLFP = 0;
        $beforeACCIDENTES = 0;
        // config otros
        $num_page = 1;
        $last_page = 1;
        $money = new Money;

        $meses = [
            "Enero", "Febrero", "Marzo", "Abril", 
            "Mayo", 'Junio', 'Julio', 'Agosto', 
            'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];

        // obtener cronogramas
        $cronogramas = Cronograma::where("año", $year)->where("mes", $mes)->get();

        // configurar descuentos
        $typeDescuentos = TypeDescuento::whereIn("key", ["43", "31", "49"])->get();
        $descuentos = Descuento::where("type_descuento_id", $typeDescuentos->pluck(['id']))
            ->whereIn("cronograma_id", $cronogramas->pluck(['id']))
            ->get();

        // configuracion de aportaciones
        $typeAportes = TypeDescuento::where('base', 1)->get();
        $aportes = Descuento::whereIn("type_descuento_id", $typeAportes->pluck(['id']))
            ->whereIn('cronograma_id', $cronogramas->pluck(['id']))
            ->get();

        // obtener remuneraciones
        $remuneraciones = Remuneracion::whereIn("cronograma_id", $cronogramas->pluck(['id']))->get();
        
        // obtener trabajadores
        $historial = Historial::whereIn("cronograma_id", $cronogramas->pluck(['id']))
            ->orderBy('orden', 'ASC')->get();

        foreach ($historial as $history) {
            $history->monto_bruto = $remuneraciones->where("historial_id", $history->id)->sum('monto');
            //obtener x43
            $history->x43 = $descuentos->where("historial", $history->id)
                ->whereIn("type_descuento_id", $typeDescuentos->where('key', '43')->pluck(['id']))
                ->sum('monto');
            // obtener snp
            $history->snp = $descuentos->where("historial_id", $history->id)
                ->whereIn("type_descuento_id", $typeDescuentos->where('key', '31')->pluck(['id']))
                ->sum('monto');
            // obtener renta
            $history->renta = $descuentos->where("historial_id", $history->id)
                ->whereIn("type_descuento_id", $typeDescuentos->where('key', '49')->pluck(['id']))
                ->sum('monto');
            // obtener essalud
            $history->essalud = $descuentos->where("historial_id", $history->id)
                ->whereIn("type_descuento_id", $typeAportes->where('key', '80')->pluck(['id']))
                ->sum('monto');
            // obtener i.e.s
            $history->ies = $descuentos->where("historial_id", $history->id)
                ->whereIn("type_descuento_id", $typeAportes->where('key', '81')->pluck(['id']))
                ->sum('monto');
            // obtener dlfp
            $history->dlfp = $descuentos->where("historial_id", $history->id)
                ->whereIn("type_descuento_id", $typeAportes->where('key', '82')->pluck(['id']))
                ->sum('monto');
            // obtener accidentes
            $history->accidentes = $descuentos->where("historial_id", $history->id)
                ->whereIn("type_descuento_id", $typeAportes->where('key', '83')->pluck(['id']))
                ->sum('monto');
        }
        
        $pages =  $historial->chunk(48);

        $this->storage = compact(
                'year', 'mes', 'pages', 'meses', 'money', 
                'num_page', 'last_page', 'beforeBruto',
                'beforeX43', 'beforeSNP','beforeRenta', 
                'beforeEssalud', 'beforeIES','beforeDLFP', 
                'beforeACCIDENTES', 'typeAportes'
        );

    }


    public function getStorage()
    {
        return $this->storage;
    }


    public function render()
    {
        return view("reports.personal", $this->storage);
    }
}
