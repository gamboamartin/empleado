<?php
namespace gamboamartin\empleado\models;
use base\orm\modelo;

use gamboamartin\comercial\models\com_sucursal;
use gamboamartin\errores\errores;

use html\com_sucursal_html;
use PDO;
use stdClass;

class em_anticipo extends modelo{

    public function __construct(PDO $link){
        $tabla = 'em_anticipo';
        $columnas = array($tabla=>false, 'em_empleado'=>$tabla, 'em_tipo_anticipo'=>$tabla, 'em_tipo_descuento'=>$tabla,
            'em_metodo_calculo'=>'em_tipo_descuento');
        $campos_obligatorios = array('descripcion','codigo','descripcion_select','alias','codigo_bis',
            'em_tipo_anticipo_id','em_empleado_id','monto','fecha_inicio_descuento','fecha_prestacion',
            'em_tipo_descuento_id');

        $columnas_extra['em_anticipo_abonos'] = 'IFNULL((SELECT SUM(em_abono_anticipo.monto) 
        FROM em_abono_anticipo WHERE em_abono_anticipo.em_anticipo_id = em_anticipo.id),0.0)';

        $columnas_extra['em_anticipo_saldo'] = "IFNULL((em_anticipo.monto - $columnas_extra[em_anticipo_abonos]),0.0)";

        $columnas_extra['em_anticipo_tiene_saldo'] = "IFNULL((SELECT IF($columnas_extra[em_anticipo_saldo] > 0, 'activo', 'inactivo')),0.0)";
        $columnas_extra['n_pago'] = 'IFNULL((SELECT COUNT(em_abono_anticipo.id) 
        FROM em_abono_anticipo WHERE em_abono_anticipo.em_anticipo_id = em_anticipo.id) + 1,0)';
        $columnas_extra['pago_siguiente'] = 'IFNULL((SELECT ROUND(monto/n_pagos,2) FROM em_anticipo WHERE id = em_anticipo.id),0.0)';

        $campos_view['em_empleado_id']['type'] = "selects";
        $campos_view['em_empleado_id']['model'] = new em_empleado($link);
        $campos_view['em_tipo_anticipo_id']['type'] = "selects";
        $campos_view['em_tipo_anticipo_id']['model'] = new em_tipo_anticipo($link);
        $campos_view['em_tipo_descuento_id']['type'] = "selects";
        $campos_view['em_tipo_descuento_id']['model'] = new em_tipo_descuento($link);
        $campos_view['com_sucursal_id']['type'] = "selects";
        $campos_view['com_sucursal_id']['model'] = new com_sucursal($link);
        $campos_view['id']['type'] = "inputs";
        $campos_view['codigo']['type'] = "inputs";
        $campos_view['monto']['type'] = "inputs";
        $campos_view['n_pagos']['type'] = "inputs";
        $campos_view['fecha_prestacion']['type'] = "dates";
        $campos_view['fecha_inicio']['type'] = "dates";
        $campos_view['fecha_final']['type'] = "dates";
        $campos_view['fecha_inicio_descuento']['type'] = "dates";

        parent::__construct(link: $link,tabla:  $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas,campos_view: $campos_view, columnas_extra: $columnas_extra);

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

    /**
     * Obtiene los anticipos de un empleado
     * @param bool $con_saldo
     * @param int $em_empleado_id Identificador del empleado
     * @param string $fecha
     * @return array|stdClass
     * @version 1.131.1
     */
    public function get_anticipos_empleado( int $em_empleado_id, bool $con_saldo = false, string $fecha = ''): array|stdClass
    {
        if($em_empleado_id <=0){
            return $this->error->error(mensaje: 'Error $em_empleado_id debe ser mayor a 0', data: $em_empleado_id);
        }

        if($fecha === ''){
            $fecha = date('Y-m-d');
        }

        $filtro['em_empleado.id'] = $em_empleado_id;

        if($con_saldo) {
            $filtro['em_anticipo_tiene_saldo']['campo'] = 'em_empleado_tiene_saldo';
            $filtro['em_anticipo_tiene_saldo']['es_sq'] = true;
            $filtro['em_anticipo_tiene_saldo']['value'] = 'activo';
        }

        $filtro_extra[0]['em_anticipo.fecha_inicio_descuento']['operador'] = '<=';
        $filtro_extra[0]['em_anticipo.fecha_inicio_descuento']['valor'] = $fecha;
        $filtro_extra[0]['em_anticipo.fecha_inicio_descuento']['comparacion'] = 'AND';
        $r_em_anticipo = $this->filtro_and(filtro: $filtro, filtro_extra: $filtro_extra);
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