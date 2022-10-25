<?php
namespace gamboamartin\empleado\models;
use base\orm\modelo;

use PDO;

class em_emp_dir_pendiente extends modelo{

    public function __construct(PDO $link){
        $tabla = 'em_emp_dir_pendiente';
        $columnas = array($tabla=>false);
        $campos_obligatorios = array();

        parent::__construct(link: $link,tabla:  $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas);

        $this->NAMESPACE = __NAMESPACE__;
    }
}