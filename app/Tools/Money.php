<?php

namespace App\Tools;

class Money {

    private static $separate = true;

    /**
     * cambiar configuracion del separador
     *
     * @param [type] $config
     * @return void
     */
    public static function setSeparate($config = true) 
    {
        static::$separate = $config;
        return new self;
    }

    /**
     * Generar formato de cambio
     *
     * @param [type] $numero
     * @param integer $limite
     * @return void
     */
    public static function parse($numero, $limite = 3) {
        $separador = explode(".", (String)$numero);
        $entero = isset($separador[0]) ? $separador[0] : 0;
        $decimal = (string)isset($separador[1]) ? $separador[1] : "00";

        // configurar decimal
        $newDecimal = strlen($decimal) < 2 ? "{$decimal}0" : $decimal;

        // configurar entero
        if (strlen($entero) > 3) {
            $ceros = "";
            $count_ceros = 0;
            $array_enteros = str_split($entero, $limite);
            
            // averiguar cuantos ceros se van agregar
            foreach ($array_enteros as $tmp_entero) {
                $count = $limite - strlen($tmp_entero);
                if ($count > 0) {
                    $count_ceros += $count;
                }
            }
            
            // agregar los ceros
            for($iter = 0; $iter < $count_ceros; $iter++) {
                $ceros .= "0";
            }

            $union = str_split($ceros . $entero, $limite);
            $newEntero = "";

            foreach ($union as $key => $uni) {
               if ($key == 0) {
                    $newEntero .= (int)$uni;
               }else {
                   $separate = self::$separate ? "," : "";
                   $newEntero .= "{$separate}{$uni}";
               }
            }

            return (String)"{$newEntero}.{$newDecimal}";
        }

        return (String)"{$entero}.{$newDecimal}";
    }

    /**
     * Ejecutar formato de un objecto instanciado
     *
     * @param [type] $numero
     * @return void
     */
    public function parseTo($numero) {
        return static::parse($numero);
    }

}