<?php
namespace gamboamartin\empleado\models;
use base\orm\modelo;
use gamboamartin\errores\errores;
use models\bn_sucursal;
use PDO;
use stdClass;


class em_cuenta_bancaria extends modelo{

    public function __construct(PDO $link){
        $tabla = 'em_cuenta_bancaria';
        $columnas = array($tabla=>false, 'em_empleado'=>$tabla,'bn_sucursal'=>$tabla,'bn_banco'=>'bn_sucursal');
        $campos_obligatorios = array('bn_sucursal_id','em_empleado_id','descripcion_select','clabe','num_cuenta',
            'alias','codigo_bis');
        $campos_view = array('bn_sucursal_id' => array('type' => 'selects', 'model' => new bn_sucursal($link)),
            'em_empleado_id' => array('type' => 'selects', 'model' => new em_empleado($link)),
            'id' => array('type' => 'inputs'),'codigo' => array('type' => 'inputs'),
            'clabe' => array('type' => 'inputs'),'num_cuenta' => array('type' => 'inputs'));
        parent::__construct(link: $link,tabla:  $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas,campos_view: $campos_view);

        $this->NAMESPACE = __NAMESPACE__;
    }

    public function alta_bd(): array|stdClass
    {
        if (!isset($this->registro['codigo'])) {
            $this->registro['codigo'] = $this->registro['num_cuenta'];
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
            return $this->error->error(mensaje: 'Error al dar de alta cuenta', data: $r_alta_bd);
        }
        return $r_alta_bd;
    }

    public function get_cuentas_bancarias_empleado(int $em_empleado_id): array|stdClass
    {
        if($em_empleado_id <=0){
            return $this->error->error(mensaje: 'Error $em_empleado_id debe ser mayor a 0', data: $em_empleado_id);
        }

        $filtro['em_empleado.id'] = $em_empleado_id;
        $r_em_cuenta_bancaria = $this->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener cuentas bancarias', data: $r_em_cuenta_bancaria);
        }

        return $r_em_cuenta_bancaria;
    }

    public function get_empleado(int $em_cuenta_bancaria_id): array|stdClass
    {
        if($em_cuenta_bancaria_id <=0){
            return $this->error->error(mensaje: 'Error $em_cuenta_bancaria_id debe ser mayor a 0', data: $em_cuenta_bancaria_id);
        }

        $r_em_cuenta_bancaria = $this->registro(registro_id: $em_cuenta_bancaria_id,columnas:
            array("em_cuenta_bancaria_em_empleado_id"));
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener cuentas bancarias', data: $r_em_cuenta_bancaria);
        }

        return $r_em_cuenta_bancaria;
    }


}