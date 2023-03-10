<?php

namespace gamboamartin\empleado\models;

use base\orm\_modelo_parent;
use base\orm\modelo;

use gamboamartin\errores\errores;
use PDO;
use stdClass;

class em_tipo_anticipo extends _modelo_parent
{

    public function __construct(PDO $link)
    {
        $tabla = 'em_tipo_anticipo';
        $columnas = array($tabla => false);
        $campos_obligatorios = array('descripcion', 'codigo');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas);

        $this->NAMESPACE = __NAMESPACE__;
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        if (!isset($this->registro['codigo'])) {
            $codigo = $this->get_codigo_aleatorio();
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al generar codigo', data: $codigo);
            }
        }

        $r_alta_bd = parent::alta_bd($keys_integra_ds);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar tipo anticipo', data: $r_alta_bd);
        }

        return $r_alta_bd;
    }

    public function get_tipo_anticipos(int $em_empleado_id): array|stdClass
    {
        $extra_join["em_anticipo"]["key"] = "em_tipo_anticipo_id";
        $extra_join["em_anticipo"]["enlace"] = "em_tipo_anticipo";
        $extra_join["em_anticipo"]["key_enlace"] = "id";
        $extra_join["em_anticipo"]["renombre"] = "em_anticipo";

        $filtro['em_anticipo.em_empleado_id'] = $em_empleado_id;
        $group_by = array('em_tipo_anticipo.id');

        $em_tipo_anticipo = (new em_tipo_anticipo($this->link))->filtro_and(extra_join: $extra_join, filtro: $filtro,
            group_by: $group_by);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener los tipos de anticipos del empleado', data: $em_tipo_anticipo);
        }

        return $em_tipo_anticipo;
    }
}