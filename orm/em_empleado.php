<?php
namespace gamboamartin\empleado\models;


use base\orm\modelo;
use DateTime;
use gamboamartin\cat_sat\models\cat_sat_regimen_fiscal;
use gamboamartin\cat_sat\models\cat_sat_tipo_jornada_nom;
use gamboamartin\cat_sat\models\cat_sat_tipo_regimen_nom;
use gamboamartin\direccion_postal\models\dp_calle_pertenece;
use gamboamartin\errores\errores;
use gamboamartin\organigrama\models\org_puesto;
use models\im_conf_pres_empresa;
use models\im_detalle_conf_prestaciones;
use models\im_registro_patronal;
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

        $campos_view = array(
            'dp_calle_pertenece_id' => array('type' => 'selects', 'model' => new dp_calle_pertenece($link)),
            'cat_sat_regimen_fiscal_id' => array('type' => 'selects', 'model' => new cat_sat_regimen_fiscal($link)),
            'org_puesto_id' => array('type' => 'selects', 'model' => new org_puesto($link)),
            'cat_sat_tipo_regimen_nom_id' => array('type' => 'selects', 'model' => new cat_sat_tipo_regimen_nom($link)),
            'im_registro_patronal_id' => array('type' => 'selects', 'model' => new im_registro_patronal($link)),
            'cat_sat_tipo_jornada_nom_id' => array('type' => 'selects', 'model' => new cat_sat_tipo_jornada_nom($link)),
            'fecha_inicio_rel_laboral' => array('type' => 'dates'), 'codigo' => array('type' => 'inputs'),
            'nombre' => array('type' => 'inputs'), 'ap' => array('type' => 'inputs'),'am' => array('type' => 'inputs'),
            'telefono' => array('type' => 'inputs'), 'rfc' => array('type' => 'inputs'),
            'curp' => array('type' => 'inputs'), 'nss' => array('type' => 'inputs'),
            'salario_diario' => array('type' => 'inputs'), 'salario_diario_integrado' => array('type' => 'inputs'));

        $tipo_campos = array();
        $tipo_campos['rfc'] = 'rfc';

        parent::__construct(link: $link,tabla:  $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas,campos_view: $campos_view, tipo_campos: $tipo_campos);

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


        $r_alta_bd = parent::alta_bd();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al dar de alta empleado',data: $r_alta_bd);
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

    private function descripcion_select(array $registro): array
    {
        if (!isset($registro['descripcion_select'])) {
            $registro['descripcion_select'] = $registro['rfc'];
            $registro['descripcion_select'] .= $registro['nombre'].' '.$registro['ap'].' ';
            $registro['descripcion_select'] .= $registro['am'];
            $registro['descripcion_select'] = strtoupper($registro['descripcion_select']);
        }
        return $registro;
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
}