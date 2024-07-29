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
use gamboamartin\comercial\models\com_cliente_documento;
use gamboamartin\comercial\models\com_conf_tipo_doc_cliente;
use gamboamartin\comercial\models\com_contacto;
use gamboamartin\compresor\compresor;
use gamboamartin\empleado\models\em_empleado_documento;
use gamboamartin\errores\errores;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use html\com_cliente_documento_html;
use html\com_conf_tipo_doc_cliente_html;
use html\com_contacto_html;
use html\em_empleado_documento_html;
use PDO;
use stdClass;

class controlador_em_empleado_documento extends _ctl_base
{

    public string $ruta_doc = '';
    public bool $es_imagen = false;
    public bool $es_pdf = false;

    public function __construct(PDO      $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass())
    {
        $modelo = new em_empleado_documento(link: $link);
        $html = new em_empleado_documento_html(html: $html);
        $obj_link = new links_menu(link: $link, registro_id: $this->registro_id);

        $datatables = $this->init_datatable();
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al inicializar datatable', data: $datatables);
            print_r($error);
            die('Error');
        }

        parent::__construct(html: $html, link: $link, modelo: $modelo, obj_link: $obj_link, datatables: $datatables,
            paths_conf: $paths_conf);

        $init_controladores = $this->init_controladores(paths_conf: $paths_conf);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al inicializar controladores', data: $init_controladores);
            print_r($error);
            die('Error');
        }

        $configuraciones = $this->init_configuraciones();
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al inicializar configuraciones', data: $configuraciones);
            print_r($error);
            die('Error');
        }

    }

    public function alta(bool $header, bool $ws = false): array|string
    {
        $r_alta = $this->init_alta();
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al inicializar alta', data: $r_alta, header: $header, ws: $ws);
        }

        $inputs = $this->data_form();
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener inputs', data: $inputs, header: $header, ws: $ws);
        }

        return $r_alta;
    }

    protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->inputs = array('codigo', 'nombre');
        $keys->telefonos = array();
        $keys->emails = array();
        $keys->selects = array();

        $init_data = array();
        $init_data['doc_documento'] = "gamboamartin\\documento";
        $init_data['em_empleado'] = "gamboamartin\\empleado";
        $campos_view = $this->campos_view_base(init_data: $init_data, keys: $keys);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar campo view', data: $campos_view);
        }

        return $campos_view;
    }

    private function data_form(): array|stdClass
    {
        $keys_selects = $this->init_selects_inputs();
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar selects', data: $keys_selects);
        }

        $inputs = $this->inputs(keys_selects: $keys_selects);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener inputs', data: $inputs);
        }

        return $inputs;
    }

    private function init_configuraciones(): controler
    {
        $this->titulo_lista = 'Registro Documentos Empleados';

        return $this;
    }

    private function init_controladores(stdClass $paths_conf): controler
    {
        return $this;
    }

    private function init_selects(array $keys_selects, string $key, string $label, int|null $id_selected = -1, int $cols = 6,
                                  bool  $con_registros = true, array $filtro = array(), array $columns_ds = array()): array
    {
        $keys_selects = $this->key_select(cols: $cols, con_registros: $con_registros, filtro: $filtro, key: $key,
            keys_selects: $keys_selects, id_selected: $id_selected, label: $label, columns_ds: $columns_ds);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        return $keys_selects;
    }

    public function init_selects_inputs(): array
    {

        $keys_selects = $this->init_selects(keys_selects: array(), key: "doc_documento_id", label: "Documento",
            cols: 12, columns_ds: array('doc_documento_descripcion'));
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al integrar selector', data: $keys_selects);
        }

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "em_empleado_id", label: "Empleado",
            cols: 12, columns_ds: array('em_empleado_nombre', 'em_empleado_ap', 'em_empleado_am'));
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al integrar selector', data: $keys_selects);
        }

        return $keys_selects;
    }

    public function init_datatable(): stdClass
    {
        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['com_cliente_documento_id']['titulo'] = 'Id';
        $datatables->columns['em_empleado_nombre']['titulo'] = 'Empleado';
        $datatables->columns['em_empleado_nombre']['campos'] = array('em_empleado_nombre', 'em_empleado_ap', 'em_empleado_am');
        $datatables->columns['doc_documento_descripcion']['titulo'] = 'Documento';

        $datatables->filtro = array();
        $datatables->filtro[] = 'com_cliente_documento.id';
        $datatables->filtro[] = 'doc_documento.descripcion';
        $datatables->filtro[] = 'em_empleado.descripcion';
        $datatables->filtro[] = 'em_empleado.ap';
        $datatables->filtro[] = 'em_empleado.am';

        return $datatables;
    }

    protected function key_selects_txt(array $keys_selects): array
    {
        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 4, key: 'codigo',
            keys_selects: $keys_selects, place_holder: 'Cod');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

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

        $keys_selects['doc_documento_id']->id_selected = $this->registro['doc_documento_id'];
        $keys_selects['em_empleado_id']->id_selected = $this->registro['em_empleado_id'];

        $base = $this->base_upd(keys_selects: $keys_selects, params: array(), params_ajustados: array());
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar base', data: $base, header: $header, ws: $ws);
        }

        return $r_modifica;
    }

    private function name_doc(stdClass $registro): string
    {
        $name = $registro->com_cliente_documento_id . "." . $registro->com_cliente_razon_social;
        $name .= "." . $registro->doc_tipo_documento_codigo;
        return $name;
    }

    private function name_file(stdClass $registro): array|string
    {
        $name = $this->name_doc(registro: $registro);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener name', data: $name);
        }
        $name .= "." . $registro->doc_extension_descripcion;
        return $name;
    }

    public function descarga(bool $header, bool $ws = false): array|string
    {
        $registro = $this->modelo->registro(registro_id: $this->registro_id, retorno_obj: true);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener documento', data: $registro, header: $header,
                ws: $ws);
        }
        $ruta_doc = $this->path_base . "$registro->doc_documento_ruta_relativa";

        $content = file_get_contents($ruta_doc);

        $name_file = $this->name_file(registro: $registro);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener name_file', data: $name_file, header: $header,
                ws: $ws);
        }

        if ($header) {
            if (ob_get_level() > 0) {
                ob_end_clean();
            }
            // Define headers
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=$name_file");
            header("Content-Type: application/$registro->doc_extension_descripcion");
            header("Content-Transfer-Encoding: binary");

            // Read the file
            readfile($ruta_doc);
            exit;
        }
        return $content;

    }


    public function vista_previa(bool $header, bool $ws = false): array|string|stdClass
    {
        $registro = $this->modelo->registro(registro_id: $this->registro_id, retorno_obj: true);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener documento', data: $registro, header: $header,
                ws: $ws);
        }

        $ruta_doc = $this->url_base . "$registro->doc_documento_ruta_relativa";

        $this->ruta_doc = $ruta_doc;
        if ($registro->doc_extension_es_imagen === 'activo') {
            $this->es_imagen = true;
        }
        if ($registro->doc_extension_descripcion === 'pdf') {
            $this->es_pdf = true;
        }

        return $registro;
    }

    public function descarga_zip(bool $header, bool $ws = false): array|string
    {
        $registro = $this->modelo->registro(registro_id: $this->registro_id, retorno_obj: true);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener documento', data: $registro, header: $header,
                ws: $ws);
        }
        $ruta_doc = $this->path_base . "$registro->doc_documento_ruta_relativa";


        $name = $this->name_doc(registro: $registro);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener name', data: $name, header: $header,
                ws: $ws);
        }
        $name_zip = $name . '.zip';

        $name_file = $this->name_file(registro: $registro);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener name_file', data: $name_file, header: $header,
                ws: $ws);
        }

        $archivos[$ruta_doc] = $name_file;
        $comprime = compresor::descarga_zip_multiple(archivos: $archivos, name_zip: $name_zip);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al comprimir file', data: $comprime, header: $header,
                ws: $ws);
        }

        return $comprime;
    }

}
