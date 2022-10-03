<?php
namespace gamboamartin\empleado\models;
use base\orm\modelo;

use PDO;

class em_metodo_calculo extends modelo{

    public function __construct(PDO $link){
        $tabla = 'em_metodo_calculo';
        $columnas = array($tabla=>false);
        $campos_obligatorios = array('descripcion','codigo','descripcion_select','alias','codigo_bis');

        parent::__construct(link: $link,tabla:  $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas);

        $this->NAMESPACE = __NAMESPACE__;
    }
}