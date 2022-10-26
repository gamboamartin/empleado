<?php
namespace gamboamartin\empleado\models;


use base\orm\modelo;
use DateTime;
use gamboamartin\cat_sat\models\cat_sat_regimen_fiscal;
use gamboamartin\cat_sat\models\cat_sat_tipo_jornada_nom;
use gamboamartin\cat_sat\models\cat_sat_tipo_regimen_nom;
use gamboamartin\direccion_postal\models\dp_calle;
use gamboamartin\direccion_postal\models\dp_calle_pertenece;
use gamboamartin\direccion_postal\models\dp_colonia;
use gamboamartin\direccion_postal\models\dp_colonia_postal;
use gamboamartin\direccion_postal\models\dp_cp;
use gamboamartin\direccion_postal\models\dp_direccion_pendiente;
use gamboamartin\direccion_postal\models\dp_estado;
use gamboamartin\direccion_postal\models\dp_municipio;
use gamboamartin\direccion_postal\models\dp_pais;
use gamboamartin\errores\errores;
use gamboamartin\organigrama\models\org_puesto;
use models\im_conf_pres_empresa;
use models\im_detalle_conf_prestaciones;
use models\im_registro_patronal;
use models\nom_par_otro_pago;
use PDO;
use stdClass;
use Throwable;

class em_empleado extends modelo{
    public errores $error;
    public function __construct(PDO $link){
        $this->error = new errores();
        $tabla = 'em_empleado';

        $columnas = array($tabla=>false, 'im_registro_patronal'=>$tabla, 'cat_sat_regimen_fiscal'=>$tabla,
            'dp_calle_pertenece'=>$tabla,'cat_sat_tipo_regimen_nom'=>$tabla,'org_puesto'=>$tabla,
            'org_departamento'=>'org_puesto','cat_sat_tipo_jornada_nom'=>$tabla);

        $campos_obligatorios = array('nombre','descripcion','codigo','descripcion_select','alias','codigo_bis',
            'org_puesto_id','cat_sat_tipo_jornada_nom_id');

        $campos_view['dp_pais_id'] = array('type' => 'selects', 'model' => new dp_pais($link));
        $campos_view['dp_estado_id'] = array('type' => 'selects', 'model' => new dp_estado($link));
        $campos_view['dp_municipio_id'] = array('type' => 'selects', 'model' => new dp_municipio($link));
        $campos_view['dp_cp_id'] = array('type' => 'selects', 'model' => new dp_cp($link));
        $campos_view['dp_colonia_id'] = array('type' => 'selects', 'model' => new dp_colonia($link));
        $campos_view['dp_colonia_postal_id'] = array('type' => 'selects', 'model' => new dp_colonia_postal($link));
        $campos_view['dp_calle_id'] = array('type' => 'selects', 'model' => new dp_calle($link));
        $campos_view['dp_calle_pertenece_id'] = array('type' => 'selects', 'model' => new dp_calle_pertenece($link));
        $campos_view['cat_sat_regimen_fiscal_id'] = array('type' => 'selects', 'model' => new cat_sat_regimen_fiscal($link));
        $campos_view['org_puesto_id'] = array('type' => 'selects', 'model' => new org_puesto($link));
        $campos_view['cat_sat_tipo_regimen_nom_id'] = array('type' => 'selects', 'model' => new cat_sat_tipo_regimen_nom($link));
        $campos_view['im_registro_patronal_id'] = array('type' => 'selects', 'model' => new im_registro_patronal($link));
        $campos_view['cat_sat_tipo_jornada_nom_id'] = array('type' => 'selects', 'model' => new cat_sat_tipo_jornada_nom($link));
        $campos_view['fecha_inicio_rel_laboral'] = array('type' => 'dates');
        $campos_view['codigo'] = array('type' => 'inputs');
        $campos_view['nombre'] = array('type' => 'inputs');
        $campos_view['ap'] = array('type' => 'inputs');
        $campos_view['am'] = array('type' => 'inputs');
        $campos_view['telefono'] = array('type' => 'inputs');
        $campos_view['rfc'] = array('type' => 'inputs');
        $campos_view['nss'] = array('type' => 'inputs');
        $campos_view['curp'] = array('type' => 'inputs');
        $campos_view['salario_diario'] = array('type' => 'inputs');
        $campos_view['salario_diario_integrado'] = array('type' => 'inputs');
        $campos_view['campo_extra'] = array('type' => 'inputs');

        $tipo_campos = array();
        $tipo_campos['rfc'] = 'rfc';

        $columnas_extra['em_empleado_nombre_completo'] = 'CONCAT (IFNULL(em_empleado.nombre,"")," ",IFNULL(em_empleado.ap, "")," ",IFNULL(em_empleado.am,""))';
        $columnas_extra['em_empleado_nombre_completo_inv'] = 'CONCAT (IFNULL(em_empleado.ap,"")," ",IFNULL(em_empleado.am, "")," ",IFNULL(em_empleado.nombre,""))';

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, campos_view: $campos_view, columnas_extra: $columnas_extra, tipo_campos: $tipo_campos);

