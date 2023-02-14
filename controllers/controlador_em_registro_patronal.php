<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\empleado\controllers;

use gamboamartin\direccion_postal\models\dp_colonia_postal;
use gamboamartin\empleado\models\em_clase_riesgo;
use gamboamartin\empleado\models\em_registro_patronal;
use gamboamartin\errores\errores;
use gamboamartin\facturacion\models\fc_csd;
use gamboamartin\system\links_menu;
use gamboamartin\system\system;
use gamboamartin\template\html;
use html\em_registro_patronal_html;
use PDO;
use stdClass;

class controlador_em_registro_patronal extends system {

    public array $keys_selects = array();

    public function __construct(PDO $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass()){
        $modelo = new em_registro_patronal(link: $link);
        $html_ = new em_registro_patronal_html(html: $html);
        $obj_link = new links_menu(link: $link, registro_id:$this->registro_id);

        $this->rows_lista[] = 'em_clase_riesgo_id';
        $this->rows_lista[] = 'fc_csd_id';

        $columns["em_registro_patronal_id"]["titulo"] = "Id";
        $columns["em_registro_patronal_codigo"]["titulo"] = "Código";
        $columns["em_registro_patronal_descripcion"]["titulo"] = "Registro Patronal";
        $columns["org_empresa_razon_social"]["titulo"] = "Razón Social";
        $columns["em_clase_riesgo_factor"]["titulo"] = "Prima de Riesgo";
        $columns["cat_sat_isn_descripcion"]["titulo"] = "ISN";

        $datatables = new stdClass();
        $datatables->columns = $columns;

        parent::__construct(html:$html_, link: $link,modelo:  $modelo, obj_link: $obj_link,datatables: $datatables,
            paths_conf: $paths_conf);


        $this->asignar_propiedad(identificador:'fc_csd_id', propiedades: ["label" => "CSD Sucursal"]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador:'em_clase_riesgo_id', propiedades: ["label" => "Clase de Riesgo."]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador:'cat_sat_isn_id', propiedades: ["label" => "ISN"]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador: 'descripcion', propiedades: ['place_holder'=> 'Descripcion']);

        $this->titulo_lista = 'Registro Patronal';
    }

    public function alta(bool $header, bool $ws = false): array|string
    {
        $r_alta =  parent::alta(header: false); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar template',data:  $r_alta, header: $header,ws:$ws);
        }

