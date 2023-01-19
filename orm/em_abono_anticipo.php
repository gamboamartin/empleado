<?php
namespace gamboamartin\empleado\models;
use base\orm\_modelo_parent;
use base\orm\modelo;

use gamboamartin\cat_sat\models\cat_sat_forma_pago;
use gamboamartin\errores\errores;

use PDO;
use stdClass;

class em_abono_anticipo extends _modelo_parent{

    public function __construct(PDO $link){
        $tabla = 'em_abono_anticipo';

        $columnas = array($tabla=>false, 'em_anticipo'=>$tabla, 'em_tipo_abono_anticipo'=>$tabla,
            'cat_sat_forma_pago'=>$tabla, 'em_empleado' => 'em_anticipo');

        $campos_obligatorios = array('descripcion','codigo','descripcion_select','alias','codigo_bis',
            'em_tipo_abono_anticipo_id','em_anticipo_id','cat_sat_forma_pago_id','monto','fecha');


        parent::__construct(link: $link,tabla:  $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas);

        $this->NAMESPACE = __NAMESPACE__;
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        if (!isset($this->registro['codigo'])) {
            $this->registro['codigo'] = $this->get_codigo_aleatorio();
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al generar codigo aleatorio', data: $this->registro);
            }
        }

        $this->registro = $this->campos_base(data: $this->registro, modelo: $this);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al inicializar campos base', data: $this->registro);
        }

        $validacion = $this->validaciones(data: $this->registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar datos', data: $validacion);
        }

        $this->registro = $this->limpia_campos(registro: $this->registro, campos_limpiar: array('em_empleado_id'));
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al limpiar campos', data: $this->registro);
        }

        $anticipo['em_anticipo_saldo_pendiente'] = (new em_anticipo($this->link))->get_saldo_anticipo(
            $this->registro['em_anticipo_id']);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener el saldo pendiente',data: $anticipo);
        }

        $this->registro['monto'] = round($this->registro['monto'],2);

        if ($this->registro['monto'] > $anticipo['em_anticipo_saldo_pendiente']){
            return $this->error->error(mensaje: 'Error el monto ingresado es mayor al saldo pendiente',
                data: $this->registro['monto']);
        }

        $n_pago = $this->num_pago_siguiente(em_anticipo_id: $this->registro['em_anticipo_id']);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener el numero de pago',data: $n_pago);
        }

        $em_anticipo = (new em_anticipo($this->link))->registro(registro_id: $this->registro['em_anticipo_id'],
            columnas: ["em_anticipo_n_pagos"]);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener anticipo',data: $em_anticipo);
        }

        if ($n_pago > $em_anticipo['em_anticipo_n_pagos']){
            return $this->error->error(mensaje: 'Error el numero de pago actual es mayor al total de pagos',
                data: $n_pago);
        }

        $r_alta_bd = parent::alta_bd();
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error registrar partida', data: $r_alta_bd);
        }

        return $r_alta_bd;
    }

    /**
     * Obtiene los abonos de un anticipo
     * @param int $em_anticipo_id Identificador del anticipo
     * @return array|stdClass
     * @version 0.128.1
     */
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

    public function get_codigo_aleatorio(int $longitud = 6): string
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $random_string = '';

        for ($i = 0; $i < $longitud; $i++) {
            $random_character = $chars[mt_rand(0, strlen($chars) - 1)];
            $random_string .= $random_character;
        }

        return $random_string;
    }

    /**
     * Obtiene el monto total abonado de un anticipo
     * @param int $em_anticipo_id Identificador del anticipo
     * @return float|array
     * @version 0.129.1
     */
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

    private function limpia_campos(array $registro, array $campos_limpiar): array
    {
        foreach ($campos_limpiar as $valor) {
            if (isset($registro[$valor])) {
                unset($registro[$valor]);
            }
        }
        return $registro;
    }

    public function num_pago_siguiente(int $em_anticipo_id): int|array
    {
        if($em_anticipo_id <= 0){
            return $this->error->error(mensaje: 'Error $em_anticipo_id debe ser mayor a 0', data: $em_anticipo_id);
        }

        $filtro['em_abono_anticipo.em_anticipo_id'] = $em_anticipo_id;
        $r_em_abono = $this->filtro_and(filtro:  $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener los abonos', data: $r_em_abono);
        }

        return $r_em_abono->n_registros + 1;
    }

    private function validaciones(array $data): bool|array
    {
        $keys = array('descripcion', 'codigo','monto');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys, registro: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar campos', data: $valida);
        }

        $keys = array('em_tipo_abono_anticipo_id', 'em_anticipo_id', 'cat_sat_forma_pago_id');
        $valida = $this->validacion->valida_ids(keys: $keys, registro: $this->registro);
        if (errores::$error) {
            return $this->error->error(mensaje: "Error al validar foraneas", data: $valida);
        }

        $keys = array('monto');
        $valida = $this->validacion->valida_double_mayores_igual_0(keys: $keys,registro:  $this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar cantidades',data: $valida);
        }

        return true;
    }

}