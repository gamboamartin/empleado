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

    private function _add_em_empleado(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'em_empleado');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        $columnas = new stdClass();

        $columnas->nombre = new stdClass();
        $columnas->ap = new stdClass();
        $columnas->am = new stdClass();
        $columnas->telefono = new stdClass();
        $columnas->rfc = new stdClass();
        $columnas->curp = new stdClass();
        $columnas->nss = new stdClass();
        $columnas->fecha_inicio_rel_laboral = new stdClass();
        $columnas->fecha_inicio_rel_laboral->tipo_dato = 'DATE';
        $columnas->salario_diario = new stdClass();
        $columnas->salario_diario->tipo_dato = 'DOUBLE';

        $columnas->salario_diario_integrado = new stdClass();
        $columnas->salario_diario_integrado->tipo_dato = 'DOUBLE';

        $columnas->fecha_antiguedad = new stdClass();
        $columnas->fecha_antiguedad->tipo_dato = 'DATE';

        $columnas->salario_total = new stdClass();
        $columnas->salario_total->tipo_dato = 'DOUBLE';

        $columnas->correo = new stdClass();

        $add_colums = $init->add_columns(campos: $columnas,table:  'em_empleado');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add_colums);
        }
        $out->add_colums_base = $add_colums;


        $foraneas = array();
        $foraneas['dp_calle_pertenece_id'] = new stdClass();
        $foraneas['cat_sat_regimen_fiscal_id'] = new stdClass();
        $foraneas['em_registro_patronal_id'] = new stdClass();
        $foraneas['org_puesto_id'] = new stdClass();
        $foraneas['cat_sat_tipo_regimen_nom_id'] = new stdClass();
        $foraneas['cat_sat_tipo_jornada_nom_id'] = new stdClass();
        $foraneas['em_centro_costo_id'] = new stdClass();

        $result = $init->foraneas(foraneas: $foraneas,table:  'em_empleado');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $result);
        }

        $out->foraneas = $result;


        return $out;
    }

    private function _add_em_cuenta_bancaria(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'em_cuenta_bancaria');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        $columnas = new stdClass();

        $columnas->num_cuenta = new stdClass();
        $columnas->clabe = new stdClass();

        $add_colums = $init->add_columns(campos: $columnas,table:  'em_cuenta_bancaria');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add_colums);
        }
        $out->add_colums_base = $add_colums;


        $foraneas = array();
        $foraneas['em_empleado_id'] = new stdClass();
        $foraneas['bn_sucursal_id'] = new stdClass();

        $result = $init->foraneas(foraneas: $foraneas,table:  'em_cuenta_bancaria');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $result);
        }

        $out->foraneas = $result;


        return $out;
    }

    private function _add_em_emp_dir_pendiente(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'em_emp_dir_pendiente');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        $columnas = new stdClass();

        $columnas->dp_direccion_pendiente_id = new stdClass();
        $columnas->dp_direccion_pendiente_id->tipo_dato = 'BIGINT';
        $columnas->dp_direccion_pendiente_id->default = '1';

        $add_colums = $init->add_columns(campos: $columnas,table:  'em_emp_dir_pendiente');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add_colums);
        }
        $out->add_colums_base = $add_colums;


        $foraneas = array();
        $foraneas['em_empleado_id'] = new stdClass();

        $result = $init->foraneas(foraneas: $foraneas,table:  'em_emp_dir_pendiente');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $result);
        }

        $out->foraneas = $result;


        return $out;
    }

    private function _add_em_rel_empleado_sucursal(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'em_rel_empleado_sucursal');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;



        $foraneas = array();
        $foraneas['em_empleado_id'] = new stdClass();
        $foraneas['com_sucursal_id'] = new stdClass();

        $result = $init->foraneas(foraneas: $foraneas,table:  'em_rel_empleado_sucursal');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $result);
        }

        $out->foraneas = $result;


        return $out;
    }

    private function _add_em_registro_patronal(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'em_registro_patronal');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;


        $foraneas = array();
        $foraneas['fc_csd_id'] = new stdClass();
        $foraneas['cat_sat_isn_id'] = new stdClass();
        $foraneas['em_clase_riesgo_id'] = new stdClass();

        $result = $init->foraneas(foraneas: $foraneas,table:  'em_registro_patronal');

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

    private function _add_em_tipo_descuento(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'em_tipo_descuento');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        $columnas = new stdClass();
        $columnas->monto = new stdClass();
        $columnas->monto->tipo_dato = 'DOUBLE';

        $add_colums = $init->add_columns(campos: $columnas,table:  'em_tipo_descuento');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add_colums);
        }
        $out->add_colums_base = $add_colums;

        $foraneas = array();
        $foraneas['em_metodo_calculo_id'] = new stdClass();

        $result = $init->foraneas(foraneas: $foraneas,table:  'em_tipo_descuento');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar foraneas', data:  $result);
        }


        return $out;
    }

    private function _add_em_metodo_calculo(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'em_metodo_calculo');
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

    private function _add_em_centro_costo(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'em_centro_costo');
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


        $columnas = new stdClass();
        $columnas->monto = new stdClass();
        $columnas->monto->tipo_dato = 'DOUBLE';

        $columnas->fecha_inicio_descuento = new stdClass();
        $columnas->fecha_inicio_descuento->tipo_dato = 'DATE';

        $columnas->fecha_prestacion = new stdClass();
        $columnas->fecha_prestacion->tipo_dato = 'DATE';

        $columnas->n_pagos = new stdClass();
        $columnas->n_pagos->tipo_dato = 'BIGINT';

        $add_colums = $init->add_columns(campos: $columnas,table:  'em_anticipo');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add_colums);
        }

        $foraneas = array();
        $foraneas['em_tipo_anticipo_id'] = new stdClass();
        $foraneas['em_empleado_id'] = new stdClass();
        $foraneas['em_tipo_descuento_id'] = new stdClass();

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

    private function em_tipo_descuento(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $create = $this->_add_em_tipo_descuento(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $out->create = $create;

        $adm_menu_descripcion = 'Abonos';
        $adm_sistema_descripcion = 'empleado';
        $etiqueta_label = 'Tipos de Descuento';
        $adm_seccion_pertenece_descripcion = 'em_tipo_descuento';
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

    private function em_metodo_calculo(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $create = $this->_add_em_metodo_calculo(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $out->create = $create;

        $adm_menu_descripcion = 'Abonos';
        $adm_sistema_descripcion = 'empleado';
        $etiqueta_label = 'Metodos de calculo';
        $adm_seccion_pertenece_descripcion = 'em_metodo_calculo';
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

    private function em_centro_costo(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $create = $this->_add_em_centro_costo(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $out->create = $create;

        $adm_menu_descripcion = 'Generales';
        $adm_sistema_descripcion = 'empleado';
        $etiqueta_label = 'Centros de Costo Nom';
        $adm_seccion_pertenece_descripcion = 'em_centro_costo';
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

    private function em_empleado(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $create = $this->_add_em_empleado(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $out->create = $create;

        $adm_menu_descripcion = 'IMSS';
        $adm_sistema_descripcion = 'empleado';
        $etiqueta_label = 'Empleados';
        $adm_seccion_pertenece_descripcion = 'em_empleado';
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

    private function em_cuenta_bancaria(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $create = $this->_add_em_cuenta_bancaria(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $out->create = $create;

        $adm_menu_descripcion = 'Banco';
        $adm_sistema_descripcion = 'empleado';
        $etiqueta_label = 'Cuentas de empleados';
        $adm_seccion_pertenece_descripcion = 'em_cuenta_bancaria';
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

    private function em_emp_dir_pendiente(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $create = $this->_add_em_emp_dir_pendiente(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $out->create = $create;





        return $out;

    }

    private function em_rel_empleado_sucursal(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $create = $this->_add_em_rel_empleado_sucursal(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $out->create = $create;





        return $out;

    }

    private function em_registro_patronal(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $create = $this->_add_em_registro_patronal(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $out->create = $create;

        $adm_menu_descripcion = 'IMSS';
        $adm_sistema_descripcion = 'empleado';
        $etiqueta_label = 'Registro Patronal';
        $adm_seccion_pertenece_descripcion = 'em_registro_patronal';
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

        $em_metodo_calculo = $this->em_metodo_calculo(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar em_metodo_calculo', data:  $em_metodo_calculo);
        }

        $em_tipo_descuento = $this->em_tipo_descuento(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar em_tipo_descuento', data:  $em_tipo_descuento);
        }

        $em_centro_costo = $this->em_centro_costo(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar em_centro_costo', data:  $em_centro_costo);
        }
        $out->em_centro_costo = $em_centro_costo;

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

        $em_registro_patronal = $this->em_registro_patronal(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar em_registro_patronal', data:  $em_registro_patronal);
        }
        $out->em_registro_patronal = $em_registro_patronal;

        $em_empleado = $this->em_empleado(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar em_empleado', data:  $em_empleado);
        }
        $out->em_empleado = $em_empleado;



        $em_cuenta_bancaria = $this->em_cuenta_bancaria(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar em_cuenta_bancaria', data:  $em_cuenta_bancaria);
        }
        $out->em_cuenta_bancaria = $em_cuenta_bancaria;

        $em_emp_dir_pendiente = $this->em_emp_dir_pendiente(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar em_emp_dir_pendiente', data:  $em_emp_dir_pendiente);
        }
        $out->em_emp_dir_pendiente = $em_emp_dir_pendiente;

        $em_rel_empleado_sucursal = $this->em_rel_empleado_sucursal(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar em_rel_empleado_sucursal', data:  $em_rel_empleado_sucursal);
        }
        $out->em_rel_empleado_sucursal = $em_rel_empleado_sucursal;

        return $out;

    }


}
