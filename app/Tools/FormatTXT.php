<?php

namespace App\Tools;
use Illuminate\Support\Facades\Storage;
use App\Tools\Money;


class FormatTXT 
{

    private static $header = [];
    private static $body = "";
    private static $disk = "public";
    private static $path = "";
    private static $prefix = "";

    /**
     * Inicializar el formato para generar el formato de texto
     *
     * @return void
     */
    public static function init() {
        self::$body = "";
        self::$header = [
            "unidad" => "000112",
            "year" => date('Y'),
            "mes" => date('m'),
            "activo" => "01",
            "planilla" => "01",
            "normal" => "0001",
            "count" => "",
            "total" => "",
            "formato" => "",
        ];

        return new self;
    }


    /**
     * Configurar la cabezera del formato (mes y year)
     *
     * @param [type] $year
     * @param [type] $mes
     * @return void
     */
    public function setHeader($year, $mes) 
    {
        self::$header["year"] = $year;
        self::$header["mes"] = (int)$mes < 10 ? "0{$mes}" : $mes; 
        return $this;
    }


    /**
     * configurar planilla de la cabezera del formato
     *
     * @param integer $plan
     * @param boolean $activo
     * @return void
     */
    public function setPlanilla(int $plan)
    {
        $validate = $plan < 10 ? "0{$plan}" : $plan;
        self::$header["planilla"] = $validate;
        return $this;
    } 

    /**
     * configurar el activo de la cabezera
     *
     * @param integer $act
     * @return void
     */
    public function setActivo(int $act)
    {
        $validate = $act < 10 ? "0{$act}" : $act;
        self::$header["activo"] = $validate;
        return $this;
    }


    /**
     * Configurar normal
     *
     * @param integer $normal
     * @return void
     */
    public function setNormal(int $normal)
    {
        self::$header['normal'] = "000{$normal}";
        return $this;
    }

    /**
     * configurar el prefijo del archivo
     *
     * @param [type] $pref
     * @return void
     */
    public function setPrefix($pref) 
    {   
        self::$prefix = $pref;
        return $this;
    }

    /**
     * Generar nombre del archivo
     *
     * @param [type] $path
     * @return void
     */
    public function generateFile($path)
    {
        $file = "PLL";
        $file .= self::$header['unidad'];
        $file .= self::$header['year'];
        $file .= self::$header['mes'];
        $file .= self::$header['activo'];
        $file .= self::$header['planilla'];
        $file .= self::$header['normal'];
        // agregar prefijo
        $file = self::$prefix ? self::$prefix . "_{$file}" : $file;
        // guardar ruta
        self::$path = "{$path}/{$file}.TXT";
        // devolver objecto
        return $this;
    }


    /**
     * configurar content del formato del archivo
     *
     * @param array $storage
     * @return void
     */
    public function setBody(array $storage = [array("numero_de_documento" => "", "monto" => "0.00")]) 
    {   
        try {

            $total = 0;
            $count = 0;

            foreach ($storage as $store) {
                $monto = $store['monto'];
                self::$body .= $this->format($store['numero_de_documento'], $monto) . "\n";
                self::$body .= $this->format($store['numero_de_documento'], $monto , "1") . "\n";
                $total += $monto;
            } 

            self::$header["count"] = count($storage) * 2;
            self::$header["total"] = Money::setSeparate(false)->parseTo($total);
            self::$header["formato"] = "0.00;0.00;0.00;0.00;0.00";

            return $this;

        } catch (\Throwable $th) {
            throw new \Exception($th);
        }
    }


    /**
     * personalizar el total
     *
     * @param [type] $total
     * @return void
     */
    public function setTotal($total) 
    {
        self::$header['total'] = $total;
        return $this;
    }


    /**
     * Generar formato del texto
     *
     * @param [type] $dni
     * @param [type] $monto
     * @param string $condicion
     * @return void
     */
    private function format($dni, $monto, $condicion = "9") {
        // validar condiciones permitidas
        $validate = $condicion == "9" || $condicion == "1" ? true : false;
        // verificar si todo esta correcto
        if ($validate) {
            $valor = $condicion == '9' ? '9999' : '0000';
            $money = Money::setSeparate(false)->parseTo($monto);
            $content = [
                "type" => "01", 
                "dni" => $dni, 
                "default" => "00", 
                "condicion" => $condicion, 
                "valor" => $valor, 
                "monto" => ";{$money}"
            ];
            return implode(";", $content);
        }
        // generar error
        throw new \Exception("la condición no existe", 1);
    }

    
    /**
     * Generar salida 
     *
     * @return void
     */
    public function output() 
    {
        $union = [
            implode(";", self::$header),
            self::$body,
        ];

        $content = implode("\n", $union);

        return $content;
    } 

    /**
     * Guardar salida en un archivo
     *
     * @param string $path
     * @param string $disk
     * @return void
     */
    public function save($path = null, $disk = "public") 
    {
        if ($path) {
            self::$path = $path;
        }

        self::$disk = $disk;

        return Storage::disk(self::$disk)->put(self::$path, $this->output());
    }


    /**
     * Generar descarga
     *
     * @param [type] $path
     * @param string $disk
     * @return void
     */
    public function download($path = null, $disk = "public")
    {
        if ($path) {
            self::$path = $path;
        }

        self::$disk = $disk;

        $this->save(self::$path, self::$disk);
        return Storage::disk(self::$disk)->download(self::$path);
    }


    /**
     * Obtener la ruta del archivo
     *
     * @return void
     */
    public function getPath()
    {
        return self::$path;
    }


    public function stream()
    {
        $this->save(self::$path, self::$disk);
        return Storage::disk(self::$disk)->get(self::$path);
    }

}