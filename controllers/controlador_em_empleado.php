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
use gamboamartin\comercial\controllers\controlador_com_sucursal;
use gamboamartin\direccion_postal\models\dp_calle_pertenece;
use gamboamartin\direccion_postal\models\dp_municipio;
use gamboamartin\documento\models\doc_documento;
use gamboamartin\empleado\models\_email;
use gamboamartin\empleado\models\em_abono_anticipo;
use gamboamartin\empleado\models\em_anticipo;
use gamboamartin\empleado\models\em_conf_tipo_doc_empleado;
use gamboamartin\empleado\models\em_empleado_documento;
use gamboamartin\errores\errores;
use gamboamartin\notificaciones\models\not_mensaje;
use gamboamartin\plugins\exportador;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\actions;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;

use html\doc_tipo_documento_html;
use html\em_empleado_html;
use gamboamartin\empleado\models\em_empleado;
use PDO;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use stdClass;
use Throwable;

class controlador_em_empleado extends _ctl_base {

    public controlador_em_cuenta_bancaria $controlador_em_cuenta_bancaria;

    public controlador_em_anticipo $controlador_em_anticipo;
    public controlador_em_abono_anticipo $controlador_em_abono_anticipo;
    public string $link_em_cuenta_bancaria_alta_bd = '';
    public string $link_em_anticipo_alta_bd = '';
    public string $link_em_abono_anticipo_alta_bd = '';
    public string $link_em_empleado_sube_archivo = '';
    public string $link_em_empleado_reportes = '';
    public string $link_em_empleado_reporte_remunerado = '';
    public string $link_em_empleado_exportar = '';

    public string $link_em_empleado_documento_alta_bd = '';

    public string $link_envia_documentos = '';

    public function __construct(PDO      $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass())
    {
        $modelo = new em_empleado(link: $link);
        $html_ = new em_empleado_html(html: $html);
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

        $init_controladores = $this->init_controladores(paths_conf: $paths_conf);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al inicializar controladores', data: $init_controladores);
            print_r($error);
            die('Error');
        }

        $init_links = $this->init_links();
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al inicializar links', data: $init_links);
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

