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

class em_empleado extends modelo{

    public function __construct(PDO $link){
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
            'em_empleado_id' => array('type' => 'selects', 'model' => $this),
            'em_tipo_anticipo_id' => array('type' => 'selects', 'model' => new em_tipo_anticipo($link)),
            'fecha_inicio_rel_laboral' => array('type' => 'dates'), 'fecha_prestacion' => array('type' => 'dates'),
            'monto' => array('type' => 'inputs'),'codigo' => array('type' => 'inputs'),
            'nombre' => array('type' => 'inputs'),'ap' => array('type' => 'inputs'),'am' => array('type' => 'inputs'),
            'telefono' => array('type' => 'inputs'),'rfc' => array('type' => 'inputs'),'curp' => array('type' => 'inputs'),
            'nss' => array('type' => 'inputs'),'salario_diario' => array('type' => 'inputs'),
            'salario_diario_integrado' => array('type' => 'inputs'));

        parent::__construct(link: $link,tabla:  $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas,campos_view: $campos_view);

        $this->NAMESPACE = __NAMESPACE__;
    }

    public function alta_bd(): array|stdClass
    {
        $registro = $this->registro;
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar registro',data: $registro);
        }

        $keys = array('nombre','ap');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys, registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data: $valida);
        }

        if(!isset($registro['am'])){
            $registro['am'] = '';
        }

        if (!isset($registro['descripcion'])) {
            $registro['descripcion'] = $registro['nombre'].' '.$registro['ap'].' ';
            $registro['descripcion'] .= $registro['am'];
        }

        $this->registro['descripcion'] = $registro['descripcion'];

        if(!isset($registro['alias'])){
            $registro['alias'] = $registro['descripcion'];
        }

        $this->registro['alias'] = $registro['alias'];

        if (!isset($registro['descripcion_select'])) {
            $registro['descripcion_select'] = $registro['nombre'].' '.$registro['ap'].' ';
            $registro['descripcion_select'] .= $registro['am'];
        }

        $this->registro['descripcion_select'] = $registro['descripcion_select'];

        if (!isset($registro['codigo_bis'])) {
            $registro['codigo_bis'] = $registro['codigo'];
        }

        $this->registro['codigo_bis'] = $registro['codigo_bis'];

        if (!isset($registro['org_puesto_id'])) {
            $registro['org_puesto_id'] =  (new org_puesto($this->link))->get_puesto_default_id();
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener get_puesto_default_id',data: $registro['org_puesto_id']);
            }
            $this->registro['org_puesto_id'] = $registro['org_puesto_id'];
        }

        if (!isset($registro['cat_sat_tipo_jornada_nom_id'])) {
            $cat_tipo_jornada_nom_id =  (new cat_sat_tipo_jornada_nom($this->link))->id_predeterminado();
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener cat_tipo_jornada_nom_id',data: $cat_tipo_jornada_nom_id);
            }
            $this->registro['cat_sat_tipo_jornada_nom_id'] = $cat_tipo_jornada_nom_id;
        }

        $r_alta_bd = parent::alta_bd(); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al dar de alta empleado',data: $r_alta_bd);
        }

        return $r_alta_bd;

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

    /**
     * Obtiene empresa a partir de empleado
     * @param int $em_empleado_id Identificador del empleado a revisar su empresa
     * @return array|stdClass
     * @version
     */
    public function get_empresa(int $em_empleado_id){
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

    public function obten_conf(int $em_empleado_id){
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

    public function obten_factor(int $em_empleado_id, string $fecha_inicio_rel){
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

    public function calcula_sdi(int $em_empleado_id, string $fecha_inicio_rel, float $salario_diario){
        $factor = $this->obten_factor(em_empleado_id: $em_empleado_id, fecha_inicio_rel: $fecha_inicio_rel);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener factor',data:  $factor);
        }

        $sdi = $salario_diario * $factor;

        return round($sdi,2);
    }

    /**
     * @throws \Exception
     */
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

        $date1 = new DateTime($fecha_inicio_rel);
        $date2 = new DateTime($fecha_calculo);
        $diff = $date1->diff($date2);

        return $diff->y;
    }
}