<?php
namespace gamboamartin\empleado\models;
use base\orm\modelo;
use PDO;


class em_cuenta_bancaria extends modelo{

    public function __construct(PDO $link){
        $tabla = 'em_cuenta_bancaria';
        $columnas = array($tabla=>false, 'em_empleado'=>$tabla,'bn_sucursal'=>$tabla,'bn_banco'=>'bn_sucursal');
        $campos_obligatorios = array('bn_sucursal_id','em_empleado_id','descripcion_select');

        parent::__construct(link: $link,tabla:  $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas);
    }


}