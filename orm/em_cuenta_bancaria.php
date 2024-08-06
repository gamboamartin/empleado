<?php
namespace gamboamartin\empleado\models;
use base\orm\_modelo_parent;
use gamboamartin\banco\models\bn_sucursal;
use gamboamartin\errores\errores;
use PDO;
use stdClass;


class em_cuenta_bancaria extends _modelo_parent{

    public function __construct(PDO $link){
        $tabla = 'em_cuenta_bancaria';
        $columnas = array($tabla=>false, 'em_empleado'=>$tabla,'bn_sucursal'=>$tabla,'bn_banco'=>'bn_sucursal');
        $campos_obligatorios = array('bn_sucursal_id','em_empleado_id','descripcion_select','clabe','num_cuenta',
            'alias','codigo_bis');

        parent::__construct(link: $link,tabla:  $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas);

        $this->NAMESPACE = __NAMESPACE__;
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        if(!isset($this->registro['codigo'])){

            $this->registro['codigo'] =  $this->get_codigo_aleatorio();
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al generar codigo aleatorio',data:  $this->registro);
            }

            if (isset($this->registro['num_cuenta'])){
                $this->registro['codigo'] = $this->registro['num_cuenta'];
            }
        }

        if(!isset($this->registro['num_cuenta'])){
            $this->registro['num_cuenta'] = 'SIN CUENTA';
        }

        $validaciones = $this->validaciones($this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar campos',data: $validaciones);
        }

        $this->registro = $this->campos_base(data: $this->registro,modelo: $this);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar campos base',data: $this->registro);
        }

        if (isset($this->registro['num_cuenta']) && $this->registro['num_cuenta'] !== 'SIN CUENTA'){
            $filtro['em_cuenta_bancaria.num_cuenta'] = $this->registro['num_cuenta'];
            $existe = $this->existe(filtro: $filtro);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al validar si existe numero de cuenta', data: $existe);
            }

            if ($existe){
                return $this->error->error(mensaje: "Error el numero de cuenta ya existe en el registro", data: $existe);
            }
        }

        $r_alta_bd =  parent::alta_bd();
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al dar de alta cuenta bancaria', data: $r_alta_bd);
        }

        return $r_alta_bd;
    }

    public function validaciones(array $registros): array|stdClass
    {
        $banco = (new bn_sucursal(link: $this->link))->registro(registro_id: $registros['bn_sucursal_id']);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener registro banco',data: $banco);
        }

        if (substr($registros['clabe'], 0, 3) !== $banco['bn_sucursal_codigo']) {
            $mensaje = sprintf("Error la clabe %s no corresponde al codigo %s del banco %s", $registros['clabe'],
                $banco['bn_sucursal_codigo'], $banco['bn_sucursal_descripcion']);
            return $this->error->error(mensaje: $mensaje,data: $registros);
        }

        return $registros;
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

    public function modifica_bd(array $registro, int $id, bool $reactiva = false,
                                array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $filtro['em_cuenta_bancaria.num_cuenta'] = $registro['num_cuenta'];
        $not_in['llave'] = 'em_cuenta_bancaria.id';
        $not_in['values'] = array($this->registro_id);
        $existe = $this->filtro_and(filtro: $filtro, limit: 1, not_in: $not_in);

        if ($existe->n_registros){
            return $this->error->error(mensaje: "Error el numero de cuenta ya existe en el registro", data: $existe);
        }

        $r_modifica_bd = parent::modifica_bd($registro, $id, $reactiva, $keys_integra_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar cuenta bancaria', data: $r_modifica_bd);
        }

        return $r_modifica_bd;
    }
}