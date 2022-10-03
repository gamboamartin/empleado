<?php
namespace gamboamartin\empleado\models;
use base\orm\modelo;

use PDO;

class em_tipo_descuento extends modelo{

    public function __construct(PDO $link){
        $tabla = 'em_tipo_descuento';
        $columnas = array($tabla=>false);
        $campos_obligatorios = array('descripcion','codigo','descripcion_select','alias','codigo_bis');

        $campos_view['monto']['type'] = "inputs";

        parent::__construct(link: $link,tabla:  $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas,campos_view: $campos_view);

        $this->NAMESPACE = __NAMESPACE__;
    }
}