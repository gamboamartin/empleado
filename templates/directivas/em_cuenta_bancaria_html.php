<?php
namespace html;

use gamboamartin\empleado\models\em_cuenta_bancaria;
use gamboamartin\errores\errores;
use gamboamartin\template\directivas;
use PDO;

class em_cuenta_bancaria_html extends em_html {


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
