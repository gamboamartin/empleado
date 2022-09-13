<?php
namespace html;

use gamboamartin\errores\errores;
use gamboamartin\system\html_controler;
use stdClass;

class em_html extends html_controler {

    public function input_clabe(int $cols, stdClass $row_upd, bool $value_vacio, bool $disable = false): array|string
    {
        $valida = $this->directivas->valida_cols(cols: $cols);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar columnas', data: $valida);
        }

        $html =$this->directivas->input_text_required(disable: $disable,name: 'clabe',place_holder: 'Clabe',
            row_upd: $row_upd, value_vacio: $value_vacio);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input', data: $html);
        }

        $div = $this->directivas->html->div_group(cols: $cols,html:  $html);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar div', data: $div);
        }

        return $div;
    }

}
