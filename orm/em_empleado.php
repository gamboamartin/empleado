<?php
namespace models;
use base\orm\modelo;
use PDO;

class em_empleado extends modelo{

    public function __construct(PDO $link){
        $tabla = __CLASS__;
        $columnas = array($tabla=>false, 'im_resgistro_patronal'=>$tabla, 'cat_sat_regimen_fiscal'=>$tabla,
            'dp_calle_pertenece'=>$tabla);
        $campos_obligatorios = array();

        parent::__construct(link: $link,tabla:  $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas);
    }
}