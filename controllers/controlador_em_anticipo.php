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
use gamboamartin\system\_ctl_base;
use gamboamartin\system\actions;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use html\em_anticipo_html;
use PDO;
use stdClass;

class controlador_em_anticipo extends _ctl_base {

    public array $keys_selects = array();
    public string $link_em_abono_anticipo_alta_bd = '';
    public string $link_em_abono_anticipo_modifica_bd = '';
    public string $link_em_anticipo_reporte_cliente = '';
    public string $link_em_anticipo_reporte_empresa = '';

    public int $em_anticipo_id = -1;
    public int $em_abono_anticipo_id = -1;
    public int $com_sucursal_id = -1;
    public int $org_sucursal_id = -1;

    public controlador_em_abono_anticipo $controlador_em_abono_anticipo;
    public stdClass $abonos;

    public function __construct(PDO $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass()){
        $modelo = new em_anticipo(link: $link);
        $html_ = new em_anticipo_html(html: $html);
        $obj_link = new links_menu(link: $link, registro_id: $this->registro_id);
        parent::__construct(html:$html_, link: $link,modelo:  $modelo, obj_link: $obj_link, paths_conf: $paths_conf);

        $this->titulo_lista = 'Anticipo';

        $this->controlador_em_abono_anticipo= new controlador_em_abono_anticipo(link: $this->link, paths_conf: $paths_conf);
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

        $link_em_abono_anticipo_alta_bd= $obj_link->link_con_id(accion: 'abono_alta_bd', link: $this->link,
            registro_id: $this->registro_id, seccion: $this->seccion);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al generar link', data: $link_em_abono_anticipo_alta_bd);
            print_r($error);
            die('Error');
        }
        $this->link_em_abono_anticipo_alta_bd = $link_em_abono_anticipo_alta_bd;

