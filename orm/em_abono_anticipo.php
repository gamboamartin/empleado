<?php
namespace gamboamartin\empleado\models;
use base\orm\modelo;

use gamboamartin\cat_sat\models\cat_sat_forma_pago;
use gamboamartin\errores\errores;

use PDO;
use stdClass;

class em_abono_anticipo extends modelo{

    public function __construct(PDO $link){
        $tabla = 'em_abono_anticipo';

        $columnas = array($tabla=>false, 'em_anticipo'=>$tabla, 'em_tipo_abono_anticipo'=>$tabla,
            'cat_sat_forma_pago'=>$tabla);

        $campos_obligatorios = array('descripcion','codigo','descripcion_select','alias','codigo_bis',
            'em_tipo_abono_anticipo_id','em_anticipo_id','cat_sat_forma_pago_id','monto','fecha');

        $campos_view = array('em_anticipo_id' => array('type' => 'selects', 'model' => new em_anticipo($link)),
            'em_tipo_abono_anticipo_id' => array('type' => 'selects', 'model' => new em_tipo_abono_anticipo($link)),
            'cat_sat_forma_pago_id' => array('type' => 'selects', 'model' => new cat_sat_forma_pago($link)),
            'id' => array('type' => 'inputs'),'codigo' => array('type' => 'inputs'),
            'fecha' => array('type' => 'dates'), 'monto' => array('type' => 'inputs'));

        parent::__construct(link: $link,tabla:  $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas,campos_view: $campos_view);

        $this->NAMESPACE = __NAMESPACE__;
    }

    public function alta_bd(): array|stdClass
    {
        if (!isset($this->registro['codigo'])) {
            $this->registro['codigo'] = $this->registro['em_anticipo_id'];
            $this->registro['codigo'] .= $this->registro['em_tipo_abono_anticipo_id'];
            $this->registro['codigo'] .= $this->registro['cat_sat_forma_pago_id'];
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

    public function get_abonos_anticipo(int $em_anticipo_id): array|stdClass
    {
        if($em_anticipo_id <=0){
            return $this->error->error(mensaje: 'Error $em_anticipo_id debe ser mayor a 0', data: $em_anticipo_id);
        }

        $filtro['em_anticipo.id'] = $em_anticipo_id;
        $registros = $this->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener abonos', data: $registros);
        }

        return $registros;
    }

    public function get_total_abonado(int $em_anticipo_id): float|array
    {
        if($em_anticipo_id <= 0){
            return $this->error->error(mensaje: 'Error $em_anticipo_id debe ser mayor a 0', data: $em_anticipo_id);
        }

        $campos['total_abonado'] = 'em_abono_anticipo.monto';
        $filtro['em_abono_anticipo.em_anticipo_id'] = $em_anticipo_id;
        $r_em_anticipo = $this->suma(campos:$campos, filtro:  $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener el anticipo', data: $r_em_anticipo);
        }

        return round($r_em_anticipo['total_abonado'],2);
    }
}