<?php
namespace gamboamartin\empleado\models;
use base\orm\modelo;
use gamboamartin\cat_sat\models\cat_sat_isn;
use gamboamartin\errores\errores;
use gamboamartin\facturacion\models\fc_csd;
use PDO;
use stdClass;

class em_registro_patronal extends modelo{
    public function __construct(PDO $link){
        $tabla = "em_registro_patronal";
        $columnas = array($tabla=>false, 'fc_csd' => $tabla, 'cat_sat_isn'=>$tabla,'org_sucursal' => 'fc_csd',
            'org_empresa' => 'org_sucursal','dp_calle_pertenece'=>'org_sucursal',
            'dp_colonia_postal'=>'dp_calle_pertenece','dp_cp'=>'dp_colonia_postal','em_clase_riesgo'=>$tabla,
            'cat_sat_regimen_fiscal'=>'org_empresa');
        $campos_obligatorios = array('em_clase_riesgo_id','fc_csd_id','descripcion_select');

        $campos_view = array();
        $campos_view['fc_csd_id']['type'] = 'selects';
        $campos_view['fc_csd_id']['model'] = (new fc_csd($link));
        $campos_view['em_clase_riesgo_id']['type'] = 'selects';
        $campos_view['em_clase_riesgo_id']['model'] = (new em_clase_riesgo($link));
        $campos_view['descripcion']['type'] = "inputs";
        $campos_view['cat_sat_isn_id']['type'] = 'selects';
        $campos_view['cat_sat_isn_id']['model'] = (new cat_sat_isn($link));

        parent::__construct(link: $link,tabla:  $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas,campos_view:  $campos_view );
        $this->NAMESPACE = __NAMESPACE__;
    }

    private function alias(array $registro): array
    {
        if(!isset($registro['alias'])) {
            $registro['alias'] = $registro['codigo_bis'];
        }
        return $registro;
    }

    public function alta_bd(): array|stdClass
    {

        $valida = $this->valida_alta_bd(registro: $this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }

        $registro = $this->inicializa_row(registro: $this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar row',data:  $registro);
        }

        $this->registro = $registro;

        $r_alta_bd = parent::alta_bd();
        if(errores::$error){
            return $this->error->error('Error al dar de alta registro',$r_alta_bd);
        }

        return $r_alta_bd;
    }

    private function codigo(array $registro): array
    {
        if(!isset($registro['codigo'])){
            $registro['codigo'] = $registro['descripcion'];
        }
        return $registro;
    }

    private function codigo_bis(array $fc_csd, array $registro): array
    {
        if(!isset($registro['codigo_bis'])) {
            $registro['codigo_bis'] = $registro['codigo'] . ' ' . $fc_csd['org_empresa_rfc'];
        }
        return $registro;
    }

    private function descripcion_select(array $fc_csd, array $registro): array|string
    {
        if(!isset($registro['descripcion_select'])) {
            $registro['descripcion_select'] = $fc_csd['org_empresa_razon_social'] . ' ' . $registro['descripcion'];
        }
        return $registro;
    }

    private function inicializa_row(array $registro){
        $fc_csd = (new fc_csd(link: $this->link))->registro(registro_id: $registro['fc_csd_id']);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener el registro',data:  $fc_csd);
        }

        $registro = $this->init_row(fc_csd: $fc_csd, registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar row',data:  $registro);
        }
        return $registro;
    }

    private function init_row(array $fc_csd, array $registro){
        $registro = $this->codigo(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar codigo',data:  $registro);
        }

        $registro = $this->codigo_bis(fc_csd: $fc_csd, registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar codigo_bis',data:  $registro);
        }

        $registro = $this->alias(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar alias',data:  $registro);
        }

        $registro = $this->descripcion_select(fc_csd: $fc_csd, registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar descripcion_select',data:  $registro);
        }
        return $registro;
    }

    public function modifica_bd(array $registro, int $id, bool $reactiva = false): array|stdClass
    {


        $fc_csd = (new fc_csd(link: $this->link))->registro(registro_id: $registro['fc_csd_id']);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener el registro',data:  $fc_csd);
        }


        if(!isset($registro['codigo'])) {
            $registro['codigo'] = $registro['descripcion'];
        }

        if(!isset($registro['codigo_bis'])) {
            $registro['codigo_bis'] = $registro['codigo'] . ' ' . $fc_csd['org_empresa_rfc'];
        }

        if(!isset($registro['alias'])) {
            $registro['alias'] = $registro['codigo_bis'];
        }

        if(!isset($registro['descripcion_select'])) {
            $registro['descripcion_select'] = $fc_csd['org_empresa_razon_social'] . ' ' . $registro['descripcion'];
        }


        $r_modifica_bd = parent::modifica_bd($registro, $id, $reactiva); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error('Error al modificar registro',$r_modifica_bd);
        }

        return $r_modifica_bd;
    }

    /**
     * Valida que existan los elementos base de un alta
     * @param array $registro Registro en proceso
     * @return array|true
     */
    private function valida_alta_bd(array $registro): bool|array
    {
        $keys = array('fc_csd_id','em_clase_riesgo_id','cat_sat_isn_id');
        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }

        $keys = array('descripcion');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }
        return true;
    }
}