        $link_em_abono_anticipo_modifica_bd = $obj_link->link_con_id(accion: 'abono_modifica_bd', link: $this->link,
            registro_id: $this->registro_id, seccion: $this->seccion);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al generar link', data: $link_em_abono_anticipo_modifica_bd);
            print_r($error);
            die('Error');
        }
        $this->link_em_abono_anticipo_modifica_bd = $link_em_abono_anticipo_modifica_bd;

        $link_em_anticipo_reporte_cliente = $obj_link->link_con_id(accion: 'reporte_cliente', link: $link, registro_id: $this->registro_id,
            seccion: $this->seccion);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al generar link', data: $link_em_anticipo_reporte_cliente);
            print_r($error);
            die('Error');
        }
        $this->link_em_anticipo_reporte_cliente = $link_em_anticipo_reporte_cliente;

        $link_em_anticipo_reporte_empresa = $obj_link->link_con_id(accion: 'reporte_empresa', link: $link, registro_id: $this->registro_id,
            seccion: $this->seccion);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al generar link', data: $link_em_anticipo_reporte_empresa);
            print_r($error);
            die('Error');
        }
        $this->link_em_anticipo_reporte_empresa = $link_em_anticipo_reporte_empresa;

        if (isset($_GET['em_anticipo_id'])){
            $this->em_anticipo_id = $_GET['em_anticipo_id'];
        }

        if (isset($_GET['em_abono_anticipo_id'])){
            $this->em_abono_anticipo_id = $_GET['em_abono_anticipo_id'];
        }

        if (isset($_GET['com_sucursal_id'])){
            $this->com_sucursal_id = $_GET['com_sucursal_id'];
        }

        if (isset($_GET['org_sucursal_id'])){
            $this->org_sucursal_id = $_GET['org_sucursal_id'];
        }

        $this->lista_get_data = true;
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
        $r_alta = $this->init_alta();
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al inicializar alta', data: $r_alta, header: $header, ws: $ws);
        }

        $keys_selects = $this->init_selects_inputs();
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al inicializar selects', data: $keys_selects, header: $header,
                ws: $ws);
        }

        $this->row_upd->fecha_prestacion = date('Y-m-d');
        $this->row_upd->fecha_inicio_descuento = date('Y-m-d');
        $this->row_upd->monto = 0;
        $this->row_upd->n_pagos = 1;

        $inputs = $this->inputs(keys_selects: $keys_selects);
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs', data: $inputs, header: $header, ws: $ws);
        }

        return $r_alta;
    }

    protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->inputs = array('codigo','descripcion','monto', 'n_pagos');
        $keys->fechas = array('fecha_prestacion', 'fecha_inicio_descuento');
        $keys->selects = array();

        $init_data = array();
        $init_data['em_tipo_anticipo'] = "gamboamartin\\empleado";
        $init_data['em_empleado'] = "gamboamartin\\empleado";
        $init_data['em_tipo_descuento'] = "gamboamartin\\empleado";

        $campos_view = $this->campos_view_base(init_data: $init_data, keys: $keys);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar campo view', data: $campos_view);
        }

        return $campos_view;
    }

    /**
     * Integra los selects
     * @param array $keys_selects Key de selcta integrar
     * @param string $key key a validar
     * @param string $label Etiqueta a mostrar
     * @param int $id_selected  selected
     * @param int $cols cols css
     * @param bool $con_registros Intrega valores
     * @param array $filtro Filtro de datos
     * @return array
     */
    private function init_selects(array $keys_selects, string $key, string $label, int $id_selected = -1, int $cols = 6,
                                  bool  $con_registros = true, array $filtro = array()): array
    {
        $keys_selects = $this->key_select(cols: $cols, con_registros: $con_registros, filtro: $filtro, key: $key,
            keys_selects: $keys_selects, id_selected: $id_selected, label: $label);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        return $keys_selects;
    }

    public function init_selects_inputs(): array
    {
        $keys_selects = $this->init_selects(keys_selects: array(), key: "em_tipo_anticipo_id", label: "Tipo Anticipo");
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "em_empleado_id", label: "Empleado",
            cols: 12);
        return $this->init_selects(keys_selects: $keys_selects, key: "em_tipo_descuento_id", label: "Tipo Descuento");
    }

    protected function key_selects_txt(array $keys_selects): array
    {
        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 12, key: 'descripcion',
            keys_selects: $keys_selects, place_holder: 'Descripción');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'monto',
            keys_selects: $keys_selects, place_holder: 'Monto');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'n_pagos',
            keys_selects: $keys_selects, place_holder: 'Número Pagos');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'fecha_prestacion',
            keys_selects: $keys_selects, place_holder: 'Fecha Prestación');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'fecha_inicio_descuento',
            keys_selects: $keys_selects, place_holder: 'Fecha Inicio Descuento');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        return $keys_selects;
    }

    private function base(): array|stdClass
    {
        $r_modifica =  parent::modifica(header: false);
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

        $link = $this->obj_link->link_con_id(accion: $accion, link: $this->link,registro_id:  $row->em_anticipo_id,
            seccion:  $this->tabla);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al genera link',data:  $link);
        }

        $row->$propiedad = $link;
        $row->$estilo = 'info';

        return $row;
    }

    public function lee_archivo(){

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

    public function modifica(bool $header, bool $ws = false): array|stdClass
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

    public function get_anticipos(bool $header, bool $ws = true): array|stdClass
    {
        $keys['em_empleado'] = array('id', 'descripcion', 'codigo', 'codigo_bis');

        $salida = $this->get_out(header: $header, keys: $keys, ws: $ws);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al generar salida', data: $salida, header: $header, ws: $ws);
        }

        return $salida;
    }

    public function sube_archivo(bool $header, bool $ws = false){

        $this->lee_archivo();

        $r_alta =  parent::alta(header: false,ws:  false); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al generar template',data:  $r_alta);
        }

        return $r_alta;
    }

    public function reporte_cliente(bool $header, bool $ws = false){

        $this->asignar_propiedad(identificador:'com_sucursal_id', propiedades: ["label" => "Sucursal", "cols" => 12]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador:'fecha_inicio', propiedades: ["place_holder" => "Fecha Inicio"]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador:'fecha_final', propiedades: ["place_holder" => "Fecha Final"]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        /**
         * $this->row_upd->fecha_inicio = date('Y-m-d');
         * $this->row_upd->fecha_final = date('Y-m-d');
         */

        $r_alta =  parent::alta(header: false);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar template',data:  $r_alta, header: $header,ws:$ws);
        }

        $inputs = $this->genera_inputs(keys_selects: $this->keys_selects);
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al generar inputs',data:  $inputs);
            print_r($error);
            die('Error');
        }

        return $this->inputs;
    }

    public function reporte_empresa(bool $header, bool $ws = false){

        $this->asignar_propiedad(identificador:'org_sucursal_id', propiedades: ["label" => "Sucursal", "cols" => 12]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador:'fecha_inicio', propiedades: ["place_holder" => "Fecha Inicio"]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador:'fecha_final', propiedades: ["place_holder" => "Fecha Final",
            date(format:'Y-m-d')]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        /**
         * $this->row_upd->fecha_inicio = date('Y-m-d');
         * $this->row_upd->fecha_final = date('Y-m-d');
         */

        $r_alta =  parent::alta(header: false);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar template',data:  $r_alta, header: $header,ws:$ws);
        }

        $inputs = $this->genera_inputs(keys_selects: $this->keys_selects);
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al generar inputs',data:  $inputs);
            print_r($error);
            die('Error');
        }

        return $this->inputs;
    }
}
