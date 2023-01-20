<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\empleado\controllers;

use base\controller\controler;
use gamboamartin\empleado\models\em_abono_anticipo;
use gamboamartin\errores\errores;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use html\em_abono_anticipo_html;
use PDO;
use stdClass;

class controlador_em_abono_anticipo extends _ctl_base {

    public array $keys_selects = array();

    public function __construct(PDO      $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass())
    {
        $modelo = new em_abono_anticipo(link: $link);
        $html_ = new em_abono_anticipo_html(html: $html);
        $obj_link = new links_menu(link: $link, registro_id: $this->registro_id);

        $datatables = $this->init_datatable();
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al inicializar datatable', data: $datatables);
            print_r($error);
            die('Error');
        }

        parent::__construct(html: $html_, link: $link, modelo: $modelo, obj_link: $obj_link, datatables: $datatables,
            paths_conf: $paths_conf);

        $configuraciones = $this->init_configuraciones();
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al inicializar configuraciones', data: $configuraciones);
            print_r($error);
            die('Error');
        }

        $this->lista_get_data = true;
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
        $keys->inputs = array('codigo', 'descripcion', 'monto', 'anticipo', 'saldo', 'num_pago', 'n_pagos');
        $keys->fechas = array('fecha');

        $keys->selects = array();

        $init_data = array();
        $init_data['em_empleado'] = "gamboamartin\\empleado";
        $init_data['em_tipo_abono_anticipo'] = "gamboamartin\\empleado";
        $init_data['em_anticipo'] = "gamboamartin\\empleado";
        $init_data['cat_sat_forma_pago'] = "gamboamartin\\cat_sat";

        $campos_view = $this->campos_view_base(init_data: $init_data, keys: $keys);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar campo view', data: $campos_view);
        }

        return $campos_view;
    }

    private function init_configuraciones(): controler
    {
        $this->seccion_titulo = 'Abonos';
        $this->titulo_lista = 'Registro de Abonos';

        return $this;
    }

    private function init_datatable(): stdClass
    {
        $columns["em_abono_anticipo_id"]["titulo"] = "Id";
        $columns["em_empleado_nombre"]["titulo"] = "Empleado";
        $columns["em_empleado_nombre"]["campos"] = array("em_empleado_ap","em_empleado_am");
        $columns["em_tipo_abono_anticipo_descripcion"]["titulo"] = "Tipo Abono";
        $columns["em_anticipo_codigo"]["titulo"] = "Anticipo";
        $columns["em_abono_anticipo_monto"]["titulo"] = "Monto";
        $columns["em_abono_anticipo_fecha"]["titulo"] = "Fecha";

        $filtro = array("em_abono_anticipo.id", "em_empleado.codigo", "em_tipo_abono_anticipo.descripcion",
            "em_anticipo.codigo","cat_sat_forma_pago.descripcion");

        $datatables = new stdClass();
        $datatables->columns = $columns;
        $datatables->filtro = $filtro;

        return $datatables;
    }

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
        $keys_selects = $this->init_selects(keys_selects: array(), key: "em_empleado_id", label: "Empleado", cols: 12);
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "em_tipo_abono_anticipo_id",
            label: "Tipo de Abono");
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "em_anticipo_id", label: "Anticipo",
            cols: 12, con_registros: false);
        return $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_forma_pago_id", label: "Forma Pago");
    }

    protected function key_selects_txt(array $keys_selects): array
    {
        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 4, key: 'codigo',
            keys_selects: $keys_selects, place_holder: 'Código');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 12, key: 'descripcion',
            keys_selects: $keys_selects, place_holder: 'Descripción');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'monto',
            keys_selects: $keys_selects, place_holder: 'Abono');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'fecha',
            keys_selects: $keys_selects, place_holder: 'Fecha');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'anticipo',
            keys_selects: $keys_selects, place_holder: 'Monto Anticipo');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'saldo',
            keys_selects: $keys_selects, place_holder: 'Saldo');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'num_pago',
            keys_selects: $keys_selects, place_holder: 'Nº Pago Actual');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'n_pagos',
            keys_selects: $keys_selects, place_holder: 'Num Pagos');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects['n_pagos']->disabled = true;
        $keys_selects['num_pago']->disabled = true;
        $keys_selects['anticipo']->disabled = true;
        $keys_selects['saldo']->disabled = true;

        return $keys_selects;
    }

    public function modifica(bool $header, bool $ws = false): array|stdClass
    {
        $r_modifica = $this->init_modifica();
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al generar salida de template', data: $r_modifica, header: $header, ws: $ws);
        }

        $keys_selects = $this->init_selects_inputs();
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al inicializar selects', data: $keys_selects, header: $header,
                ws: $ws);
        }

        $abono = (new em_abono_anticipo($this->link))->get_abono_anticipo(em_abono_anticipo_id: $this->registro_id);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener abono', data: $abono, header: $header, ws: $ws);
        }

        $keys_selects['em_empleado_id']->id_selected = $abono['em_empleado_id'];
        $keys_selects['em_empleado_id']->disabled = true;
        $keys_selects['em_tipo_abono_anticipo_id']->id_selected = $this->registro['em_tipo_abono_anticipo_id'];
        $keys_selects['em_anticipo_id']->id_selected = $this->registro['em_anticipo_id'];
        $keys_selects['em_anticipo_id']->con_registros = true;
        $keys_selects['em_anticipo_id']->filtro = array('em_empleado.id' => $abono['em_empleado_id']);
        $keys_selects['em_anticipo_id']->disabled = true;
        $keys_selects['cat_sat_forma_pago_id']->id_selected = $this->registro['cat_sat_forma_pago_id'];
        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'monto',
            keys_selects: $keys_selects, place_holder: 'Abono');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects['monto']->disabled = true;
        $this->row_upd->anticipo = $abono['em_anticipo_monto'];
        $this->row_upd->n_pagos = $abono['em_anticipo_n_pagos'];


        $base = $this->base_upd(keys_selects: $keys_selects, params: array(), params_ajustados: array());
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar base', data: $base, header: $header, ws: $ws);
        }


        return $r_modifica;
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

}
