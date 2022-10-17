<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\empleado\controllers;

use gamboamartin\empleado\models\em_abono_anticipo;
use gamboamartin\empleado\models\em_anticipo;
use gamboamartin\errores\errores;
use gamboamartin\system\actions;
use gamboamartin\system\links_menu;
use gamboamartin\system\system;
use gamboamartin\template\html;
use html\em_anticipo_html;
use PDO;
use stdClass;

class controlador_em_anticipo extends system {

    public array $keys_selects = array();
    public string $link_em_abono_anticipo_alta_bd = '';
    public string $link_em_abono_anticipo_modifica_bd = '';

    public int $em_anticipo_id = -1;
    public int $em_abono_anticipo_id = -1;

    public controlador_em_abono_anticipo $controlador_em_abono_anticipo;
    public stdClass $abonos;

    public function __construct(PDO $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass()){
        $modelo = new em_anticipo(link: $link);
        $html_ = new em_anticipo_html(html: $html);
        $obj_link = new links_menu($this->registro_id);
        parent::__construct(html:$html_, link: $link,modelo:  $modelo, obj_link: $obj_link, paths_conf: $paths_conf);

        $this->titulo_lista = 'Anticipo';

        $this->controlador_em_abono_anticipo= new controlador_em_abono_anticipo($this->link);
        $this->abonos = new stdClass();

        $keys_rows_lista = $this->keys_rows_lista();
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al generar keys de lista', data: $keys_rows_lista);
            print_r($error);
            die('Error');
        }
        $this->keys_row_lista = $keys_rows_lista;


        $this->asignar_propiedad(identificador:'em_tipo_anticipo_id', propiedades: ["label" => "Tipo Anticipo"]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador:'em_empleado_id', propiedades: ["label" => "Empleado"]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador:'em_tipo_descuento_id', propiedades: ["label" => "Tipo Descuento"]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador:'n_pagos', propiedades: ["place_holder" => "Nº. Pagos"]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador:'fecha_prestacion', propiedades: ["place_holder" => "Fecha Prestacion"]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador:'fecha_inicio_descuento', propiedades: ["place_holder" => "Fecha Inicio Descuento"]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $link_em_abono_anticipo_alta_bd= $obj_link->link_con_id(accion: 'abono_alta_bd', registro_id: $this->registro_id,
            seccion: $this->seccion);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al generar link', data: $link_em_abono_anticipo_alta_bd);
            print_r($error);
            die('Error');
        }
        $this->link_em_abono_anticipo_alta_bd = $link_em_abono_anticipo_alta_bd;

