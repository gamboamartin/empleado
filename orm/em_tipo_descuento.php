<?php

namespace gamboamartin\empleado\models;

use base\orm\_modelo_parent;

use gamboamartin\errores\errores;
use PDO;
use stdClass;

class em_tipo_descuento extends _modelo_parent
{

    public function __construct(PDO $link)
    {
        $tabla = 'em_tipo_descuento';
        $columnas = array($tabla => false, 'em_metodo_calculo' => $tabla);
        $campos_obligatorios = array('monto', 'em_metodo_calculo_id');

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

        if (!isset($this->registro['descripcion'])) {
            $this->registro['descripcion'] = $this->registro['monto'];
        }

        $r_alta_bd = parent::alta_bd($keys_integra_ds);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar tipo descuento', data: $r_alta_bd);
        }

        return $r_alta_bd;
    }

    public function modifica_bd(array $registro, int $id, bool $reactiva = false,
                                array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        if (!isset($this->registro['descripcion'])) {
            $this->registro['descripcion'] = $this->registro['monto'];
        }

        $r_modifica_bd = parent::modifica_bd($registro, $id, $reactiva, $keys_integra_ds);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al modificar tipo descuento', data: $r_modifica_bd);
        }

        return $r_modifica_bd;
    }

}