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
use gamboamartin\documento\models\doc_documento;
use gamboamartin\empleado\models\em_anticipo;
use gamboamartin\empleado\models\em_empleado;
use gamboamartin\empleado\models\em_metodo_calculo;
use gamboamartin\empleado\models\em_tipo_anticipo;
use gamboamartin\empleado\models\em_tipo_descuento;
use gamboamartin\errores\errores;
use gamboamartin\plugins\Importador;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use html\em_anticipo_html;
use PDO;
use stdClass;

class controlador_em_anticipo extends _ctl_base
{
    public controlador_em_abono_anticipo $controlador_em_abono_anticipo;

    public string $link_em_abono_anticipo_alta_bd = '';
    public string $link_em_anticipo_lee_archivo = '';

    public function __construct(PDO      $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass())
    {
        $modelo = new em_anticipo(link: $link);
        $html_ = new em_anticipo_html(html: $html);
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

    public function abono(bool $header = true, bool $ws = false, array $not_actions = array()): array|string
    {
        $seccion = "em_abono_anticipo";

        $data_view = new stdClass();
        $data_view->names = array('Id', 'Tipo Abono', 'Tipo Anticipo', 'Forma Pago', 'Monto', 'Acciones');
        $data_view->keys_data = array($seccion . "_id", "em_tipo_abono_anticipo_descripcion", 'em_tipo_anticipo_descripcion',
            'cat_sat_forma_pago_descripcion', $seccion . '_monto');
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
        $keys->inputs = array('codigo', 'descripcion', 'monto', 'n_pagos', 'comentarios');
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

    public function get_anticipos(bool $header, bool $ws = true): array|stdClass
    {
        $keys['em_empleado'] = array('id', 'descripcion', 'codigo', 'codigo_bis');

        $salida = $this->get_out(header: $header, keys: $keys, ws: $ws);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al generar salida', data: $salida, header: $header, ws: $ws);
        }

        return $salida;
    }

    protected function init_configuraciones(): controler
    {
        $this->seccion_titulo = 'Anticipo';
        $this->titulo_lista = 'Registro de Anticipos';

        $this->lista_get_data = true;

        return $this;
    }

    protected function init_controladores(stdClass $paths_conf): controler
    {
        $this->controlador_em_abono_anticipo = new controlador_em_abono_anticipo(link: $this->link,
            paths_conf: $paths_conf);

        return $this;
    }

    protected function init_datatable(): stdClass
    {
        $columns["em_anticipo_id"]["titulo"] = "Id";
        $columns["em_empleado_nss"]["titulo"] = "NSS";
        $columns["em_empleado_nombre"]["titulo"] = "Empleado";
        $columns["em_empleado_nombre"]["campos"] = array("em_empleado_ap", "em_empleado_am");
        $columns["em_tipo_anticipo_descripcion"]["titulo"] = "Tipo Anticipo";
        $columns["em_anticipo_monto"]["titulo"] = "Monto";
        $columns["em_anticipo_fecha_prestacion"]["titulo"] = "Fecha Prestacion";
        $columns["total_abonado"]["titulo"] = "Abonado";
        $columns["em_anticipo_saldo"]["titulo"] = "Saldo";

        $filtro = array("em_anticipo.id", "em_empleado.nss", "em_empleado.nombre", "em_empleado.ap", "em_empleado.am",
            "em_tipo_anticipo.descripcion", "em_anticipo.monto", "em_anticipo.fecha_prestacion");

        $datatables = new stdClass();
        $datatables->columns = $columns;
        $datatables->filtro = $filtro;

        return $datatables;
    }

    protected function init_links(): array|string
    {
        $links = $this->obj_link->genera_links(controler: $this);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al generar links', data: $links);
            print_r($error);
            exit;
        }

        $link = $this->obj_link->get_link(seccion: "em_abono_anticipo", accion: "alta_bd");
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al recuperar link abono_alta_bd', data: $link);
            print_r($error);
            exit;
        }
        $this->link_em_abono_anticipo_alta_bd = $link;

