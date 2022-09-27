<?php
namespace html;

use gamboamartin\empleado\controllers\controlador_em_cuenta_bancaria;
use gamboamartin\empleado\models\em_cuenta_bancaria;
use gamboamartin\errores\errores;
use gamboamartin\template\directivas;
use PDO;
use stdClass;

class em_cuenta_bancaria_html extends em_html {

    private function asigna_inputs(controlador_em_cuenta_bancaria $controler, stdClass $inputs): array|stdClass
    {
        $controler->inputs->select = new stdClass();
        $controler->inputs->select->bn_sucursal_id = $inputs->selects->bn_sucursal_id;
        $controler->inputs->select->em_empleado_id = $inputs->selects->em_empleado_id;
        $controler->inputs->id = $inputs->texts->id;
        $controler->inputs->codigo = $inputs->texts->codigo;
        $controler->inputs->clabe = $inputs->texts->clabe;
        $controler->inputs->num_cuenta = $inputs->texts->num_cuenta;

        return $controler->inputs;
    }

    public function genera_inputs(controlador_em_cuenta_bancaria $controler, array $keys_selects = array()): array|stdClass
    {
        $inputs = $this->init_alta2(row_upd: $controler->row_upd, modelo: $controler->modelo, link: $controler->link,
            keys_selects:$keys_selects);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar inputs',data:  $inputs);
        }

        $inputs_asignados = $this->asigna_inputs(controler:$controler, inputs: $inputs);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al asignar inputs',data:  $inputs_asignados);
        }

        return $inputs_asignados;
    }

    public function select_em_cuenta_bancaria_id(int $cols, bool $con_registros, int $id_selected, PDO $link,
                                                 array $filtro = array()): array|string
    {
        $valida = (new directivas(html:$this->html_base))->valida_cols(cols:$cols);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar cols', data: $valida);
        }
        if(is_null($id_selected)){
            $id_selected = -1;
        }
        $modelo = new em_cuenta_bancaria(link: $link);

        $select = $this->select_catalogo(cols:$cols,con_registros:$con_registros,id_selected:$id_selected,
            modelo: $modelo,filtro: $filtro, label: 'Cuenta bancaria',required: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar select', data: $select);
        }
        return $select;
    }

}
