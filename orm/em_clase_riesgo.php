<?php
namespace gamboamartin\empleado\models;
use base\orm\_modelo_parent;


use PDO;

class em_clase_riesgo extends _modelo_parent{

    public function __construct(PDO $link){
        $tabla = 'em_clase_riesgo';
        $columnas = array($tabla=>false);
        $campos_obligatorios = array('descripcion','codigo','descripcion_select','alias','codigo_bis','factor');

        $campos_view['codigo'] = array('type' => 'inputs');
        $campos_view['descripcion'] = array('type' => 'inputs');
        $campos_view['factor']['type'] = array('type' => 'inputs');


        parent::__construct(link: $link,tabla:  $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas,campos_view: $campos_view);

        $this->NAMESPACE = __NAMESPACE__;
    }


}