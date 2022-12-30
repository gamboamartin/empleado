<?php
namespace gamboamartin\empleado\models;
use base\orm\modelo;

use PDO;

class em_metodo_calculo extends modelo{

    public function __construct(PDO $link){
        $tabla = 'em_metodo_calculo';
        $columnas = array($tabla=>false);
        $campos_obligatorios = array('descripcion','codigo');

        $campos_view['codigo'] = array('type' => 'inputs');
        $campos_view['descripcion'] = array('type' => 'inputs');

        parent::__construct(link: $link,tabla:  $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas,campos_view: $campos_view);

        $this->NAMESPACE = __NAMESPACE__;
    }
}