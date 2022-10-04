<?php
namespace gamboamartin\empleado\models;
use base\orm\modelo;

use gamboamartin\errores\errores;

use PDO;
use stdClass;

class em_anticipo extends modelo{

    public function __construct(PDO $link){
        $tabla = 'em_anticipo';
        $columnas = array($tabla=>false, 'em_empleado'=>$tabla, 'em_tipo_anticipo'=>$tabla, 'em_tipo_descuento'=>$tabla,
            'em_metodo_calculo'=>'em_tipo_descuento');
        $campos_obligatorios = array('descripcion','codigo','descripcion_select','alias','codigo_bis',
            'em_tipo_anticipo_id','em_empleado_id','monto','fecha_prestacion');

        $campos_view['em_empleado_id']['type'] = "selects";
        $campos_view['em_empleado_id']['model'] = new em_empleado($link);
        $campos_view['em_tipo_anticipo_id']['type'] = "selects";
        $campos_view['em_tipo_anticipo_id']['model'] = new em_tipo_anticipo($link);
        $campos_view['em_tipo_descuento_id']['type'] = "selects";
        $campos_view['em_tipo_descuento_id']['model'] = new em_tipo_descuento($link);
        $campos_view['id']['type'] = "inputs";
        $campos_view['codigo']['type'] = "inputs";
        $campos_view['monto']['type'] = "inputs";
        $campos_view['fecha_prestacion']['type'] = "dates";

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

    public function get_anticipos_empleado(int $em_empleado_id): array|stdClass
    {
        if($em_empleado_id <=0){
            return $this->error->error(mensaje: 'Error $em_empleado_id debe ser mayor a 0', data: $em_empleado_id);
        }

        $filtro['em_empleado.id'] = $em_empleado_id;
        $r_em_anticipo = $this->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener anticipos', data: $r_em_anticipo);
        }

        return $r_em_anticipo;
    }

    public function get_saldo_anticipo(int $em_anticipo_id): float|array
    {
        if($em_anticipo_id <= 0){
            return $this->error->error(mensaje: 'Error $em_anticipo_id debe ser mayor a 0', data: $em_anticipo_id);
        }

        $r_em_anticipo = $this->registro(registro_id: $em_anticipo_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener el anticipo', data: $r_em_anticipo);
        }

        $total_abonado = (new em_abono_anticipo(link: $this->link))->get_total_abonado($em_anticipo_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener el total abonado', data: $total_abonado);
        }

        return round(round($r_em_anticipo['em_anticipo_monto'],2) - round($total_abonado,2),2);
    }


}