        $keys_selects = $this->init_selects_inputs();
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al inicializar selects', data: $keys_selects, header: $header,
                ws: $ws);
        }

        $this->row_upd->fecha_inicio_rel_laboral = date('Y-m-d');
        $this->row_upd->salario_diario = 0;
        $this->row_upd->salario_diario_integrado = 0;

        $em_empleado_rfc = (new em_empleado_html(html: $this->html_base))->input_rfc(cols: 6, row_upd: $this->row_upd,
            value_vacio: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar input', data: $em_empleado_rfc);
        }

        $em_empleado_curp = (new em_empleado_html(html: $this->html_base))->input_curp(cols: 12, row_upd: $this->row_upd,
            value_vacio: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar input', data: $em_empleado_curp);
        }

        $em_empleado_nss = (new em_empleado_html(html: $this->html_base))->input_nss(cols: 6, row_upd: $this->row_upd,
            value_vacio: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar input', data: $em_empleado_nss);
        }

        $this->inputs->em_empleado_rfc = $em_empleado_rfc;
        $this->inputs->em_empleado_curp = $em_empleado_curp;
        $this->inputs->em_empleado_nss = $em_empleado_nss;

        $inputs = $this->inputs(keys_selects: $keys_selects);
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs', data: $inputs, header: $header, ws: $ws);
        }

        return $r_alta;
    }

    public function anticipo(bool $header = true, bool $ws = false, array $not_actions = array()): array|string
    {
        $seccion = "em_anticipo";

        $data_view = new stdClass();
        $data_view->names = array('Id', 'Tipo Anticipo', 'Monto', 'Fecha Prestación','Acciones');
        $data_view->keys_data = array($seccion . "_id", 'em_tipo_anticipo_descripcion', $seccion . '_monto',
            $seccion . '_fecha_prestacion');
        $data_view->key_actions = 'acciones';
        $data_view->namespace_model = 'gamboamartin\\empleado\\models';
        $data_view->name_model_children = $seccion;

        $contenido_table = $this->contenido_children(data_view: $data_view, next_accion: __FUNCTION__,
            not_actions: $not_actions);
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al obtener tbody', data: $contenido_table, header: $header, ws: $ws);
        }

        return $contenido_table;
    }

    protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->inputs = array('codigo', 'descripcion', 'nombre', 'ap', 'am',  'rfc', 'curp', 'nss', 'salario_diario',
            'salario_diario_integrado','com_sucursal','org_sucursal', 'salario_total', 'numero_exterior',
            'numero_interior', 'cp', 'colonia', 'calle', 'asunto', 'mensaje', 'receptor', 'cc', 'cco');
        $keys->telefonos = array('telefono');
        $keys->fechas = array('fecha_inicio_rel_laboral', 'fecha_inicio', 'fecha_final');
        $keys->emails = array('correo');
        $keys->selects = array();

        $init_data = array();
        $init_data['dp_pais'] = "gamboamartin\\direccion_postal";
        $init_data['dp_estado'] = "gamboamartin\\direccion_postal";
        $init_data['dp_municipio'] = "gamboamartin\\direccion_postal";
        $init_data['dp_cp'] = "gamboamartin\\direccion_postal";
        $init_data['dp_colonia_postal'] = "gamboamartin\\direccion_postal";
        $init_data['dp_calle_pertenece'] = "gamboamartin\\direccion_postal";
        $init_data['cat_sat_regimen_fiscal'] = "gamboamartin\\cat_sat";
        $init_data['cat_sat_tipo_regimen_nom'] = "gamboamartin\\cat_sat";
        $init_data['cat_sat_tipo_jornada_nom'] = "gamboamartin\\cat_sat";
        $init_data['org_puesto'] = "gamboamartin\\organigrama";
        $init_data['em_centro_costo'] = "gamboamartin\\empleado";
        $init_data['em_empleado'] = "gamboamartin\\empleado";
        $init_data['em_registro_patronal'] = "gamboamartin\\empleado";
        $init_data['com_sucursal'] = "gamboamartin\\comercial";


        $campos_view = $this->campos_view_base(init_data: $init_data, keys: $keys);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar campo view', data: $campos_view);
        }

        return $campos_view;
    }

    public function cuenta_bancaria(bool $header = true, bool $ws = false, array $not_actions = array()): array|string
    {
        $seccion = "em_cuenta_bancaria";

        $data_view = new stdClass();
        $data_view->names = array('Id', 'Banco Sucursal', 'Número  Cuenta', 'Acciones');
        $data_view->keys_data = array($seccion . "_id", 'bn_sucursal_descripcion', $seccion . '_num_cuenta');
        $data_view->key_actions = 'acciones';
        $data_view->namespace_model = 'gamboamartin\\empleado\\models';
        $data_view->name_model_children = $seccion;

        $contenido_table = $this->contenido_children(data_view: $data_view, next_accion: __FUNCTION__,
            not_actions: $not_actions);
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al obtener tbody', data: $contenido_table, header: $header, ws: $ws);
        }

        return $contenido_table;
    }

    public function exportar(bool $header, bool $ws = false): array|stdClass
    {
        $fecha_inicio = date('Y-m-d');
        $fecha_fin = date('Y-m-d');

        if (isset($_POST['fecha_inicio'])){
            $fecha_inicio = $_POST['fecha_inicio'];
        }

        if (isset($_POST['fecha_final'])){
            $fecha_fin = $_POST['fecha_final'];
        }

        $filtro_especial[0][$fecha_fin]['operador'] = '>=';
        $filtro_especial[0][$fecha_fin]['valor'] = 'em_empleado.fecha_inicio_rel_laboral';
        $filtro_especial[0][$fecha_fin]['comparacion'] = 'AND';
        $filtro_especial[0][$fecha_fin]['valor_es_campo'] = true;

        $filtro_especial[1][$fecha_inicio]['operador'] = '<=';
        $filtro_especial[1][$fecha_inicio]['valor'] = 'em_empleado.fecha_inicio_rel_laboral';
        $filtro_especial[1][$fecha_inicio]['comparacion'] = 'AND';
        $filtro_especial[1][$fecha_inicio]['valor_es_campo'] = true;

        $data = (new em_empleado($this->link))->filtro_and(filtro_especial: $filtro_especial);
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al obtener registros',data:  $data);
            print_r($error);
            die('Error');
        }

        $exportador = (new exportador());
        $registros_xls = array();

        foreach ($data->registros as $registro){

            $row = array();
            $row["empleado"] = $registro['em_empleado_nombre'];
            $row["empleado"] .= " ".$registro['em_empleado_ap'];
            $row["empleado"] .= " ".$registro['em_empleado_am'];
            $row["nss"] = $registro['em_empleado_nss'];
            $row["rfc"] = $registro['em_empleado_rfc'];
            $row["salario_diario"] = $registro['em_empleado_salario_diario'];
            $row["salario_diario_integrado"] = $registro['em_empleado_salario_diario_integrado'];
            $row["puesto"] = $registro['org_puesto_descripcion'];
            $row["departamento"] = $registro['org_departamento_descripcion'];
            $row["centro_costo"] = $registro['em_centro_costo_descripcion'];
            $registros_xls[] = $row;
        }

        $keys = array();

        foreach (array_keys($registros_xls[0]) as $key) {
            $keys[$key] = strtoupper(str_replace('_', ' ', $key));
        }

        $registros = array();

        foreach ($registros_xls as $row) {
            $registros[] = array_combine(preg_replace(array_map(function($s){return "/^$s$/";},
                array_keys($keys)),$keys, array_keys($row)), $row);
        }

        $resultado = $exportador->listado_base_xls(header: $header, name: $this->seccion, keys:  $keys,
            path_base: $this->path_base,registros:  $registros,totales:  array());
        if(errores::$error){
            $error =  $this->errores->error('Error al generar xls',$resultado);
            if(!$header){
                return $error;
            }
            print_r($error);
            die('Error');
        }

        $link = "./index.php?seccion=em_empleado&accion=lista&registro_id=".$this->registro_id;
        $link.="&session_id=$this->session_id";
        header('Location:' . $link);
        exit;
    }

    final public function documentos(bool $header, bool $ws = false): array|stdClass
    {
        $template = $this->modifica(header: false);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar base', data: $template, header: $header, ws: $ws);
        }

        $keys_selects = $this->init_selects_inputs();
        if (errores::$error) {return $this->errores->error(mensaje: 'Error al inicializar selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 4, key: 'nombre',
            keys_selects: $keys_selects, place_holder: 'Nombre');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects['nombre']->disabled = true;

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 4, key: 'ap',
            keys_selects: $keys_selects, place_holder: 'Apellido Paterno');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects['ap']->disabled = true;

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 4, key: 'am',
            keys_selects: $keys_selects, place_holder: 'Apellido Materno');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects['am']->disabled = true;

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'telefono',
            keys_selects: $keys_selects, place_holder: 'Teléfono');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects['telefono']->disabled = true;

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'correo',
            keys_selects: $keys_selects, place_holder: 'Correo');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects['correo']->disabled = true;

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'rfc',
            keys_selects: $keys_selects, place_holder: 'RFC');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects['rfc']->disabled = true;

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'nss',
            keys_selects: $keys_selects, place_holder: 'NSS');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects['nss']->disabled = true;

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'curp',
            keys_selects: $keys_selects, place_holder: 'CURP');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects['curp']->disabled = true;

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 12, key: 'receptor',
            keys_selects: $keys_selects, place_holder: 'Receptor');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 12, key: 'asunto',
            keys_selects: $keys_selects, place_holder: 'Asunto');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 12, key: 'mensaje',
            keys_selects: $keys_selects, place_holder: 'Mensaje');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 12, key: 'cc',
            keys_selects: $keys_selects, place_holder: 'CC',required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 12, key: 'cco',
            keys_selects: $keys_selects, place_holder: 'CCO',required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $this->row_upd->receptor = $this->row_upd->correo;
        $this->row_upd->asunto = "Envío de documentos";
        $this->row_upd->mensaje = "Se envían documentos";

        $base = $this->base_upd(keys_selects: $keys_selects, params: array(), params_ajustados: array());
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar base', data: $base, header: $header, ws: $ws);
        }

        return $template;
    }

    public function tipos_documentos(bool $header, bool $ws = false): array
    {
        $documentos = (new em_empleado($this->link))->integra_documentos(controler: $this);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar buttons', data: $documentos, header: $header, ws: $ws);
        }

        $salida['draw'] = count($documentos);
        $salida['recordsTotal'] = count($documentos);
        $salida['recordsFiltered'] = count($documentos);
        $salida['data'] = $documentos;

        header('Content-Type: application/json');
        echo json_encode($salida);
        exit;
    }

    final public function subir_documento(bool $header, bool $ws = false)
    {
        $em_empleado = (new em_empleado(link: $this->link))->registro(registro_id: $this->registro_id);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener empleado', data: $em_empleado,
                header: $header, ws: $ws);
        }

        $em_conf_tipo_doc_empleado = (new em_conf_tipo_doc_empleado(link: $this->link))->filtro_and(
            columnas: ['doc_tipo_documento_id'],
            filtro: array('em_empleado_id' => $em_empleado['em_empleado_id']));
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener em_conf_tipo_doc_empleado',
                data: $em_conf_tipo_doc_empleado, header: $header, ws: $ws);
        }

        $this->inputs = new stdClass();

        $filtro['em_empleado.id'] = $this->registro_id;
        $em_empleado_id = (new em_empleado_html(html: $this->html_base))->select_em_empleado_id(
            cols: 12, con_registros: true, id_selected: $this->registro_id, link: $this->link, filtro: $filtro);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al generar input', data: $em_empleado_id, header: $header, ws: $ws);
        }
        $this->inputs->em_empleado_id = $em_empleado_id;

        $doc_ids = array_map(function ($registro) {
            return $registro['doc_tipo_documento_id'];
        }, $em_conf_tipo_doc_empleado->registros);

        $doc_tipos_documentos = array();

        if (count($doc_ids) > 0) {
            $doc_tipos_documentos = (new em_empleado($this->link))->documentos_de_empleado(em_empleado_id: $this->registro_id,
                link: $this->link, todos: true, tipos_documentos: $doc_ids);
            if (errores::$error) {
                return $this->retorno_error(mensaje: 'Error al obtener tipos de documento', data: $doc_tipos_documentos,
                    header: $header, ws: $ws);
            }
        }

        $_doc_tipo_documento_id = -1;
        $filtro = array();
        if (isset($_GET['doc_tipo_documento_id'])) {
            $_doc_tipo_documento_id = $_GET['doc_tipo_documento_id'];
            $filtro['doc_tipo_documento.id'] = $_GET['doc_tipo_documento_id'];
        }

        $doc_tipo_documento_id = (new doc_tipo_documento_html(html: $this->html_base))->select_doc_tipo_documento_id(
            cols: 12, con_registros: true, id_selected: $_doc_tipo_documento_id, link: $this->link,
            filtro: $filtro, registros: $doc_tipos_documentos);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al generar input', data: $doc_tipo_documento_id, header: $header, ws: $ws);
        }
        $this->inputs->doc_tipo_documento_id = $doc_tipo_documento_id;

        $documento = $this->html->input_file(cols: 12, name: 'documento', row_upd: new stdClass(), value_vacio: false);
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs', data: $documento, header: $header, ws: $ws);
        }

        $this->inputs->documento = $documento;

        $link_alta_doc = $this->obj_link->link_alta_bd(link: $this->link, seccion: 'em_empleado_documento');
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al generar link', data: $link_alta_doc, header: $header, ws: $ws);
        }

        $this->link_em_empleado_documento_alta_bd = $link_alta_doc;

        $btn_action_next = $this->html->hidden('btn_action_next', value: 'documentos');
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al generar btn_action_next', data: $btn_action_next, header: $header, ws: $ws);
        }

        $id_retorno = $this->html->hidden('id_retorno', value: $this->registro_id);
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al generar btn_action_next', data: $btn_action_next, header: $header, ws: $ws);
        }

        $seccion_retorno = $this->html->hidden('seccion_retorno', value: $this->seccion);
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al generar btn_action_next', data: $btn_action_next, header: $header, ws: $ws);
        }

        $this->inputs->btn_action_next = $btn_action_next;
        $this->inputs->id_retorno = $id_retorno;
        $this->inputs->seccion_retorno = $seccion_retorno;
    }

    public function get_empleado(bool $header, bool $ws = true): array|stdClass
    {
        $keys['em_empleado'] = array('id', 'descripcion', 'codigo', 'nss', 'rfc', 'curp');

        $salida = $this->get_out(header: $header, keys: $keys, ws: $ws);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al generar salida', data: $salida, header: $header, ws: $ws);
        }

        return $salida;
    }

    private function init_configuraciones(): controler
    {
        $this->seccion_titulo = 'Empleados';
        $this->titulo_lista = 'Registro de Empleados';

        $this->lista_get_data = true;

        return $this;
    }

    private function init_controladores(stdClass $paths_conf): controler
    {
        $this->controlador_em_cuenta_bancaria = new controlador_em_cuenta_bancaria(link: $this->link,
            paths_conf: $paths_conf);
        $this->controlador_em_anticipo = new controlador_em_anticipo(link: $this->link,
            paths_conf: $paths_conf);


        return $this;
    }

    protected function init_datatable(): stdClass
    {
        $columns["em_empleado_id"]["titulo"] = "Id";
        $columns["em_empleado_nombre"]["titulo"] = "Nombre";
        $columns["em_empleado_nombre"]["campos"] = array("em_empleado_ap","em_empleado_am");
        $columns["em_empleado_rfc"]["titulo"] = "Rfc";
        $columns["em_empleado_nss"]["titulo"] = "NSS";
        $columns["org_puesto_descripcion"]["titulo"] = "Puesto";
        $columns["em_empleado_n_cuentas_bancarias"]["titulo"] = "Cuentas Bancarias";

        $filtro = array("em_empleado.id","em_empleado.nombre","em_empleado.ap","em_empleado.am","em_empleado.rfc",
            "em_empleado_nombre_completo","em_empleado_nombre_completo_inv", "em_empleado.nss","org_puesto.descripcion");

        $datatables = new stdClass();
        $datatables->columns = $columns;
        $datatables->filtro = $filtro;

        return $datatables;
    }

    private function init_links(): array|string
    {
        $this->link_em_cuenta_bancaria_alta_bd = $this->obj_link->link_alta_bd(link: $this->link,
            seccion: 'em_cuenta_bancaria');
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al obtener link',
                data: $this->link_em_cuenta_bancaria_alta_bd);
            print_r($error);
            exit;
        }

        $this->link_em_anticipo_alta_bd = $this->obj_link->link_alta_bd(link: $this->link,
            seccion: 'em_anticipo');
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al obtener link',
                data: $this->link_em_anticipo_alta_bd);
            print_r($error);
            exit;
        }

        $this->link_em_empleado_sube_archivo = $this->obj_link->link_con_id(accion: "sube_archivo",link: $this->link,
            registro_id: $this->registro_id,seccion: "em_empleado");
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al obtener link',
                data: $this->link_em_empleado_sube_archivo);
            print_r($error);
            exit;
        }

        $this->link_em_empleado_reportes = $this->obj_link->link_con_id(accion: "reportes",link: $this->link,
            registro_id: $this->registro_id,seccion: "em_empleado");
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al obtener link',
                data: $this->link_em_empleado_reportes);
            print_r($error);
            exit;
        }

        $this->link_em_empleado_reporte_remunerado = $this->obj_link->link_con_id(accion: "reporte_remunerado",link: $this->link,
            registro_id: $this->registro_id,seccion: "em_empleado");
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al obtener link',
                data: $this->link_em_empleado_reporte_remunerado);
            print_r($error);
            exit;
        }

        $this->link_em_empleado_exportar = $this->obj_link->link_con_id(accion: "exportar",link: $this->link,
            registro_id: $this->registro_id,seccion: "em_empleado");
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al obtener link',
                data: $this->link_em_empleado_exportar);
            print_r($error);
            exit;
        }

        $this->link_envia_documentos = $this->obj_link->link_con_id(accion: "envia_documentos",link: $this->link,
            registro_id: $this->registro_id,seccion: "em_empleado");
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al obtener link',
                data: $this->link_envia_documentos);
            print_r($error);
            exit;
        }

        return $this->link_em_empleado_exportar;
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
        $keys_selects = $this->init_selects(keys_selects: array(), key: "dp_pais_id", label: "País");
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_estado_id", label: "Estado",
            con_registros: false);
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_municipio_id", label: "Municipio",
            con_registros: false);
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_cp_id", label: "CP",
            con_registros: false);
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_colonia_postal_id", label: "Colonia",
            con_registros: false);
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "dp_calle_pertenece_id", label: "Calle",
            con_registros: false);
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_regimen_fiscal_id",
            label: "Régimen Fiscal");
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_tipo_regimen_nom_id",
            label: "Tipo de Régimen Nom");
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "cat_sat_tipo_jornada_nom_id",
            label: "Tipo de Jornada Nom");
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "org_puesto_id", label: "Puesto");

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "em_centro_costo_id",
            label: "Centro Costo", cols: 12);
        return $this->init_selects(keys_selects: $keys_selects, key: "em_registro_patronal_id", label: "Registro Patronal");
    }

    protected function inputs_children(stdClass $registro): array|stdClass
    {
        if ($this->accion === "cuenta_bancaria"){
            $r_template = $this->controlador_em_cuenta_bancaria->alta(header: false);
            if (errores::$error) {
                return $this->errores->error(mensaje: 'Error al obtener template', data: $r_template);
            }

            $keys_selects = $this->controlador_em_cuenta_bancaria->init_selects_inputs();
            if (errores::$error) {
                return $this->errores->error(mensaje: 'Error al inicializar selects', data: $keys_selects);
            }

            $keys_selects['em_empleado_id']->id_selected = $this->registro_id;
            $keys_selects['em_empleado_id']->filtro = array("em_empleado.id" => $this->registro_id);
            $keys_selects['em_empleado_id']->disabled = true;

            $keys_selects = $this->controlador_em_cuenta_bancaria->key_selects_txt($keys_selects);
            if (errores::$error) {
                return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
            }

            $keys_selects['clabe']->cols = 12;
            $keys_selects['descripcion']->cols = 12;

            $inputs = $this->controlador_em_cuenta_bancaria->inputs(keys_selects: $keys_selects);
            if (errores::$error) {
                return $this->errores->error(mensaje: 'Error al obtener inputs', data: $inputs);
            }

            $this->inputs = $inputs;
        } else if ($this->accion === "anticipo"){

            $r_template = $this->controlador_em_anticipo->alta(header: false);
            if (errores::$error) {
                return $this->errores->error(mensaje: 'Error al obtener template', data: $r_template);
            }

            $keys_selects = $this->controlador_em_anticipo->init_selects_inputs();
            if (errores::$error) {
                return $this->errores->error(mensaje: 'Error al inicializar selects', data: $keys_selects);
            }

            $keys_selects['em_empleado_id']->id_selected = $this->registro_id;
            $keys_selects['em_empleado_id']->filtro = array("em_empleado.id" => $this->registro_id);
            $keys_selects['em_empleado_id']->disabled = true;

            $inputs = $this->controlador_em_anticipo->inputs(keys_selects: $keys_selects);
            if (errores::$error) {
                return $this->errores->error(mensaje: 'Error al obtener inputs', data: $inputs);
            }

            $this->inputs = $inputs;
        }
        return $this->inputs;
    }

    public function valida_campos(array $campos): array
    {
        $campos_validos = array('documentos', 'receptor', 'asunto', 'mensaje');
        $campos_faltantes = array_diff($campos_validos, array_keys($campos));
        if (!empty($campos_faltantes)) {
            $mensaje_error = 'Faltan los siguientes campos: ' . implode(', ', $campos_faltantes);
            return $this->errores->error(mensaje: $mensaje_error, data: $campos_faltantes);
        }

        return $campos;
    }

    function separar_correos(string $correos) :array {
        return preg_split('/[;,]/', $correos);
    }

    public function valida_receptor(array $correos): array|bool
    {
        foreach ($correos as $receptor) {
            $validacion = (new _email($this->link))->validar_correo(correo: $receptor);
            if (!$validacion) {
                $mensaje_error = sprintf(_email::ERROR_CORREO_NO_VALIDO, $receptor);
                return $this->errores->error(mensaje: $mensaje_error, data: $mensaje_error);
            }
        }

        return true;
    }

    final public function envia_documentos(bool $header, bool $ws = false): array|stdClass
    {
        $campos_necesarios = $this->valida_campos($_POST);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al validar campos', data: $campos_necesarios,
                header: $header, ws: $ws);
        }

        $correos = $this->separar_correos(correos: $campos_necesarios['receptor']);
        if (empty($correos)) {
            $mensaje_error = 'No se encontraron correos válidos';
            return $this->errores->error(mensaje: $mensaje_error, data: $correos);
        }

        $valida_correos = $this->valida_receptor(correos: $correos);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al validar correos', data: $valida_correos,
                header: $header, ws: $ws);
        }

        $this->link->beginTransaction();

        $siguiente_view = (new actions())->init_alta_bd();
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener siguiente view', data: $siguiente_view,
                header: $header, ws: $ws);
        }

        $emisor = (new _email($this->link))->emisor(correo: 'factura@efacturacion.com.mx');
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener emisor', data: $emisor,
                header: $header, ws: $ws);
        }

        $mensaje = (new _email($this->link))->mensaje(asunto: $campos_necesarios['asunto'],
            mensaje: $campos_necesarios['mensaje'], emisor: $emisor['not_emisor_id']);
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener mensaje', data: $mensaje,
                header: $header, ws: $ws);
        }

        foreach ($correos as $correo) {
            $receptor = (new _email($this->link))->receptor(correo: $correo);
            if (errores::$error) {
                $this->link->rollBack();
                return $this->retorno_error(mensaje: 'Error al obtener receptor', data: $receptor,
                    header: $header, ws: $ws);
            }

            $mensaje_receptor = (new _email($this->link))->mensaje_receptor(mensaje: $mensaje['not_mensaje_id'],
                receptor: $receptor['not_receptor_id']);
            if (errores::$error) {
                $this->link->rollBack();
                return $this->retorno_error(mensaje: 'Error al obtener mensaje receptor', data: $mensaje_receptor,
                    header: $header, ws: $ws);
            }
        }

        $documentos_seleccionados = explode(',', $campos_necesarios['documentos']);
        $documentos = array();

        foreach ($documentos_seleccionados as $documento) {
            $registro = (new em_empleado_documento($this->link))->registro(registro_id: $documento, columnas: ['doc_documento_id']);
            if (errores::$error) {
                $this->link->rollBack();
                return $this->retorno_error(mensaje: 'Error al obtener documento', data: $registro,
                    header: $header, ws: $ws);
            }
            $documentos[] = $registro['doc_documento_id'];
        }

        $r_alta_doc_etapa = new stdClass();

        $mensaje_adjuntos = (new _email($this->link))->adjuntos(mensaje: $mensaje['not_mensaje_id'],
            documentos: $documentos);
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener adjuntos', data: $mensaje_adjuntos,
                header: $header, ws: $ws);
        }

        $mensaje_enviado = (new not_mensaje($this->link))->envia_mensaje(not_mensaje_id: $mensaje['not_mensaje_id']);
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al enviar mensaje', data: $mensaje_enviado,
                header: $header, ws: $ws);
        }

        $this->link->commit();

        if ($header) {
            $this->retorno_base(registro_id: $this->registro_id, result: $r_alta_doc_etapa,
                siguiente_view: "documentos", ws: $ws);
        }
        if ($ws) {
            header('Content-Type: application/json');
            echo json_encode($r_alta_doc_etapa, JSON_THROW_ON_ERROR);
            exit;
        }
        $r_alta_doc_etapa->siguiente_view = "documentos";

        return $r_alta_doc_etapa;
    }

    protected function key_selects_txt(array $keys_selects): array
    {
        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'codigo',
            keys_selects: $keys_selects, place_holder: 'Código');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'nombre',
            keys_selects: $keys_selects, place_holder: 'Nombre');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'ap',
            keys_selects: $keys_selects, place_holder: 'Apellido Paterno');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'am',
            keys_selects: $keys_selects, place_holder: 'Apellido Materno',required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'telefono',
            keys_selects: $keys_selects, place_holder: 'Teléfono');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'rfc',
            keys_selects: $keys_selects, place_holder: 'RFC');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'curp',
            keys_selects: $keys_selects, place_holder: 'CURP');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'nss',
            keys_selects: $keys_selects, place_holder: 'NSS', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'fecha_inicio_rel_laboral',
            keys_selects: $keys_selects, place_holder: 'Fecha Relación Laboral');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 4, key: 'salario_diario',
            keys_selects: $keys_selects, place_holder: 'Salario Diario');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 4, key: 'salario_diario_integrado',
            keys_selects: $keys_selects, place_holder: 'Salario Diario Integrado');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'fecha_inicio',
            keys_selects: $keys_selects, place_holder: 'Fecha Inicio');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'com_sucursal',
            keys_selects: $keys_selects, place_holder: 'Comercial Sucursal');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'org_sucursal',
            keys_selects: $keys_selects, place_holder: 'Comercial Sucursal');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 4, key: 'salario_total',
            keys_selects: $keys_selects, place_holder: 'Salario Total');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'correo',
            keys_selects: $keys_selects, place_holder: 'Correo');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'fecha_final',
            keys_selects: $keys_selects, place_holder: 'Fecha Final');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'numero_exterior',
            keys_selects: $keys_selects, place_holder: 'Numero Exterior');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'numero_interior',
            keys_selects: $keys_selects, place_holder: 'Numero Interior', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'cp',
            keys_selects: $keys_selects, place_holder: 'CP');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'colonia',
            keys_selects: $keys_selects, place_holder: 'Colonia');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 6, key: 'calle',
            keys_selects: $keys_selects, place_holder: 'Calle');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        return $keys_selects;
    }

    public function lee_archivo(bool $header, bool $ws = false)
    {
        $doc_documento_modelo = new doc_documento($this->link);
        $doc_documento_modelo->registro['descripcion'] = rand();
        $doc_documento_modelo->registro['descripcion_select'] = rand();
        $doc_documento_modelo->registro['doc_tipo_documento_id'] = 1;
        $doc_documento = $doc_documento_modelo->alta_bd(file: $_FILES['archivo']);
        if (errores::$error) {
            $error =  $this->errores->error(mensaje: 'Error al dar de alta el documento', data: $doc_documento);
            if(!$header){
                return $error;
            }
            print_r($error);
            die('Error');
        }

        $empleados_excel = $this->obten_empleados_excel(
            ruta_absoluta: $doc_documento->registro['doc_documento_ruta_absoluta']);
        if (errores::$error) {
            $error =  $this->errores->error(mensaje: 'Error obtener empleados',data:  $empleados_excel);
            if(!$header){
                return $error;
            }
            print_r($error);
            die('Error');
        }

        foreach ($empleados_excel as $empleado){

            $registro = array();
            $keys = array('codigo','nombre','ap','am','telefono','curp','rfc','nss','fecha_inicio_rel_laboral',
                'salario_diario','salario_diario');
            foreach ($keys as $key){
                if(isset($empleado->$key)){
                    $registro[$key] = $empleado->$key;
                }
            }

            $em_empleado = new em_empleado($this->link);
            $em_empleado->registro = $registro;
            $r_alta = $em_empleado->alta_bd();
            if (errores::$error) {
                $error = $this->errores->error(mensaje: 'Error al dar de alta registro', data: $r_alta);
                if (!$header) {
                    return $error;
                }
                print_r($error);
                die('Error');
            }
        }

        $link = "./index.php?seccion=em_empleado&accion=lista&registro_id=".$this->registro_id;
        $link.="&session_id=$this->session_id";
        header('Location:' . $link);
        exit;
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

        $em_empleado_rfc = (new em_empleado_html(html: $this->html_base))->input_rfc(cols: 6, row_upd: $this->row_upd,
            value_vacio: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar input', data: $em_empleado_rfc);
        }

        $em_empleado_curp = (new em_empleado_html(html: $this->html_base))->input_curp(cols: 12, row_upd: $this->row_upd,
            value_vacio: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar input', data: $em_empleado_curp);
        }

        $em_empleado_nss = (new em_empleado_html(html: $this->html_base))->input_nss(cols: 6, row_upd: $this->row_upd,
            value_vacio: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar input', data: $em_empleado_nss);
        }

        $this->inputs->em_empleado_rfc = $em_empleado_rfc;
        $this->inputs->em_empleado_curp = $em_empleado_curp;
        $this->inputs->em_empleado_nss = $em_empleado_nss;

        $dp_municipio = (new dp_municipio($this->link))->get_municipio($this->registro['em_empleado_dp_municipio_id']);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener dp_municipio', data: $dp_municipio);
        }

        $keys_selects['dp_pais_id']->id_selected = $dp_municipio['dp_pais_id'];

        $keys_selects['dp_estado_id']->con_registros = true;
        $keys_selects['dp_estado_id']->filtro = array("dp_pais.id" => $dp_municipio['dp_pais_id']);
        $keys_selects['dp_estado_id']->id_selected = $dp_municipio['dp_estado_id'];

        $keys_selects['dp_municipio_id']->con_registros = true;
        $keys_selects['dp_municipio_id']->filtro = array("dp_estado.id" => $dp_municipio['dp_estado_id']);
        $keys_selects['dp_municipio_id']->id_selected = $dp_municipio['dp_municipio_id'];

        $keys_selects['cat_sat_regimen_fiscal_id']->id_selected = $this->registro['cat_sat_regimen_fiscal_id'];
        $keys_selects['cat_sat_tipo_regimen_nom_id']->id_selected = $this->registro['cat_sat_tipo_regimen_nom_id'];
        $keys_selects['cat_sat_tipo_jornada_nom_id']->id_selected = $this->registro['cat_sat_tipo_jornada_nom_id'];
        $keys_selects['org_puesto_id']->id_selected = $this->registro['org_puesto_id'];
        $keys_selects['em_centro_costo_id']->id_selected = $this->registro['em_centro_costo_id'];
        $keys_selects['em_registro_patronal_id']->id_selected = $this->registro['em_registro_patronal_id'];

        $base = $this->base_upd(keys_selects: $keys_selects, params: array(), params_ajustados: array());
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar base', data: $base, header: $header, ws: $ws);
        }

        return $r_modifica;
    }

    public function obten_empleados_excel(string $ruta_absoluta){
        $documento = IOFactory::load($ruta_absoluta);
        $empleados = array();
        $hojaActual = $documento->getSheet(0);

        $registros = array();
        foreach ($hojaActual->getRowIterator() as $fila) {
            foreach ($fila->getCellIterator() as $celda) {
                $fila = $celda->getRow();
                $columna = $celda->getColumn();

                if($fila >= 2){
                    if($columna === "A"){
                        $reg = new stdClass();
                        $reg->fila = $fila;
                        $registros[] = $reg;
                    }
                }
            }
        }

        foreach ($registros as $registro) {
            $reg = new stdClass();
            $reg->codigo = $hojaActual->getCell('A' . $registro->fila)->getValue();
            $reg->nombre = $hojaActual->getCell('B' . $registro->fila)->getValue();
            $reg->ap = $hojaActual->getCell('C' . $registro->fila)->getValue();
            $reg->am = $hojaActual->getCell('D' . $registro->fila)->getValue();
            $reg->telefono = $hojaActual->getCell('E' . $registro->fila)->getValue();
            $reg->curp = $hojaActual->getCell('F' . $registro->fila)->getValue();
            $reg->rfc = $hojaActual->getCell('G' . $registro->fila)->getValue();
            $reg->nss = $hojaActual->getCell('H' . $registro->fila)->getValue();

            $fecha = $hojaActual->getCell('I' . $registro->fila)->getCalculatedValue();
            $reg->fecha_inicio_rel_laboral  = Date::excelToDateTimeObject($fecha)->format('Y-m-d');

            $reg->sd = $hojaActual->getCell('J' . $registro->fila)->getValue();
            $reg->fi = $hojaActual->getCell('K' . $registro->fila)->getValue();
            $reg->sdi = $hojaActual->getCell('L' . $registro->fila)->getValue();

            $reg->numero_cuenta = $hojaActual->getCell('M' . $registro->fila)->getValue();
            $reg->clabe = $hojaActual->getCell('N' . $registro->fila)->getValue();
            $empleados[] = $reg;
        }

        return $empleados;
    }

    public function reporte_remunerado(bool $header, bool $ws = false): array|stdClass
    {
        $r_alta = $this->init_alta();
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al inicializar alta', data: $r_alta, header: $header, ws: $ws);
        }

        $this->asignar_propiedad(identificador: 'em_empleado_id',
            propiedades: ["id_selected" => $this->registro_id, "disabled" => true, "cols" => 12,
                "filtro" => array('em_empleado.id' => $this->registro_id), "label" => "Empleado"]);

        $this->inputs = $this->genera_inputs(
            keys_selects:  $this->keys_selects);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al generar inputs', data: $this->inputs);
            print_r($error);
            die('Error');
        }

        $inputs = $this->inputs(keys_selects: $this->keys_selects);
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs', data: $inputs, header: $header, ws: $ws);
        }

        return $this->inputs;
    }

    public function reportes(bool $header, bool $ws = false): array|stdClass
    {
        $r_alta = $this->init_alta();
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al inicializar alta', data: $r_alta, header: $header, ws: $ws);
        }

        $inputs = $this->inputs(keys_selects: array());
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs', data: $inputs, header: $header, ws: $ws);
        }

        return $this->inputs;
    }

    public function sube_archivo(bool $header, bool $ws = false){
        $r_alta =  parent::alta(header: false,ws:  false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al generar template',data:  $r_alta);
        }

        return $r_alta;
    }




    // ----- POR REVISAR -----

    public array|stdClass $keys_selects = array();
    public stdClass $anticipos;
    public stdClass $abonos;
    public stdClass $conf_nominas;

    public string $link_nom_conf_empleado_alta_bd = '';
    public string $link_em_abono_anticipo_modifica_bd = '';


    public int $em_anticipo_id = -1;
    public int $em_abono_anticipo_id = -1;








    public function abono(bool $header, bool $ws = false): array|stdClass
    {
        $alta = $this->controlador_em_abono_anticipo->alta(header: false);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al generar template', data: $alta, header: $header, ws: $ws);
        }

        $this->controlador_em_abono_anticipo->asignar_propiedad(identificador: 'em_anticipo_id',
            propiedades: ["id_selected" => $this->em_anticipo_id, "disabled" => true,
                "filtro" => array('em_anticipo.id' => $this->em_anticipo_id)]);

        $this->inputs = $this->controlador_em_abono_anticipo->genera_inputs(
            keys_selects:  $this->controlador_em_abono_anticipo->keys_selects);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al generar inputs', data: $this->inputs);
            print_r($error);
            die('Error');
        }

        $abonos = (new em_abono_anticipo($this->link))->get_abonos_anticipo(em_anticipo_id: $this->em_anticipo_id);
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

        $_POST['em_anticipo_id'] = $this->em_anticipo_id;

        $alta = (new em_abono_anticipo($this->link))->alta_registro(registro: $_POST);
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al dar de alta abono', data: $alta,
                header: $header, ws: $ws);
        }

        $this->link->commit();

        if ($header) {
            $this->retorno_base(registro_id:$this->registro_id, result: $alta,
                siguiente_view: "abono", ws:  $ws, params: ['em_anticipo_id'=>$this->em_anticipo_id]);
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


    private function asigna_keys_post(array $keys_generales): array
    {
        $registro = array();
        foreach ($keys_generales as $key_general){
            $registro = $this->asigna_key_post(key_general: $key_general,registro:  $registro);
            if(errores::$error){
                return $this->errores->error(mensaje: 'Error al asignar key post',data:  $registro);
            }
        }
        return $registro;
    }

    private function asigna_key_post(string $key_general, array $registro): array
    {
        if(isset($_POST[$key_general])){
            $registro[$key_general] = $_POST[$key_general];
        }
        return $registro;
    }


    public function asignar_propiedad(string $identificador, array $propiedades): array|stdClass
    {
        $identificador = trim($identificador);
        if($identificador === ''){
            return $this->errores->error(mensaje: 'Error identificador esta vacio',data:  $identificador);
        }

        if (!array_key_exists($identificador,$this->keys_selects)){
            $this->keys_selects[$identificador] = new stdClass();
        }

        foreach ($propiedades as $key => $value){
            $this->keys_selects[$identificador]->$key = $value;
        }
        return $this->keys_selects;
    }

    private function base(): array|stdClass
    {
        $r_modifica =  parent::modifica(header: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al generar template',data:  $r_modifica);
        }

        $direccion = (new em_empleado($this->link))->get_direccion(
            dp_calle_pertenece_id: $this->row_upd->dp_calle_pertenece_id);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener direccion',data:  $direccion);
        }

        $this->asignar_propiedad(identificador:'dp_pais_id',
            propiedades: ["id_selected"=> $direccion["dp_pais_id"]]);
        $this->asignar_propiedad(identificador:'dp_estado_id',
            propiedades: ["id_selected"=> $direccion["dp_estado_id"],"con_registros"=>true,
                "filtro" => array('dp_estado.id' => $direccion["dp_estado_id"])]);
        $this->asignar_propiedad(identificador:'dp_municipio_id',
            propiedades: ["id_selected"=> $direccion["dp_municipio_id"],"con_registros"=>true,
                "filtro" => array('dp_municipio.id' => $direccion["dp_municipio_id"])]);
        $this->asignar_propiedad(identificador:'dp_cp_id',
            propiedades: ["id_selected"=> $direccion["dp_cp_id"],"con_registros"=>true,
                "filtro" => array('dp_cp.id' => $direccion["dp_cp_id"])]);
        $this->asignar_propiedad(identificador:'dp_colonia_postal_id',
            propiedades: ["id_selected"=> $direccion["dp_colonia_postal_id"],"con_registros"=>true,
                "filtro" => array('dp_colonia_postal.id' => $direccion["dp_colonia_postal_id"])]);
        $this->asignar_propiedad(identificador:'dp_calle_pertenece_id',
            propiedades: ["id_selected"=>$this->row_upd->dp_calle_pertenece_id,"con_registros"=>true,
                "filtro" => array('dp_calle_pertenece.id' => $this->row_upd->dp_calle_pertenece_id)]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador:'cat_sat_regimen_fiscal_id',
            propiedades: ["id_selected"=>$this->row_upd->cat_sat_regimen_fiscal_id]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador:'em_registro_patronal_id',
            propiedades: ["id_selected"=>$this->row_upd->em_registro_patronal_id]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador:'org_puesto_id',
            propiedades: ["id_selected"=>$this->row_upd->org_puesto_id]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador:'cat_sat_tipo_regimen_nom_id',
            propiedades: ["id_selected"=>$this->row_upd->cat_sat_tipo_regimen_nom_id]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }


        $this->asignar_propiedad(identificador:'cat_sat_tipo_jornada_nom_id',
            propiedades: ["id_selected"=>$this->row_upd->cat_sat_tipo_jornada_nom_id]);
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

    private function data_anticipo_btn(array $anticipo): array
    {
        $params['em_anticipo_id'] = $anticipo['em_anticipo_id'];

        $btn_abono = $this->html_base->button_href(accion: 'abono', etiqueta: 'Abono',
            registro_id: $this->registro_id, seccion: 'em_empleado', style: 'info',params: $params);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al generar btn', data: $btn_abono);
        }
        $anticipo['link_abono'] = $btn_abono;

        $btn_elimina = $this->html_base->button_href(accion: 'anticipo_elimina_bd', etiqueta: 'Elimina',
            registro_id: $this->registro_id, seccion: 'em_empleado', style: 'danger',params: $params);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al generar btn', data: $btn_elimina);
        }
        $anticipo['link_elimina'] = $btn_elimina;

        $btn_modifica = $this->html_base->button_href(accion: 'anticipo_modifica', etiqueta: 'Modifica',
            registro_id: $this->registro_id, seccion: 'em_empleado', style: 'warning',params: $params);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al generar btn', data: $btn_modifica);
        }
        $anticipo['link_modifica'] = $btn_modifica;

        return $anticipo;
    }




    private function data_abono_btn(array $abono): array
    {
        $params['em_abono_anticipo_id'] = $abono['em_abono_anticipo_id'];
        $params['em_anticipo_id'] = $abono['em_anticipo_id'];

        $btn_elimina = $this->html_base->button_href(accion: 'abono_elimina_bd', etiqueta: 'Elimina',
            registro_id: $this->registro_id, seccion: 'em_empleado', style: 'danger',params: $params);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al generar btn', data: $btn_elimina);
        }
        $abono['link_elimina'] = $btn_elimina;

        $btn_modifica = $this->html_base->button_href(accion: 'abono_modifica', etiqueta: 'Modifica',
            registro_id: $this->registro_id, seccion: 'em_empleado', style: 'warning',params: $params);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al generar btn', data: $btn_modifica);
        }
        $abono['link_modifica'] = $btn_modifica;

        return $abono;
    }

    public function fiscales(bool $header, bool $ws = false): array|stdClass
    {
        $base = $this->base();
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar datos',data:  $base,
                header: $header,ws:$ws);
        }

        return $base;

    }

    public function imss(bool $header, bool $ws = false): array|stdClass
    {
        $base = $this->base();
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar datos',data:  $base,
                header: $header,ws:$ws);
        }

        return $base;

    }


    public function modifica_fiscales(bool $header, bool $ws = false): array|stdClass
    {
        $keys_fiscales[] = 'cat_sat_regimen_fiscal_id';
        $keys_fiscales[] = 'rfc';

        $r_modifica_bd = $this->upd_base(keys_generales: $keys_fiscales);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al modificar cif',data:  $r_modifica_bd,
                header: $header,ws:$ws);
        }

        $_SESSION[$r_modifica_bd->salida][]['mensaje'] = $r_modifica_bd->mensaje.' del id '.$this->registro_id;
        $this->header_out(result: $r_modifica_bd, header: $header,ws:  $ws);

        return $r_modifica_bd;
    }


    private function upd_base(array $keys_generales): array|stdClass
    {
        $registro = $this->asigna_keys_post(keys_generales: $keys_generales);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al asignar keys post',data:  $registro);
        }

        $r_modifica_bd = $this->modelo->modifica_bd(registro: $registro, id: $this->registro_id);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al modificar generales',data:  $r_modifica_bd);
        }
        return $r_modifica_bd;
    }



    public function ver_anticipos(bool $header, bool $ws = false): array|stdClass
    {
        $anticipos = (new em_anticipo($this->link))->get_anticipos_empleado(em_empleado_id: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener anticipos',data:  $anticipos,
                header: $header,ws:$ws);
        }

        foreach ($anticipos->registros as $indice => $anticipo) {

            $anticipo = $this->data_anticipo_btn(anticipo: $anticipo);

            if (errores::$error) {
                return $this->retorno_error(mensaje: 'Error al asignar botones', data: $anticipo, header: $header, ws: $ws);
            }

            $anticipo['em_anticipo_saldo_pendiente'] = (new em_anticipo($this->link))->get_saldo_anticipo($anticipo['em_anticipo_id']);
            if(errores::$error){
                return $this->errores->error(mensaje: 'Error al obtener el saldo pendiente',data:  $anticipo);
            }

            $anticipo['em_anticipo_total_abonado'] = (new em_abono_anticipo($this->link))->get_total_abonado($anticipo['em_anticipo_id']);
            if(errores::$error){
                return $this->errores->error(mensaje: 'Error al obtener el total abonado',data:  $anticipo);
            }
            $anticipos->registros[$indice] = $anticipo;
        }

        $this->anticipos = $anticipos;

        return $this->anticipos;
    }



}