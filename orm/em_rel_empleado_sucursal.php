<?php

namespace gamboamartin\empleado\models;

use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class em_rel_empleado_sucursal extends _modelo_parent
{
    public function __construct(PDO $link)
    {
        $tabla = 'em_rel_empleado_sucursal';
        $columnas = array($tabla => false, 'em_empleado' => $tabla, 'com_sucursal' => $tabla);
        $campos_obligatorios = array('codigo', 'descripcion', 'em_empleado_id', 'com_sucursal_id');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
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

        if (!isset($this->registro['descripcion'])) {
            $this->registro['descripcion'] = $this->registro['codigo'];
        }

        $r_alta_bd = parent::alta_bd();
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al dar de alta relacion empleado sucursal', data: $r_alta_bd);
        }

        return $r_alta_bd;
    }

    public function maqueta_em_rel_empleado_sucursal(array $data): array
    {
        $salida = array();

        if (isset($data['codigo'])) {
            $salida['codigo'] = $data['codigo'];
        }

        if (isset($data['descripcion'])) {
            $salida['descripcion'] = $data['descripcion'];
        }

        $salida['em_empleado_id'] = $data['em_empleado_id'];
        $salida['com_sucursal_id'] = $data['com_sucursal_id'];

        return $salida;
    }

    public function inserta_em_rel_empleado_sucursal(array $data): array|stdClass
    {
        $data = $this->maqueta_em_rel_empleado_sucursal(data: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al maquetar datos para relacion empleado sucursal', data: $data);
        }

        $respuesta = $this->alta_registro(registro: $data);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al ingresar relacion empleado sucursal', data: $respuesta);
        }

        return $respuesta;
    }


}