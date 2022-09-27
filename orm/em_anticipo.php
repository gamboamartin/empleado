<?php
namespace gamboamartin\empleado\models;
use base\orm\modelo;

use gamboamartin\errores\errores;

use PDO;
use stdClass;

class em_anticipo extends modelo{

    public function __construct(PDO $link){
        $tabla = 'em_anticipo';
        $columnas = array($tabla=>false, 'em_empleado'=>$tabla, 'em_tipo_anticipo'=>$tabla);
        $campos_obligatorios = array('descripcion','codigo','descripcion_select','alias','codigo_bis',
            'em_tipo_anticipo_id','em_empleado_id','monto','fecha_prestacion');
        $campos_view = array(
            'em_tipo_anticipo_id' => array('type' => 'selects', 'model' => new em_tipo_anticipo($link)),
            'em_empleado_id' => array('type' => 'selects', 'model' => new em_empleado($link)));

        parent::__construct(link: $link,tabla:  $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas,campos_view: $campos_view);

        $this->NAMESPACE = __NAMESPACE__;
    }

    public function alta_bd(): array|stdClass
    {
        if (!isset($this->registro['codigo'])) {
            $this->registro['codigo'] = $this->registro['em_empleado_id'];
            $this->registro['codigo'] .= $this->registro['em_tipo_anticipo_id'];
            $this->registro['codigo'] .= $this->registro['descripcion'];
        }

        if (!isset($this->registro['descripcion_select'])) {
            $this->registro['descripcion_select'] = $this->registro['descripcion'];
        }

        if (!isset($this->registro['codigo_bis'])) {
            $this->registro['codigo_bis'] = $this->registro['codigo'];
        }

        if (!isset($this->registro['alias'])) {
            $this->registro['alias'] = $this->registro['codigo'];
            $this->registro['alias'] .= $this->registro['descripcion'];
        }

        $r_alta_bd = parent::alta_bd();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al dar de alta anticipo',data: $r_alta_bd);
        }

        return $r_alta_bd;
    }

    public function anticipos(int $em_empleado_id): array|stdClass
    {
        if($em_empleado_id <=0){
            return $this->error->error(mensaje: 'Error $em_empleado_id debe ser mayor a 0', data: $em_empleado_id);
        }
        $filtro['em_empleado.id'] = $em_empleado_id;
        $r_em_anticipo = $this->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener anticipo', data: $r_em_anticipo);
        }
        return $r_em_anticipo;
    }
}