        $inputs = $this->genera_inputs(keys_selects:  $this->keys_selects);
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al generar inputs',data:  $inputs);
            print_r($error);
            die('Error');
        }

        return $r_alta;
    }

    public function asignar_propiedad(string $identificador, mixed $propiedades)
    {
        if (!array_key_exists($identificador,$this->keys_selects)){
            $this->keys_selects[$identificador] = new stdClass();
        }

        foreach ($propiedades as $key => $value){
            $this->keys_selects[$identificador]->$key = $value;
        }
    }

    private function base(): array|stdClass
    {
        $r_modifica =  parent::modifica(header: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al generar template',data:  $r_modifica);
        }

        $this->asignar_propiedad(identificador:'fc_csd_id',
            propiedades: ["id_selected"=>$this->row_upd->fc_csd_id]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador:'em_clase_riesgo_id',
            propiedades: ["id_selected"=>$this->row_upd->em_clase_riesgo_id]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador:'cat_sat_isn_id',
            propiedades: ["id_selected"=>$this->row_upd->cat_sat_isn_id]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }


        $inputs = $this->genera_inputs(keys_selects:  $this->keys_selects);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar inputs',data:  $inputs);
        }


        $data = new stdClass();
        $data->template = $r_modifica;
        $data->inputs = $inputs;

        return $data;
    }

    private function cat_sat_regimen_fiscal_descripcion_row(stdClass $row): array|stdClass
    {
        $keys = array('em_registro_patronal_id');
        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $row);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al validar row',data:  $valida);
        }

        $fc_csd = new fc_csd($this->link);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al genera modelo',data:  $fc_csd);
        }
        $r_fc_csd = $fc_csd->registro(registro_id: $row->em_registro_patronal_fc_csd_id);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener el registro',data:  $r_fc_csd);
        }

        $row->cat_sat_regimen_fiscal_descripcion = $r_fc_csd['cat_sat_regimen_fiscal_descripcion'];

        return $row;
    }

    private function dp_estado_descripcion_row(stdClass $row): array|stdClass
    {
        $keys = array('em_registro_patronal_id');
        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $row);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al validar row',data:  $valida);
        }

        $fc_csd = new fc_csd($this->link);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al genera modelo',data:  $fc_csd);
        }
        $r_fc_csd = $fc_csd->registro(registro_id: $row->em_registro_patronal_fc_csd_id);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener el registro',data:  $r_fc_csd);
        }


        $dp_colonia_postal = new dp_colonia_postal($this->link);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al genera modelo',data:  $dp_colonia_postal);
        }
        $r_dp_colonia_postal = $dp_colonia_postal->registro(registro_id: $r_fc_csd['dp_calle_pertenece_dp_colonia_postal_id']);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener el registro',data:  $r_dp_colonia_postal);
        }

        $row->dp_estado_descripcion = $r_dp_colonia_postal['dp_estado_descripcion'];

        return $row;
    }

    private function em_clase_riesgo_factor_row(stdClass $row): array|stdClass
    {
        $keys = array('em_registro_patronal_id');
        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $row);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al validar row',data:  $valida);
        }

        $em_clase_riesgo = new em_clase_riesgo($this->link);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al genera modelo',data:  $em_clase_riesgo);
        }
        $r_em_clase_riesgo = $em_clase_riesgo->registro(registro_id: $row->em_registro_patronal_em_clase_riesgo_id);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener el registro',data:  $r_em_clase_riesgo);
        }

        $row->em_clase_riesgo_factor = $r_em_clase_riesgo['em_clase_riesgo_factor'];

        return $row;
    }

    private function maqueta_registros_lista(array $registros): array
    {
        foreach ($registros as $indice=> $row){
            $row = $this->org_empresa_descripcion_row(row: $row);
            if(errores::$error){
                return $this->errores->error(mensaje: 'Error al maquetar row',data:  $row);
            }
            $registros[$indice] = $row;

            $row = $this->cat_sat_regimen_fiscal_descripcion_row(row: $row);
            if(errores::$error){
                return $this->errores->error(mensaje: 'Error al maquetar row',data:  $row);
            }
            $registros[$indice] = $row;

            $row = $this->org_empresa_rfc_row(row: $row);
            if(errores::$error){
                return $this->errores->error(mensaje: 'Error al maquetar row',data:  $row);
            }
            $registros[$indice] = $row;

            $row = $this->em_clase_riesgo_factor_row(row: $row);
            if(errores::$error){
                return $this->errores->error(mensaje: 'Error al maquetar row',data:  $row);
            }
            $registros[$indice] = $row;

            $row = $this->dp_estado_descripcion_row(row: $row);
            if(errores::$error){
                return $this->errores->error(mensaje: 'Error al maquetar row',data:  $row);
            }
            $registros[$indice] = $row;



        }
        return $registros;
    }

    public function modifica(bool $header, bool $ws = false): array|stdClass
    {
        $base = $this->base();
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar datos',data:  $base,
                header: $header,ws:$ws);
        }

        return $base->template;
    }

    private function org_empresa_descripcion_row(stdClass $row): array|stdClass
    {
        $keys = array('em_registro_patronal_id');
        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $row);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al validar row',data:  $valida);
        }

        $fc_csd = new fc_csd($this->link);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al genera modelo',data:  $fc_csd);
        }
        $r_fc_csd = $fc_csd->registro(registro_id: $row->em_registro_patronal_fc_csd_id);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener el registro',data:  $r_fc_csd);
        }

        $row->org_empresa_descripcion = $r_fc_csd['org_empresa_descripcion'];

        return $row;
    }

    private function org_empresa_rfc_row(stdClass $row): array|stdClass
    {
        $keys = array('em_registro_patronal_id');
        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $row);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al validar row',data:  $valida);
        }

        $fc_csd = new fc_csd($this->link);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al genera modelo',data:  $fc_csd);
        }
        $r_fc_csd = $fc_csd->registro(registro_id: $row->em_registro_patronal_fc_csd_id);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener el registro',data:  $r_fc_csd);
        }

        $row->org_empresa_rfc = $r_fc_csd['org_empresa_rfc'];

        return $row;
    }



}
