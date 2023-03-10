<?php

namespace gamboamartin\empleado\models;

use base\orm\_modelo_parent;

use gamboamartin\errores\errores;
use PDO;
use stdClass;

class em_metodo_calculo extends _modelo_parent
{
    public function __construct(PDO $link)
    {
        $tabla = 'em_metodo_calculo';
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
            return $this->error->error(mensaje: 'Error al insertar tipo descuento', data: $r_alta_bd);
        }

        return $r_alta_bd;
    }
}