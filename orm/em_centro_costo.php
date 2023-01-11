<?php
namespace gamboamartin\empleado\models;
use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class em_centro_costo extends _modelo_parent{
    public function __construct(PDO $link){
        $tabla = 'em_centro_costo';
        $columnas = array($tabla=>false,"em_empleado" => $tabla);
        $campos_obligatorios[] = 'descripcion';

        parent::__construct(link: $link,tabla:  $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas);

        $this->NAMESPACE = __NAMESPACE__;
    }
}