<?php

namespace App\Tools;

class Money {

    public static function parse($numero, $limite = 3) {
        $separador = explode(".", (String)$numero);
        $entero = isset($separador[0]) ? $separador[0] : 0;
        $decimal = (string)isset($separador[1]) ? $separador[1] : "00";

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
                   $newEntero .= ",$uni";
               }
            }

            return (String)"{$newEntero}.{$decimal}";
        }

        return (String)"{$entero}.{$decimal}";
    }

    public function parseTo($numero) {
        return static::parse($numero);
    }

}