<?php
namespace html;

use gamboamartin\empleado\controllers\controlador_em_empleado;
use gamboamartin\errores\errores;
use gamboamartin\template\directivas;
use gamboamartin\empleado\models\em_empleado;
use PDO;
use stdClass;

class em_empleado_html extends em_html {

    private function asigna_inputs(controlador_em_empleado $controler, stdClass $inputs): array|stdClass
    {
        $controler->inputs->select = new stdClass();
        $controler->inputs->select->dp_calle_pertenece_id = $inputs->selects->dp_calle_pertenece_id;
        $controler->inputs->select->cat_sat_regimen_fiscal_id = $inputs->selects->cat_sat_regimen_fiscal_id;
        $controler->inputs->select->im_registro_patronal_id = $inputs->selects->im_registro_patronal_id;
        $controler->inputs->select->org_puesto_id = $inputs->selects->org_puesto_id;
        $controler->inputs->select->cat_sat_tipo_regimen_nom_id = $inputs->selects->cat_sat_tipo_regimen_nom_id;
        $controler->inputs->select->em_tipo_anticipo_id = $inputs->selects->em_tipo_anticipo_id;
        $controler->inputs->select->em_empleado_id = $inputs->selects->em_empleado_id;
        $controler->inputs->select->bn_sucursal_id = $inputs->selects->bn_sucursal_id;
        $controler->inputs->codigo = $inputs->texts->codigo;
        $controler->inputs->nombre = $inputs->texts->nombre;
        $controler->inputs->ap = $inputs->texts->ap;
        $controler->inputs->am = $inputs->texts->am;
        $controler->inputs->telefono = $inputs->texts->telefono;
        $controler->inputs->rfc = $inputs->texts->rfc;
        $controler->inputs->curp = $inputs->texts->curp;
        $controler->inputs->nss = $inputs->texts->nss;
        $controler->inputs->fecha_inicio_rel_laboral = $inputs->dates->fecha_inicio_rel_laboral;
        $controler->inputs->salario_diario = $inputs->texts->salario_diario;
        $controler->inputs->salario_diario_integrado = $inputs->texts->salario_diario_integrado;
        $controler->inputs->monto = $inputs->texts->monto;
        $controler->inputs->fecha_prestacion = $inputs->dates->fecha_prestacion;
        $controler->inputs->num_cuenta = $inputs->texts->num_cuenta;
        $controler->inputs->clabe = $inputs->texts->clabe;

        return $controler->inputs;
    }

    public function genera_inputs(controlador_em_empleado $controler, array $keys_selects = array()): array|stdClass
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

    /**
     * Genera un input de tipo empleado
     * @param int $cols N columnas css
     * @param bool $con_registros si con registros deja el input con rows para options
     * @param int $id_selected identificador selected
     * @param PDO $link conexion a la bd
     * @param array $filtro filtro para obtencion de datos
     * @param bool $disabled si disabled el input queda disabled
     * @return array|string
     */
    public function select_em_empleado_id(int $cols, bool $con_registros, mixed $id_selected, PDO $link,
                                          array $filtro = array(), bool $disabled = false): array|string
    {
        $valida = (new directivas(html:$this->html_base))->valida_cols(cols:$cols);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar cols', data: $valida);
        }
        if(is_null($id_selected)){
            $id_selected = -1;
        }

        $modelo = new em_empleado(link: $link);

        $extra_params_keys[] = 'em_empleado_id';
        $extra_params_keys[] = 'em_empleado_rfc';
        $extra_params_keys[] = 'em_empleado_curp';
        $extra_params_keys[] = 'em_empleado_nss';
        $extra_params_keys[] = 'em_empleado_salario_diario';
        $extra_params_keys[] = 'em_empleado_salario_diario_integrado';
        $extra_params_keys[] = 'em_empleado_fecha_inicio_rel_laboral';
        $extra_params_keys[] = 'em_empleado_org_puesto_id';

        $select = $this->select_catalogo(cols:$cols,con_registros:$con_registros,id_selected:$id_selected,modelo: $modelo,
            disabled: $disabled,extra_params_keys:$extra_params_keys,filtro: $filtro,label: 'Empleado',required: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar select', data: $select);
        }
        return $select;
    }


}