        $this->NAMESPACE = __NAMESPACE__;
    }
    private function alias(array $registro): array
    {
        if(!isset($registro['alias'])){
            $registro['alias'] = strtoupper($registro['descripcion']);
        }
        return $registro;
    }


    public function alta_bd(): array|stdClass
    {
        $registro = $this->registro;

        $keys = array('nombre','ap');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys, registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data: $valida);
        }

        $registro = $this->init_alta(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al asignar am',data: $registro);
        }

        $this->registro = $registro;

        $alta_direccion = $this->direccion_pendiente();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al dar de alta direccion pendiente',data: $alta_direccion);
        }

        $r_alta_bd = parent::alta_bd();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al dar de alta empleado',data: $r_alta_bd);
        }

        if(!empty($alta_direccion)){
            $alta_emp_dir_pendiente = $this->emp_direccion_pendiente(em_empleado: $r_alta_bd,
                dp_direccion_pendiente: $alta_direccion);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al asignar direccion pendiente al empleado',data: $alta_emp_dir_pendiente);
            }
        }
        return $r_alta_bd;
    }


    /**
     * Maqueta el apellido materno en vacio si no existe
     * @param array $registro Registro en proceso
     * @return array
     * @version 0.125.0
     */
    private function am(array $registro): array
    {
        if(!isset($registro['am'])){
            $registro['am'] = '';
        }
        return $registro;
    }

    public function calcula_sdi(int $em_empleado_id, string $fecha_inicio_rel, float $salario_diario): float|array
    {
        $factor = $this->obten_factor(em_empleado_id: $em_empleado_id, fecha_inicio_rel: $fecha_inicio_rel);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener factor',data:  $factor);
        }

        $sdi = $salario_diario * $factor;

        return round($sdi,2);
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

    private function codigo_bis(array $registro): array
    {
        if(!isset($registro['codigo_bis'])){
            $registro['codigo_bis'] = strtoupper($registro['codigo']);
        }
        return $registro;
    }

    private function descripcion(array $registro): array
    {
        if (!isset($registro['descripcion'])) {
            $registro['descripcion'] = $registro['nombre'].' '.$registro['ap'].' ';
            $registro['descripcion'] .= $registro['am'];
        }
        return $registro;
    }

    private function direccion_pendiente(): array|stdClass
    {
        $resultado = array();

        if (isset($this->registro["campo_extra_dp_pais"]) || isset($this->registro["campo_extra_dp_estado"]) ||
            isset($this->registro["campo_extra_dp_municipio"]) || isset($this->registro["campo_extra_dp_cp"]) ||
            isset($this->registro["campo_extra_dp_colonia"]) || isset($this->registro["campo_extra_dp_colonia_postal"]) ||
            isset($this->registro["campo_extra_dp_calle"]) || isset($this->registro["campo_extra_dp_calle_pertenece"])){

            $registros = array();
            $registros['descripcion_pais'] = $this->registro["campo_extra_dp_pais"] ?? $this->registro["dp_pais_id"];
            $registros['descripcion_estado'] = $this->registro["campo_extra_dp_estado"] ?? $this->registro["dp_estado_id"];
            $registros['descripcion_municipio'] = $this->registro["campo_extra_dp_municipio"] ?? $this->registro["dp_municipio_id"];
            $registros['descripcion_cp'] = $this->registro["campo_extra_dp_cp"] ?? $this->registro["dp_cp_id"];
            $registros['descripcion_colonia'] = $this->registro["campo_extra_dp_colonia"] ?? $this->registro["dp_colonia_id"];
            $registros['descripcion_colonia_postal'] = $this->registro["campo_extra_dp_colonia_postal"] ?? $this->registro["dp_colonia_postal_id"];
            $registros['descripcion_calle'] = $this->registro["campo_extra_dp_calle"] ?? $this->registro["dp_calle_id"];
            $registros['descripcion_calle_pertenece'] = $this->registro["campo_extra_dp_calle_pertenece"] ?? $this->registro["dp_calle_pertenece_id"];

            $resultado = (new dp_direccion_pendiente($this->link))->alta_registro($registros);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al dar de alta direccion pendiente',data: $resultado);
            }
        }

        $this->registro = $this->limpia_campos(registro: $this->registro,
            campos_limpiar: array('campo_extra', 'campo_extra_dp_pais', 'campo_extra_dp_estado'
            ,'campo_extra_dp_municipio','campo_extra_dp_cp','campo_extra_dp_colonia'
            ,'campo_extra_dp_colonia_postal','campo_extra_dp_calle','campo_extra_dp_calle_pertenece',
                "dp_pais_id","dp_estado_id","dp_municipio_id","dp_cp_id","dp_colonia_id","dp_colonia_postal_id",
                "dp_calle_id"));
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al limpiar campos', data: $this->registro);
        }

        return $resultado;
    }

    private function descripcion_select(array $registro): array
    {
        if (!isset($registro['descripcion_select'])) {
            $registro['descripcion_select'] = $registro['nombre'].' '.$registro['ap'].' ';
            $registro['descripcion_select'] .= $registro['am'];
            $registro['descripcion_select'] = strtoupper($registro['descripcion_select']);
        }
        return $registro;
    }

    private function emp_direccion_pendiente(array|stdClass $em_empleado ,array|stdClass $dp_direccion_pendiente): array|stdClass
    {
        $registro['em_empleado_id'] = $em_empleado->registro_id;
        $registro['dp_direccion_pendiente_id'] = $dp_direccion_pendiente->registro_id;
        $registro['codigo'] = $dp_direccion_pendiente->registro["dp_direccion_pendiente_codigo"];
        $registro['codigo_bis'] = $dp_direccion_pendiente->registro["dp_direccion_pendiente_codigo_bis"];
        $registro['descripcion'] = $dp_direccion_pendiente->registro["dp_direccion_pendiente_descripcion"];
        $registro['descripcion_select'] = $dp_direccion_pendiente->registro["dp_direccion_pendiente_descripcion_select"];
        $registro['alias'] = $dp_direccion_pendiente->registro["dp_direccion_pendiente_alias"];
        $alta = (new em_emp_dir_pendiente($this->link))->alta_registro(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener direccion', data: $alta);
        }

        return $alta;
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

        $r_registro_patronal =  (new im_registro_patronal($this->link))->registro(registro_id:
            $r_empleado['im_registro_patronal_id']);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener registro patronal',data: $r_registro_patronal);
        }

        return $r_registro_patronal;
    }

    private function init_alta(array $registro): array
    {
        $registro = $this->am(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al asignar am',data: $registro);
        }


        $registro = $this->descripcion(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al asignar descripcion',data: $registro);
        }

        $registro = $this->alias(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al asignar alias',data: $registro);
        }

        $registro = $this->descripcion_select(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al asignar descripcion',data: $registro);
        }

        $registro = $this->codigo_bis(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al asignar codigo_bis',data: $registro);
        }

        $registro = $this->org_puesto_id(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al asignar org puesto',data: $registro);
        }
        $registro = $this->cat_sat_tipo_jornada_nom_id(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al asignar cat_sat_tipo_jornada_nom_id',data: $registro);
        }
        $registro = $this->rfc(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al asignar am',data: $registro);
        }
        $registro = $this->fecha_inicio_rel_laboral_default(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al asignar am',data: $registro);
        }

        return $registro;
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

    public function modifica_bd(array $registro, int $id, bool $reactiva = false): array|stdClass
    {
        if(isset($registro['codigo'])){
            $registro['codigo_bis'] = $registro['codigo'];
        }

        if(isset($registro['nombre']) && isset($registro['ap']) && isset($registro['am']) && isset($registro['rfc'])) {
            $registro['descripcion_select'] = $registro['nombre'] . ' ' . $registro['ap'] . ' ';
            $registro['descripcion_select'] .= $registro['am'] . ' ' . $registro['rfc'];

            $registro['descripcion'] = $registro['nombre'] . ' ' . $registro['ap'] . ' ';
            $registro['descripcion'] .= $registro['am'] . ' ' . $registro['rfc'];

            $registro['alias'] = $registro['descripcion'];
        }
        $r_modifica_bd = parent::modifica_bd($registro, $id, $reactiva); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar empleado',data: $r_modifica_bd);
        }

        return $r_modifica_bd;

    }



    public function obten_conf(int $em_empleado_id): array
    {
        $imss_modelo = new im_conf_pres_empresa($this->link);
        $empresa = $imss_modelo->obten_configuraciones_empresa(em_empleado_id: $em_empleado_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener empresa perteneciente',data:  $empresa);
        }

        $im_conf_prestaciones_id = $empresa[0]['im_conf_prestaciones_id'];
        $detalle_conf = (new im_detalle_conf_prestaciones($this->link))->obten_detalle_conf
        (im_conf_prestaciones_id: $im_conf_prestaciones_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener el detalle de las conf de prestaciones',
                data:  $detalle_conf);
        }

        return $detalle_conf;
    }

    public function obten_detalle(int $em_empleado_id, string $fecha_inicio_rel){
        $detalles = $this->obten_conf(em_empleado_id: $em_empleado_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener detalle',data:  $detalles);
        }

        $fecha_calculo = date('Y-m-d');
        $years = $this->obten_years(fecha_calculo: $fecha_calculo, fecha_inicio_rel: $fecha_inicio_rel);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener años de trabajo',data:  $years);
        }

        $datos_sdi = array();
        foreach ($detalles as $detalle){
            if((int)$detalle['im_detalle_conf_prestaciones_n_year'] === (int)$years){
                $datos_sdi = $detalle;
            }
        }

        return $datos_sdi;
    }

    public function obten_factor(int $em_empleado_id, string $fecha_inicio_rel): float|array
    {
        $detalle = $this->obten_detalle(em_empleado_id: $em_empleado_id, fecha_inicio_rel: $fecha_inicio_rel);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener detalle',data:  $detalle);
        }

        $prima_vacacional = round((float)$detalle['im_detalle_conf_prestaciones_n_dias_vacaciones']*.25,4);
        $prima_mas_aguinaldo = round($prima_vacacional+
            (float)$detalle['im_detalle_conf_prestaciones_n_dias_aguinaldo'],4);
        $dias_sdi = round($prima_mas_aguinaldo+365,4);

        return round($dias_sdi/365, 4);
    }




    private function obten_years(string $fecha_calculo, string $fecha_inicio_rel): int|array
    {
        $fecha_calculo = trim($fecha_calculo);
        if($fecha_calculo === ''){
            return $this->error->error("Error fecha calculo esta vacia",$fecha_calculo);
        }
        $fecha_inicio_rel = trim($fecha_inicio_rel);
        if($fecha_inicio_rel === ''){
            return $this->error->error("Error fecha_inicio_rel esta vacia",$fecha_inicio_rel);
        }

        $valida = $this->validacion->valida_fecha($fecha_calculo);
        if(errores::$error){
            return $this->error->error("Error al validar fecha_calculo",$valida);
        }

        $valida = $this->validacion->valida_fecha($fecha_inicio_rel);
        if(errores::$error){
            return $this->error->error("Error al validar fecha_inicio_rel",$valida);
        }

        if($fecha_inicio_rel>$fecha_calculo){
            return $this->error->error("Error la fecha inicio rel laboral debe ser mas antigua que la fecha 
            calculada",$fecha_calculo);
        }

        try {
            $date1 = new DateTime($fecha_inicio_rel);
            $date2 = new DateTime($fecha_calculo);
            $diff = $date1->diff($date2);
        }
        catch (Throwable $e){
            return $this->error->error(mensaje:"Error al calcular fecha",data: $e);
        }


        return $diff->y;
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

    private function rfc(array $registro): array
    {
        if (!isset($registro['rfc'])) {
            $registro['rfc'] = 'AAA010101AAA';
        }
        return $registro;
    }
}