        $link_em_abono_anticipo_modifica_bd = $obj_link->link_con_id(accion: 'abono_modifica_bd', registro_id: $this->registro_id,
            seccion: $this->seccion);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al generar link', data: $link_em_abono_anticipo_modifica_bd);
            print_r($error);
            die('Error');
        }
        $this->link_em_abono_anticipo_modifica_bd = $link_em_abono_anticipo_modifica_bd;

        if (isset($_GET['em_anticipo_id'])){
            $this->em_anticipo_id = $_GET['em_anticipo_id'];
        }

        if (isset($_GET['em_abono_anticipo_id'])){
            $this->em_abono_anticipo_id = $_GET['em_abono_anticipo_id'];
        }

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

    public function alta(bool $header, bool $ws = false): array|string
    {
        $r_alta =  parent::alta(header: false);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar template',data:  $r_alta, header: $header,ws:$ws);
        }

        $this->row_upd->fecha_prestacion = date('Y-m-d');
        $this->row_upd->fecha_inicio_descuento = date('Y-m-d');
        $this->row_upd->monto = 0;

        $inputs = $this->genera_inputs(keys_selects:  $this->keys_selects);
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al generar inputs',data:  $inputs);
            print_r($error);
            die('Error');
        }
        return $r_alta;
    }

    private function base(): array|stdClass
    {
        $r_modifica =  parent::modifica(header: false,aplica_form:  false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al generar template',data:  $r_modifica);
        }

        $this->asignar_propiedad(identificador:'id', propiedades: ["disable" => true]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador:'em_empleado_id',
            propiedades: ["id_selected"=> $this->row_upd->em_empleado_id, "disabled" => true,
                "filtro" => array('em_empleado.id' => $this->row_upd->em_empleado_id)]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador:'em_tipo_anticipo_id',
            propiedades: ["id_selected"=>$this->row_upd->em_tipo_anticipo_id]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador:'em_tipo_descuento_id',
            propiedades: ["id_selected"=>$this->row_upd->em_tipo_descuento_id]);
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

    private function keys_rows_lista(): array
    {
        $keys_rows_lista = array();
        $keys = array('em_anticipo_id','em_anticipo_descripcion','em_empleado_codigo','em_empleado_nombre','em_empleado_ap',
            'em_empleado_am','em_anticipo_monto','em_tipo_descuento_descripcion','em_anticipo_fecha_prestacion',
            'em_anticipo_saldo_pendiente','em_anticipo_saldo_pendiente');

        foreach ($keys as $campo) {
            $keys_rows_lista = $this->key_row_lista_init(campo: $campo,keys_rows_lista: $keys_rows_lista);
            if (errores::$error){
                return $this->errores->error(mensaje: "error al inicializar key",data: $keys_rows_lista);
            }
        }

        return $keys_rows_lista;
    }

    private function key_row_lista_init(string $campo, array $keys_rows_lista): array
    {
        $data = new stdClass();
        $data->campo = $campo;

        $campo = str_replace(array("em_anticipo", "em_", "_"), '', $campo);
        $campo = ucfirst(strtolower($campo));

        $data->name_lista = $campo;
        $keys_rows_lista[] = $data;

        return $keys_rows_lista;
    }


    private function asigna_link_row(stdClass $row, string $accion, string $propiedad, string $estilo): array|stdClass
    {
        $keys = array('em_anticipo_id');
        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $row);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al validar row',data:  $valida);
        }

        $link = $this->obj_link->link_con_id(accion: $accion,registro_id:  $row->em_anticipo_id,
            seccion:  $this->tabla);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al genera link',data:  $link);
        }

        $row->$propiedad = $link;
        $row->$estilo = 'info';

        return $row;
    }


    private function maqueta_registros_lista(array $registros): array
    {

        foreach ($registros as $indice=> $row){

            $row = $this->asigna_link_row(row: $row, accion: "abono",propiedad: "link_abono",
                estilo: "link_abono_style");
            if(errores::$error){
                return $this->errores->error(mensaje: 'Error al maquetar row',data:  $row);
            }
            $registros[$indice] = $row;

            $row->em_anticipo_saldo_pendiente = (new em_anticipo($this->link))->get_saldo_anticipo($row->em_anticipo_id);
            if(errores::$error){
                return $this->errores->error(mensaje: 'Error al obtener el saldo pendiente',data:  $row->em_anticipo_id);
            }

            $row->em_anticipo_total_abonado = (new em_abono_anticipo($this->link))->get_total_abonado($row->em_anticipo_id);
            if(errores::$error){
                return $this->errores->error(mensaje: 'Error al obtener el total abonado',data:  $row->em_anticipo_id);
            }
        }
        return $registros;
    }

    public function modifica(bool $header, bool $ws = false, string $breadcrumbs = '', bool $aplica_form = true,
                             bool $muestra_btn = true): array|string
    {
        $base = $this->base();
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar datos',data:  $base,
                header: $header,ws:$ws);
        }

        return $base->template;
    }

    public function lista(bool $header, bool $ws = false): array
    {
        $r_lista = parent::lista($header, $ws);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar datos',data:  $r_lista, header: $header,ws:$ws);
        }

        $registros = $this->maqueta_registros_lista(registros: $this->registros);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar registros',data:  $registros, header: $header,ws:$ws);
        }

        $this->registros = $registros;

        return $r_lista;
    }

    public function abono(bool $header, bool $ws = false): array|stdClass
    {
        $alta = $this->controlador_em_abono_anticipo->alta(header: false);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al generar template', data: $alta, header: $header, ws: $ws);
        }

        $this->controlador_em_abono_anticipo->asignar_propiedad(identificador: 'em_anticipo_id',
            propiedades: ["id_selected" => $this->registro_id, "disabled" => true,
                "filtro" => array('em_anticipo.id' => $this->registro_id)]);

        $this->inputs = $this->controlador_em_abono_anticipo->genera_inputs(
            keys_selects:  $this->controlador_em_abono_anticipo->keys_selects);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al generar inputs', data: $this->inputs);
            print_r($error);
            die('Error');
        }

        $abonos = (new em_abono_anticipo($this->link))->get_abonos_anticipo(em_anticipo_id: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener abonos',data:  $abonos,header: $header,ws:$ws);
        }

        foreach ($abonos->registros as $indice => $abono) {
            $abono = $this->data_abono_btn(abono: $abono);
            if (errores::$error) {
                return $this->retorno_error(mensaje: 'Error al asignar botones', data: $abono, header: $header, ws: $ws);
            }
            $abonos->registros[$indice] = $abono;
        }

        $this->abonos = $abonos;

        return $this->inputs;
    }

    public function abono_alta_bd(bool $header, bool $ws = false): array|stdClass
    {
        $this->link->beginTransaction();

        $siguiente_view = (new actions())->init_alta_bd();
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener siguiente view', data: $siguiente_view,
                header: $header, ws: $ws);
        }

        if (isset($_POST['btn_action_next'])) {
            unset($_POST['btn_action_next']);
        }

        $_POST['em_anticipo_id'] = $this->registro_id;

        $alta = (new em_abono_anticipo($this->link))->alta_registro(registro: $_POST);
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al dar de alta abono', data: $alta,
                header: $header, ws: $ws);
        }

        $this->link->commit();

        if ($header) {
            $this->retorno_base(registro_id:$this->registro_id, result: $alta,
                siguiente_view: "abono", ws:  $ws, params: ['em_anticipo_id'=>$this->registro_id]);
        }
        if ($ws) {
            header('Content-Type: application/json');
            echo json_encode($alta, JSON_THROW_ON_ERROR);
            exit;
        }
        $alta->siguiente_view = "abono";

        return $alta;
    }

    public function abono_modifica(bool $header, bool $ws = false): array|stdClass
    {
        $this->controlador_em_abono_anticipo->registro_id = $this->em_abono_anticipo_id;

        $modifica = $this->controlador_em_abono_anticipo->modifica(header: false);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar template',data:  $modifica, header: $header,ws:$ws);
        }

        $this->inputs = $this->controlador_em_abono_anticipo->genera_inputs(
            keys_selects:  $this->controlador_em_abono_anticipo->keys_selects);
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al generar inputs',data:  $this->inputs);
            print_r($error);
            die('Error');
        }

        return $this->inputs;
    }

    public function abono_modifica_bd(bool $header, bool $ws = false): array|stdClass
    {
        $this->link->beginTransaction();

        $siguiente_view = (new actions())->init_alta_bd();
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener siguiente view', data: $siguiente_view,
                header: $header, ws: $ws);
        }

        if (isset($_POST['btn_action_next'])) {
            unset($_POST['btn_action_next']);
        }

        $registros = $_POST;

        $r_modifica = (new em_abono_anticipo($this->link))->modifica_bd(registro: $registros,
            id: $this->em_abono_anticipo_id);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al modificar abono', data: $r_modifica, header: $header, ws: $ws);
        }

        $this->link->commit();

        if ($header) {
            $this->retorno_base(registro_id:$this->registro_id, result: $r_modifica,
                siguiente_view: "abono", ws:  $ws, params: ['em_anticipo_id'=>$this->em_anticipo_id]);
        }
        if ($ws) {
            header('Content-Type: application/json');
            echo json_encode($r_modifica, JSON_THROW_ON_ERROR);
            exit;
        }
        $r_modifica->siguiente_view = "abono";

        return $r_modifica;
    }

    public function abono_elimina_bd(bool $header, bool $ws = false): array|stdClass
    {
        $this->link->beginTransaction();

        $siguiente_view = (new actions())->init_alta_bd();
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener siguiente view', data: $siguiente_view,
                header: $header, ws: $ws);
        }

        if (isset($_POST['btn_action_next'])) {
            unset($_POST['btn_action_next']);
        }

        $r_elimina = (new em_abono_anticipo($this->link))->elimina_bd(id: $this->em_abono_anticipo_id);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al eliminar otro pago', data: $r_elimina, header: $header,
                ws: $ws);
        }

        $this->link->commit();

        if ($header) {
            $this->retorno_base(registro_id:$this->registro_id, result: $r_elimina,
                siguiente_view: "abono", ws:  $ws, params: ['em_anticipo_id'=>$this->em_anticipo_id]);
        }
        if ($ws) {
            header('Content-Type: application/json');
            echo json_encode($r_elimina, JSON_THROW_ON_ERROR);
            exit;
        }
        $r_elimina->siguiente_view = "abono";

        return $r_elimina;
    }

    private function data_abono_btn(array $abono): array
    {
        $params['em_abono_anticipo_id'] = $abono['em_abono_anticipo_id'];
        $params['em_anticipo_id'] = $abono['em_anticipo_id'];

        $btn_elimina = $this->html_base->button_href(accion: 'abono_elimina_bd', etiqueta: 'Elimina',
            registro_id: $this->registro_id, seccion: 'em_anticipo', style: 'danger',params: $params);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al generar btn', data: $btn_elimina);
        }
        $abono['link_elimina'] = $btn_elimina;

        $btn_modifica = $this->html_base->button_href(accion: 'abono_modifica', etiqueta: 'Modifica',
            registro_id: $this->registro_id, seccion: 'em_anticipo', style: 'warning',params: $params);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al generar btn', data: $btn_modifica);
        }
        $abono['link_modifica'] = $btn_modifica;

        return $abono;
    }
}