        $link = $this->obj_link->get_link(seccion: "em_anticipo", accion: "lee_archivo");
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al recuperar link abono_alta_bd', data: $link);
            print_r($error);
            exit;
        }
        $this->link_em_anticipo_lee_archivo = $link;

        return $link;
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
        $keys_selects = $this->init_selects(keys_selects: array(), key: "em_tipo_anticipo_id", label: "Tipo Anticipo");
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "em_empleado_id", label: "Empleado",
            cols: 12);
        return $this->init_selects(keys_selects: $keys_selects, key: "em_tipo_descuento_id", label: "Tipo Descuento");
    }

    protected function inputs_children(stdClass $registro): array|stdClass
    {
        $r_template = $this->controlador_em_abono_anticipo->alta(header: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener template', data: $r_template);
        }

        $keys_selects = $this->controlador_em_abono_anticipo->init_selects_inputs();
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar selects', data: $keys_selects);
        }

        $this->controlador_em_abono_anticipo->row_upd->fecha = date('Y-m-d');

        $keys_selects['em_anticipo_id']->id_selected = $this->registro_id;
        $keys_selects['em_anticipo_id']->filtro = array('em_anticipo.id' => $this->registro_id);
        $keys_selects['em_anticipo_id']->disabled = true;

        $this->inputs = $this->controlador_em_abono_anticipo->genera_inputs(
            keys_selects: $this->controlador_em_abono_anticipo->keys_selects);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al generar inputs', data: $this->inputs);
            print_r($error);
            die('Error');
        }

        $inputs = $this->controlador_em_abono_anticipo->inputs(keys_selects: $keys_selects);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al obtener inputs', data: $inputs);
        }

        $this->inputs = $inputs;

        return $this->inputs;
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

        $keys_selects = (new \base\controller\init())->key_select_txt(cols: 12, key: 'comentarios',
            keys_selects: $keys_selects, place_holder: 'Comentarios', required: false);
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
            $error = $this->errores->error(mensaje: 'Error al dar de alta el documento', data: $doc_documento);
            if (!$header) {
                return $error;
            }
            print_r($error);
            die('Error');
        }

        $columnas = array("nss", "nombre", "ap", "am", "empresa", "fecha_inicio", "fecha_compromiso", "concepto",
            "importe", "descuento_periodo", "comentarios");
        $fechas = array("fecha_inicio", "fecha_compromiso");

        $anticipos_excel = Importador::getInstance()
            ->leer_registros(ruta_absoluta: $doc_documento->registro['doc_documento_ruta_absoluta'], columnas: $columnas,
                fechas: $fechas);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al leer archivo de anticipos', data: $anticipos_excel);
            if (!$header) {
                return $error;
            }
            print_r($error);
            die('Error');
        }

        $this->link->beginTransaction();

        foreach ($anticipos_excel as $anticipo) {
            if (!isset($anticipo->nss)) {
                $error = $this->errores->error(mensaje: 'Error el campo NSS es requerido', data: $anticipo);
                if (!$header) {
                    return $error;
                }
                print_r($error);
                die('Error');
            }

            if (!isset($anticipo->fecha_inicio)) {
                $anticipo->fecha_inicio = date('Y-m-d');
            }

            if (!isset($anticipo->fecha_compromiso)) {
                $anticipo->fecha_compromiso = date('Y-m-d');
            }

            if (!isset($anticipo->concepto)) {
                $error = $this->errores->error(mensaje: 'Error el campo CONCEPTO es requerido', data: $anticipo);
                if (!$header) {
                    return $error;
                }
                print_r($error);
                die('Error');
            }

            if (!isset($anticipo->importe)) {
                $error = $this->errores->error(mensaje: 'Error el campo IMPORTE es requerido', data: $anticipo);
                if (!$header) {
                    return $error;
                }
                print_r($error);
                die('Error');
            }

            if (!is_numeric($anticipo->importe)) {
                $error = $this->errores->error(mensaje: 'Error el campo IMPORTE tiene que un numero', data: $anticipo);
                if (!$header) {
                    return $error;
                }
                print_r($error);
                die('Error');
            }

            if (!is_numeric($anticipo->descuento_periodo)) {
                $error = $this->errores->error(mensaje: 'Error el campo DESCUENTO PERIODO tiene que un numero', data: $anticipo);
                if (!$header) {
                    return $error;
                }
                print_r($error);
                die('Error');
            }

            $filtro_empleado['em_empleado.nss'] = $anticipo->nss;
            $em_empleado = (new em_empleado($this->link))->filtro_and(filtro: $filtro_empleado);
            if (errores::$error) {
                $error = $this->errores->error(mensaje: 'Error obtener datos el empleado', data: $em_empleado);
                if (!$header) {
                    return $error;
                }
                print_r($error);
                die('Error');
            }

            if ($em_empleado->n_registros <= 0) {
                $error = $this->errores->error(mensaje: "Error no existe el NSS: $anticipo->nss", data: $anticipo);
                if (!$header) {
                    return $error;
                }
                print_r($error);
                die('Error');
            } else if ($em_empleado->n_registros > 1) {
                $error = $this->errores->error(mensaje: "Error el NSS: $anticipo->nss esta asignado a varios empleados",
                    data: $anticipo);
                if (!$header) {
                    return $error;
                }
                print_r($error);
                die('Error');
            }

            $filtro_tipo_anticipo['em_tipo_anticipo.descripcion'] = $anticipo->concepto;
            $tipo_anticipo = (new em_tipo_anticipo($this->link))->filtro_and(filtro: $filtro_tipo_anticipo);
            if (errores::$error) {
                $error = $this->errores->error(mensaje: 'Error al obtener tipo de anticipo', data: $tipo_anticipo);
                if (!$header) {
                    return $error;
                }
                print_r($error);
                die('Error');
            }

            if ($tipo_anticipo->n_registros <= 0) {
                $error = $this->errores->error(mensaje: "Error no existe el CONCEPTO: $anticipo->concepto", data: $anticipo);
                if (!$header) {
                    return $error;
                }
                print_r($error);
                die('Error');
            }

            $filtro_tipo_descuento['em_tipo_descuento.monto'] = $anticipo->descuento_periodo;
            $tipo_descuento = (new em_tipo_descuento($this->link))->filtro_and(filtro: $filtro_tipo_descuento);
            if (errores::$error) {
                $error = $this->errores->error(mensaje: 'Error al obtener el tipo de descuento', data: $tipo_descuento);
                if (!$header) {
                    return $error;
                }
                print_r($error);
                die('Error');
            }

            if ($tipo_descuento->n_registros <= 0) {
                $filtro_metodo_calculo['em_metodo_calculo.descripcion'] = "monto_fijo";
                $metodo_calculo = (new em_metodo_calculo($this->link))->filtro_and(filtro: $filtro_metodo_calculo, limit: 1);
                if (errores::$error) {
                    $error = $this->errores->error(mensaje: 'Error al obtener el metodo de calculo', data: $filtro_metodo_calculo);
                    if (!$header) {
                        return $error;
                    }
                    print_r($error);
                    die('Error');
                }

                if ($metodo_calculo->n_registros <= 0) {
                    $error = $this->errores->error(mensaje: 'Error no existe el metodo de calculo: monto_fijo', data: $metodo_calculo);
                    if (!$header) {
                        return $error;
                    }
                    print_r($error);
                    die('Error');
                }

                $data['codigo'] = rand() . $anticipo->descuento_periodo;
                $data['descripcion'] = "monto_fijo " . $anticipo->descuento_periodo;
                $data['em_metodo_calculo_id'] = $metodo_calculo->registros[0]['em_metodo_calculo_id'];
                $data['monto'] = $anticipo->descuento_periodo;
                $alta = (new em_tipo_descuento($this->link))->alta_registro(registro: $data);
                if (errores::$error) {
                    $this->link->rollBack();
                    $error = $this->errores->error(mensaje: 'Error al dar de alta tipo de descuento', data: $alta);
                    if (!$header) {
                        return $error;
                    }
                    print_r($error);
                    die('Error');
                }

                $em_tipo_descuento_id = $alta->registro_id;
            } else {
                $em_tipo_descuento_id = $tipo_descuento->registros[0]['em_tipo_descuento_id'];
            }

            $registro = array();
            $registro['em_empleado_id'] = $em_empleado->registros[0]['em_empleado_id'];
            $registro['em_tipo_anticipo_id'] = $tipo_anticipo->registros[0]['em_tipo_anticipo_id'];
            $registro['em_tipo_descuento_id'] = $em_tipo_descuento_id;
            $registro['codigo'] = rand() . $anticipo->concepto;
            $registro['descripcion'] = $anticipo->concepto;
            $registro['monto'] = $anticipo->importe;
            $registro['n_pagos'] = 1;
            $registro['fecha_prestacion'] = $anticipo->fecha_compromiso;
            $registro['fecha_inicio_descuento'] = $anticipo->fecha_inicio;
            $registro['comentarios'] = $anticipo->comentarios;

            $alta = (new em_anticipo($this->link))->alta_registro(registro: $registro);
            if (errores::$error) {
                $this->link->rollBack();
                $error = $this->errores->error(mensaje: 'Error al dar de alta anticipo', data: $alta);
                if (!$header) {
                    return $error;
                }
                print_r($error);
                die('Error');
            }
        }
        $this->link->commit();

        header('Location:' . $this->link_lista);
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

        $keys_selects['em_tipo_anticipo_id']->id_selected = $this->registro['em_tipo_anticipo_id'];
        $keys_selects['em_empleado_id']->id_selected = $this->registro['em_empleado_id'];
        $keys_selects['em_tipo_descuento_id']->id_selected = $this->registro['em_tipo_descuento_id'];

        $base = $this->base_upd(keys_selects: $keys_selects, params: array(), params_ajustados: array());
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar base', data: $base, header: $header, ws: $ws);
        }

        return $r_modifica;
    }

    public function sube_archivo(bool $header, bool $ws = false)
    {
        $r_alta = parent::alta(header: false, ws: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al generar template', data: $r_alta);
        }

        return $r_alta;
    }
}
