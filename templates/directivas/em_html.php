<?php
namespace html;

use gamboamartin\errores\errores;
use gamboamartin\system\html_controler;
use gamboamartin\system\system;
use stdClass;

class em_html extends html_controler {

    protected function asigna_inputs_base(system $controler, stdClass $inputs): array|stdClass
    {
        $controler->inputs->select = new stdClass();
        $controler->inputs->select->em_empleado_id = $inputs->selects->em_empleado_id;
        $controler->inputs->select->bn_sucursal_id = $inputs->selects->bn_sucursal_id;
        $controler->inputs->num_cuenta = $inputs->texts->num_cuenta;
        $controler->inputs->clabe = $inputs->texts->clabe;

        return $controler->inputs;
    }

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
