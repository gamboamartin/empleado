<?php
namespace gamboamartin\empleado\models;

use base\orm\_modelo_parent;
use gamboamartin\cat_sat\models\cat_sat_tipo_jornada_nom;
use gamboamartin\comercial\models\com_cliente;
use gamboamartin\comercial\models\com_sucursal;
use gamboamartin\direccion_postal\models\dp_calle_pertenece;
use gamboamartin\errores\errores;

use gamboamartin\organigrama\models\org_puesto;
use PDO;
use stdClass;

class em_empleado extends _modelo_parent{
    public errores $error;
    public function __construct(PDO $link){
        $this->error = new errores();
        $tabla = 'em_empleado';

        $columnas = array($tabla=>false, 'em_registro_patronal'=>$tabla, 'cat_sat_regimen_fiscal'=>$tabla,
            'dp_calle_pertenece'=>$tabla,'cat_sat_tipo_regimen_nom'=>$tabla,'org_puesto'=>$tabla,
            'org_departamento'=>'org_puesto','cat_sat_tipo_jornada_nom'=>$tabla, 'em_centro_costo' =>$tabla,
            'fc_csd' => 'em_registro_patronal');

        $campos_obligatorios = array('nombre','ap','descripcion','codigo','curp','rfc');

        $tipo_campos = array();
        $tipo_campos['rfc'] = 'rfc';

        $columnas_extra['em_empleado_nombre_completo'] = 'CONCAT (IFNULL(em_empleado.nombre,"")," ",IFNULL(em_empleado.ap, "")," ",IFNULL(em_empleado.am,""))';
        $columnas_extra['em_empleado_nombre_completo_inv'] = 'CONCAT (IFNULL(em_empleado.ap,"")," ",IFNULL(em_empleado.am, "")," ",IFNULL(em_empleado.nombre,""))';
        $columnas_extra['em_empleado_n_cuentas_bancarias'] = "(SELECT COUNT(*) FROM em_cuenta_bancaria 
        WHERE em_cuenta_bancaria.em_empleado_id = em_empleado.id)";

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, tipo_campos: $tipo_campos);

        $this->NAMESPACE = __NAMESPACE__;
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass 
    {
        if(!isset($this->registro['codigo'])){ 

            $this->registro['codigo'] =  $this->get_codigo_aleatorio();
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al generar codigo aleatorio',data:  $this->registro);
            }

            if (isset($this->registro['rfc'])){
                $this->registro['codigo'] = $this->registro['rfc'];
            }
        }

        if(!isset($this->registro['descripcion'])){
            $this->registro['descripcion'] = $this->registro['nombre']. ' ';
            $this->registro['descripcion'] .= $this->registro['ap'];
        }

        $this->registro = $this->fecha_inicio_rel_laboral_default($this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar fecha rel laboral',data: $this->registro);
        }

        $this->registro = $this->dp_calle_pertenece_id($this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar direcciones',data: $this->registro);
        }

        $this->registro = $this->org_puesto_id($this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar puesto',data: $this->registro);
        }

        $this->registro = $this->cat_sat_tipo_jornada_nom_id($this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar tipo jornada nomina',data: $this->registro);
        }

        $this->registro = $this->campos_base(data:$this->registro,modelo: $this);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar campo base',data: $this->registro);
        }

        $this->registro['descripcion_select'] = isset($this->registro['nss']) ? $this->registro['nss']." - " : "SIN NSS - ";
        $this->registro['descripcion_select'] .= $this->registro['nombre']. ' ';
        $this->registro['descripcion_select'] .= $this->registro['ap']. ' ';
        $this->registro['descripcion_select'] .= isset($this->registro['am']) ? $this->registro['am']: "";
        $this->registro['descripcion_select'] = strtoupper($this->registro['descripcion_select']);

        $this->registro = $this->limpia_campos_extras(registro: $this->registro, campos_limpiar: array("dp_pais_id",
            "dp_estado_id","dp_municipio_id", "dp_cp_id","dp_colonia_postal_id"));
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al limpiar campos', data: $this->registro);
        }

        if(!isset($this->registro['rfc'])){
            $this->registro['rfc'] = 'AAA010101AAA';
        }



        $r_alta_bd = parent::alta_bd();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al dar de alta empleado',data:  $r_alta_bd);
        }


        $respuesta = $this->transacciona_em_rel_empleado_sucursal(data: $this->registro,
            em_empleado_id: $r_alta_bd->registro_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al transaccionar relacion empleado sucursal',data:  $respuesta);
        }

        return $r_alta_bd;
    }


    /**
     * Obtiene el tipo de jornada si no existe
     * @param array $registro Registro en proceso
     * @return array
     * @version 0.126.1
     */
    private function cat_sat_tipo_jornada_nom_id(array $registro): array
    {
        if (!isset($registro['cat_sat_tipo_jornada_nom_id'])) {
            $cat_tipo_jornada_nom_id =  (new cat_sat_tipo_jornada_nom($this->link))->id_predeterminado();
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener cat_tipo_jornada_nom_id',data: $cat_tipo_jornada_nom_id);
            }
            $registro['cat_sat_tipo_jornada_nom_id'] = $cat_tipo_jornada_nom_id;
        }
        return $registro;
    }

    private function dp_calle_pertenece_id(array $registro): array
    {
        if (!isset($registro['dp_calle_pertenece_id'])) {
            $registro['dp_calle_pertenece_id'] =  (new dp_calle_pertenece($this->link))->get_calle_pertenece_default_id();
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener calle_pertenece_default',data: $registro['dp_calle_pertenece_id']);
            }
        }
        return $registro;
    }

    private function fecha_inicio_rel_laboral_default(array $registro): array
    {
        if (!isset($registro['fecha_inicio_rel_laboral'])) {
            $registro['fecha_inicio_rel_laboral'] = '1900-01-01';
        }
        return $registro;
    }

    public function get_direccion(int $dp_calle_pertenece_id): array|stdClass
    {
        if($dp_calle_pertenece_id <= 0){
            return $this->error->error(mensaje: 'Error $dp_calle_pertenece_id debe ser mayor a 0', data: $dp_calle_pertenece_id);
        }

        $filtro['dp_calle_pertenece.id'] = $dp_calle_pertenece_id;
        $dp_calle_pertenece = (new dp_calle_pertenece($this->link))->registro($dp_calle_pertenece_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener direccion', data: $dp_calle_pertenece);
        }

        return $dp_calle_pertenece;
    }

    /**
     * Obtiene empresa a partir de empleado
     * @param int $em_empleado_id Identificador del empleado a revisar su empresa
     * @return array|stdClass
     * @version
     */
    public function get_empresa(int $em_empleado_id): array|stdClass
    {
        $r_empleado = $this->registro(registro_id: $em_empleado_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener empleado',data: $r_empleado);
        }

        $r_registro_patronal =  (new em_registro_patronal($this->link))->registro(registro_id:
            $r_empleado['em_registro_patronal_id']);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener registro patronal',data: $r_registro_patronal);
        }

        return $r_registro_patronal;
    }

    public function inserta_com_cliente(array $data): array|stdClass
    {

        $data = $this->maqueta_com_cliente(data: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al maquetar datos para cliente', data: $data);
        }

        foreach ($data as $campo=>$value){
            if(is_iterable($value)){
                return $this->error->error(mensaje: 'Error value es iterable '.$campo, data: $value);
            }
        }


        $respuesta = (new com_cliente($this->link))->alta_registro(registro: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al ingresar cliente', data: $respuesta);
        }

        return $respuesta;
    }

    public function transacciona_em_rel_empleado_sucursal(array $data, int $em_empleado_id): array|stdClass
    {
        $alta_com_cliente = $this->inserta_com_cliente(data: $this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al dar de alta com_cliente',data:  $alta_com_cliente);
        }

        $filtro['com_cliente_id'] = $alta_com_cliente->registro_id;
        $com_sucursal = (new com_sucursal($this->link))->filtro_and(filtro: $filtro, limit: 1);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al datos del cliente', data: $data);
        }

        $data = $com_sucursal->registros[0];
        $data['em_empleado_id'] = $em_empleado_id;

        $respuesta = (new em_rel_empleado_sucursal($this->link))->inserta_em_rel_empleado_sucursal(data: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al ingresar cliente', data: $respuesta);
        }

        return $respuesta;
    }


    public function maqueta_com_cliente(array $data): array
    {
        $salida = array();

        if (isset($data['codigo'])) {
            $salida['codigo'] = $data['codigo'];
        }

        if (isset($data['descripcion'])) {
            $salida['descripcion'] = $data['descripcion'];
        }

        $r_rfc = $this->rfc(registro: $data);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar rfc',data: $r_rfc);
        }

        $rfc = $r_rfc['rfc'];
        if (isset($data['rfc'])) {
            $rfc = $data['rfc'];
        }

        $razon_social = $data['descripcion'];
        if (isset($data['razon_social'])) {
            $razon_social = $data['razon_social'];
        }

        $telefono = "9999999999";

        if (isset($data['telefono'])) {
            $telefono = $data['telefono'];
        }

        $numero_exterior = "xxx";

        if (isset($data['numero_exterior'])) {
            $numero_exterior = $data['numero_exterior'];
        }

        $numero_interior = "xxx";

        if (isset($data['numero_interior'])) {
            $numero_interior = $data['numero_interior'];
        }

        $dp_calle_pertenece_id = $this->dp_calle_pertenece_id(registro: $data);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar direcciones',data: $dp_calle_pertenece_id);
        }

        if (isset($data['dp_calle_pertenece_id'])) {
            $dp_calle_pertenece_id = $data['dp_calle_pertenece_id'];
        }


        if(isset($data['cat_sat_regimen_fiscal_id'])){
            $salida['cat_sat_regimen_fiscal_id'] = $data['cat_sat_regimen_fiscal_id'];
        }
        if(isset($data['cat_sat_moneda_id'])){
            $salida['cat_sat_moneda_id'] = $data['cat_sat_moneda_id'];
        }
        if(isset($data['cat_sat_forma_pago_id'])){
            $salida['cat_sat_forma_pago_id'] = $data['cat_sat_forma_pago_id'];
        }
        if(isset($data['cat_sat_metodo_pago_id'])){
            $salida['cat_sat_metodo_pago_id'] = $data['cat_sat_metodo_pago_id'];
        }
        if(isset($data['cat_sat_uso_cfdi_id'])){
            $salida['cat_sat_uso_cfdi_id'] = $data['cat_sat_uso_cfdi_id'];
        }
        if(isset($data['cat_sat_tipo_de_comprobante_id'])){
            $salida['cat_sat_tipo_de_comprobante_id'] = $data['cat_sat_tipo_de_comprobante_id'];
        }
        if(isset($data['com_tipo_cliente_id'])){
            $salida['com_tipo_cliente_id'] = $data['com_tipo_cliente_id'];
        }

        $salida['razon_social'] = $razon_social;
        $salida['rfc'] = $rfc;
        $salida['telefono'] = $telefono;
        $salida['numero_exterior'] = $numero_exterior;
        $salida['numero_interior'] = $numero_interior;
        $salida['dp_calle_pertenece_id'] = $dp_calle_pertenece_id;
        $salida['es_empleado'] = true;

        return $salida;
    }

    public function modifica_bd(array $registro, int $id, bool $reactiva = false,
                                array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {

        $em_empleado_previo = $this->registro(registro_id: $id, columnas_en_bruto: true,retorno_obj: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener em_empleado_previo',data: $em_empleado_previo);
        }


        if(!isset($registro['codigo'])){
            if (isset($registro['rfc'])){
                $registro['codigo'] = $registro['rfc'];
            }
        }

        if(!isset($registro['descripcion'])){

            if(!isset($registro['nombre'])){
                $registro['nombre'] = $em_empleado_previo->nombre;
            }
            if(!isset($registro['ap'])){
                $registro['ap'] = $em_empleado_previo->ap;
            }

            $registro['descripcion'] = $registro['nombre']. ' ';
            $registro['descripcion'] .= $registro['ap'];
        }

        $registro = $this->campos_base(data:$registro,modelo: $this,id: $id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar campo base',data: $this->registro);
        }

        if ($registro['nss'] != ""){
            $registro['descripcion_select'] = $registro['nss']." - ";
        } else {
            $registro['descripcion_select'] = "SIN NSS - ";
        }

        //$registro['descripcion_select'] = is_null($registro['nss']) ? $registro['nss']." - " : "SIN NSS - ";
        $registro['descripcion_select'] .= $registro['nombre']. ' ';
        $registro['descripcion_select'] .= $registro['ap']. ' ';
        $registro['descripcion_select'] .= isset($registro['am']) ? $registro['am']: "";
        $registro['descripcion_select'] = strtoupper($registro['descripcion_select']);

        $registro = $this->limpia_campos_extras(registro: $registro, campos_limpiar: array("dp_pais_id",
            "dp_estado_id","dp_municipio_id", "dp_cp_id","dp_colonia_postal_id"));
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al limpiar campos', data: $registro);
        }



        $r_modifica_bd = parent::modifica_bd($registro, $id, $reactiva, $keys_integra_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar empleado',data: $r_modifica_bd);
        }

        return $r_modifica_bd;
    }


    private function org_puesto_id(array $registro): array
    {
        if (!isset($registro['org_puesto_id'])) {
            $registro['org_puesto_id'] =  (new org_puesto($this->link))->get_puesto_default_id();
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener get_puesto_default_id',data: $registro['org_puesto_id']);
            }
        }
        return $registro;
    }


    /**
     * Genera un rfc default
     * @param array $registro Registro en proceso
     * @return array
     */
    private function rfc(array $registro): array
    {
        if (!isset($registro['rfc'])) {
            $registro['rfc'] = 'AAA010101AAA';
        }
        return $registro;
    }
}