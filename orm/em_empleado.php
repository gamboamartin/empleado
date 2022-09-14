<?php
namespace gamboamartin\empleado\models;
use base\orm\modelo;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class em_empleado extends modelo{

    public function __construct(PDO $link){
        $tabla = 'em_empleado';
        $columnas = array($tabla=>false, 'im_registro_patronal'=>$tabla, 'cat_sat_regimen_fiscal'=>$tabla,
            'dp_calle_pertenece'=>$tabla);
        $campos_obligatorios = array('nombre','descripcion','codigo','descripcion_select','alias','codigo_bis');

        parent::__construct(link: $link,tabla:  $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas);
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
            $registro['descripcion'] .= $registro['am'].' '.$registro['rfc'];
        }

        $this->registro['descripcion'] = $registro['descripcion'];

        if(!isset($registro['alias'])){
            $registro['alias'] = $registro['descripcion'];
        }

        $this->registro['alias'] = $registro['alias'];

        if (!isset($registro['descripcion_select'])) {
            $registro['descripcion_select'] = $registro['nombre'].' '.$registro['ap'].' ';
            $registro['descripcion_select'] .= $registro['am'].' '.$registro['rfc'];
        }

        $this->registro['descripcion_select'] = $registro['descripcion_select'];

        if (!isset($registro['codigo_bis'])) {
            $registro['codigo_bis'] = $registro['codigo'];
        }

        $this->registro['codigo_bis'] = $registro['codigo_bis'];

        $r_alta_bd = parent::alta_bd(); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al dar de alta empleado',data: $r_alta_bd);
        }

        return $r_alta_bd;

    }

    public function modifica_bd(array $registro, int $id, bool $reactiva = false): array|stdClass
    {

        $registro['codigo_bis'] = $registro['codigo'];

        $registro['descripcion_select'] = $registro['nombre'].' '.$registro['ap'].' ';
        $registro['descripcion_select'] .= $registro['am'].' '.$registro['rfc'];

        $registro['descripcion'] = $registro['nombre'].' '.$registro['ap'].' ';
        $registro['descripcion'] .= $registro['am'].' '.$registro['rfc'];

        $registro['alias'] = $registro['descripcion'];

        $r_modifica_bd = parent::modifica_bd($registro, $id, $reactiva); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar empleado',data: $r_modifica_bd);
        }

        return $r_modifica_bd;

    }
}