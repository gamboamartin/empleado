<?php
namespace gamboamartin\empleado\instalacion;
use gamboamartin\administrador\instalacion\_adm;
use gamboamartin\administrador\models\_instalacion;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class instalacion
{




    private function _add_em_abono_anticipo(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'em_abono_anticipo');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        $columnas = new stdClass();

        $campos_new = array('monto');

        $columnas = $init->campos_double(campos: $columnas,campos_new:  $campos_new);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar campo double', data:  $columnas);
        }

        $columnas->fecha = new stdClass();
        $columnas->fecha->tipo_dato = 'DATE';
        $columnas->fecha->default = '1900-01-01';

        $add_colums = $init->add_columns(campos: $columnas,table:  'em_abono_anticipo');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add_colums);
        }
        $out->add_colums_base = $add_colums;


        $foraneas = array();
        $foraneas['em_tipo_abono_anticipo_id'] = new stdClass();
        $foraneas['em_anticipo_id'] = new stdClass();
        $foraneas['cat_sat_forma_pago_id'] = new stdClass();

        $result = $init->foraneas(foraneas: $foraneas,table:  'em_abono_anticipo');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $result);
        }

        $out->foraneas = $result;


        return $out;
    }

    private function _add_em_tipo_abono_anticipo(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'em_tipo_abono_anticipo');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;



        return $out;
    }

    private function _add_em_tipo_anticipo(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'em_tipo_anticipo');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;



        return $out;
    }
    private function _add_em_clase_riesgo(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'em_clase_riesgo');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        $columnas = new stdClass();
        $columnas->factor = new stdClass();
        $columnas->factor->tipo_dato = 'DOUBLE';

        $add_colums = $init->add_columns(campos: $columnas,table:  'em_clase_riesgo');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add_colums);
        }
        $out->add_colums_base = $add_colums;



        return $out;
    }


    private function _add_em_anticipo(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'em_anticipo');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        $foraneas = array();
        $foraneas['em_tipo_anticipo_id'] = new stdClass();

        $result = $init->foraneas(foraneas: $foraneas,table:  'em_anticipo');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $result);
        }

        $out->foraneas = $result;



        return $out;
    }


    private function em_tipo_abono_anticipo(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $create = $this->_add_em_tipo_abono_anticipo(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $out->create = $create;

        $adm_menu_descripcion = 'Abonos';
        $adm_sistema_descripcion = 'empleado';
        $etiqueta_label = 'Tipos de Abonos Anticipos';
        $adm_seccion_pertenece_descripcion = 'em_tipo_abono_anticipo';
        $adm_namespace_name = 'gamboamartin/empleado';
        $adm_namespace_descripcion = 'gamboa.martin/empleado';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__,
            adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion,
            etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }



        return $out;

    }

    private function em_tipo_anticipo(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $create = $this->_add_em_tipo_anticipo(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $out->create = $create;

        $adm_menu_descripcion = 'Anticipos';
        $adm_sistema_descripcion = 'empleado';
        $etiqueta_label = 'Tipos de Anticipos';
        $adm_seccion_pertenece_descripcion = 'em_tipo_anticipo';
        $adm_namespace_name = 'gamboamartin/empleado';
        $adm_namespace_descripcion = 'gamboa.martin/empleado';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__,
            adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion,
            etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }



        return $out;

    }

    private function em_clase_riesgo(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $create = $this->_add_em_clase_riesgo(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $out->create = $create;

        $adm_menu_descripcion = 'IMSS';
        $adm_sistema_descripcion = 'empleado';
        $etiqueta_label = 'Clases de Riesgo';
        $adm_seccion_pertenece_descripcion = 'em_clase_riesgo';
        $adm_namespace_name = 'gamboamartin/empleado';
        $adm_namespace_descripcion = 'gamboa.martin/empleado';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__,
            adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion,
            etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }



        return $out;

    }

    private function em_anticipo(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $create = $this->_add_em_anticipo(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $out->create = $create;

        $adm_menu_descripcion = 'Anticipos';
        $adm_sistema_descripcion = 'empleado';
        $etiqueta_label = 'Anticipos';
        $adm_seccion_pertenece_descripcion = 'em_anticipo';
        $adm_namespace_name = 'gamboamartin/empleado';
        $adm_namespace_descripcion = 'gamboa.martin/empleado';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__,
            adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion,
            etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }



        return $out;

    }


    private function em_abono_anticipo(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $create = $this->_add_em_abono_anticipo(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $out->create = $create;

        $adm_menu_descripcion = 'Abonos';
        $adm_sistema_descripcion = 'empleado';
        $etiqueta_label = 'Abonos Anticipos';
        $adm_seccion_pertenece_descripcion = 'em_abono_anticipo';
        $adm_namespace_name = 'gamboamartin/empleado';
        $adm_namespace_descripcion = 'gamboa.martin/empleado';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__,
            adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion,
            etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }



        return $out;

    }

    final public function instala(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $em_abono_tipo_anticipo = $this->em_tipo_abono_anticipo(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar $em_abono_tipo_anticipo', data:  $em_abono_tipo_anticipo);
        }
        $out->em_abono_tipo_anticipo = $em_abono_tipo_anticipo;

        $em_tipo_anticipo = $this->em_tipo_anticipo(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar em_tipo_anticipo', data:  $em_tipo_anticipo);
        }
        $out->em_abono_tipo_anticipo = $em_abono_tipo_anticipo;

        $em_clase_riesgo = $this->em_clase_riesgo(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar em_clase_riesgo', data:  $em_clase_riesgo);
        }
        $out->em_clase_riesgo = $em_clase_riesgo;

        $em_anticipo = $this->em_anticipo(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar em_anticipo', data:  $em_anticipo);
        }
        $out->em_anticipo = $em_anticipo;

        $em_abono_anticipo = $this->em_abono_anticipo(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar em_abono_anticipo', data:  $em_abono_anticipo);
        }
        $out->em_abono_anticipo = $em_abono_anticipo;

        return $out;

    }


}
