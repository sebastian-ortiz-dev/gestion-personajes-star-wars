<?php

namespace App\Helpers;

class Helpers{
    public function verificarAlturaMasa(string $altura, string $masa){
        if($altura == '' || $altura == "null" || $altura == 'unknown'){
            $alturaNew = null;
        }else{
            $alturaNew = (int) round((float) str_replace(',', '', trim($altura)));
        }

        if($masa == '' || $masa == "null" || $masa == 'unknown'){
            $masaNew = null;
        }else{
            $masaNew = (int) round((float) str_replace(',', '', trim($masa)));
        }

        return [$alturaNew, $masaNew];
    }
}