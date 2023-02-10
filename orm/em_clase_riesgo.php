<?php
namespace gamboamartin\empleado\models;
use base\orm\_modelo_parent;


use gamboamartin\errores\errores;
use PDO;
use stdClass;

class em_clase_riesgo extends _modelo_parent{

    public function __construct(PDO $link){
        $tabla = 'em_clase_riesgo';
        $columnas = array($tabla=>false);
        $campos_obligatorios = array('descripcion','codigo','descripcion_select','alias','codigo_bis','factor');

        $campos_view['codigo'] = array('type' => 'inputs');
        $campos_view['descripcion'] = array('type' => 'inputs');
        $campos_view['factor']['type'] = array('type' => 'inputs');


        parent::__construct(link: $link,tabla:  $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas,campos_view: $campos_view);

        $this->NAMESPACE = __NAMESPACE__;
    }

    /**
     * Inserta un registro de tipo em_clase_riesgo
     * @param array $keys_integra_ds Genera la descripcion select basada en descripcion y factor
     * @return array|stdClass
     */
    public function alta_bd(array $keys_integra_ds = array('descripcion', 'factor')): array|stdClass
    {

        if(!isset($this->registro['codigo'])){
            $codigo = $this->registro['descripcion'].' '.$this->registro['factor'];
            $this->registro['codigo'] = $codigo;
        }

        $r_alta_bd = parent::alta_bd(keys_integra_ds: $keys_integra_ds); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar clase_riesgo',data:  $r_alta_bd);
        }
        return $r_alta_bd;

    }

    public function modifica_bd(array $registro, int $id, bool $reactiva = false, array $keys_integra_ds = array('codigo', 'factor')): array|stdClass
    {
        $em_clase_riesgo_previa = $this->registro(registro_id: $id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener clase_riesgo',data:  $em_clase_riesgo_previa);
        }

        if(!isset($registro['codigo'])){
            if(!isset($registro['descripcion'])){
                $descripcion_previa = $em_clase_riesgo_previa['em_clase_riesgo_descripcion'];
            }
            else{
                $descripcion_previa = $registro['descripcion'];
            }
            if(!isset($registro['factor'])){
                $factor_previa = $em_clase_riesgo_previa['em_clase_riesgo_factor'];
            }
            else{
                $factor_previa = $registro['factor'];
            }
            $codigo = $descripcion_previa.' '.$factor_previa;
            $registro['codigo'] = $codigo;
        }

        if(!isset($registro['factor'])){
            $registro['factor'] = $em_clase_riesgo_previa['em_clase_riesgo_factor'];
        }

        $r_modifica_bd =  parent::modifica_bd($registro, $id, $reactiva, $keys_integra_ds); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar clase_riesgo',data:  $r_modifica_bd);
        }
        return $r_modifica_bd;
    }


}