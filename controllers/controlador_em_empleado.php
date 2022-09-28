<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\empleado\controllers;

use gamboamartin\empleado\models\em_anticipo;
use gamboamartin\empleado\models\em_cuenta_bancaria;
use gamboamartin\errores\errores;
use gamboamartin\system\actions;
use gamboamartin\system\links_menu;
use gamboamartin\system\system;
use gamboamartin\template\html;
use html\em_cuenta_bancaria_html;
use html\em_empleado_html;
use gamboamartin\empleado\models\em_empleado;
use PDO;
use stdClass;
use Throwable;

class controlador_em_empleado extends system {

    public array $keys_selects = array();
    public stdClass $cuentas_bancarias;
    public stdClass $anticipos;
    public string $link_em_anticipo_alta_bd = '';
    public string $link_em_cuenta_bancaria_alta_bd = '';
    public int $em_cuenta_bancaria_id = -1;

    public function __construct(PDO $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass()){
        $modelo = new em_empleado(link: $link);
        $html_ = new em_empleado_html(html: $html);
        $obj_link = new links_menu($this->registro_id);
        parent::__construct(html:$html_, link: $link,modelo:  $modelo, obj_link: $obj_link, paths_conf: $paths_conf);

        $this->titulo_lista = 'Empleados';

        $keys_rows_lista = $this->keys_rows_lista();
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al generar keys de lista', data: $keys_rows_lista);
            print_r($error);
            die('Error');
        }
        $this->keys_row_lista = $keys_rows_lista;

        $link_em_anticipo_alta_bd = $obj_link->link_con_id(accion: 'anticipo_alta_bd', registro_id: $this->registro_id,
            seccion: $this->seccion);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al generar link', data: $link_em_anticipo_alta_bd);
            print_r($error);
            die('Error');
        }
        $this->link_em_anticipo_alta_bd = $link_em_anticipo_alta_bd;

        $link_em_cuenta_bancaria_alta_bd = $obj_link->link_con_id(accion: 'cuenta_bancaria_alta_bd', registro_id: $this->registro_id,
            seccion: $this->seccion);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al generar link', data: $link_em_anticipo_alta_bd);
            print_r($error);
            die('Error');
        }
        $this->link_em_cuenta_bancaria_alta_bd = $link_em_cuenta_bancaria_alta_bd;

        $this->asignar_propiedad(identificador:'dp_calle_pertenece_id', propiedades: ["label" => "Calle Pertenece"]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador:'cat_sat_regimen_fiscal_id', propiedades: ["label" => "Regimen Fiscal"]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador:'org_puesto_id', propiedades: ["label" => "Puesto"]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador:'cat_sat_tipo_regimen_nom_id', propiedades: ["label" => "Tipo Regimen"]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador:'im_registro_patronal_id', propiedades: ["label" => "Registro Patronal"]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

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

        $this->asignar_propiedad(identificador:'bn_sucursal_id', propiedades: ["label" => "Sucursal"]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador:'fecha_inicio_rel_laboral',
            propiedades: ["place_holder" => "Fecha Inicio Rel Laboral"]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador:'salario_diario', propiedades: ["place_holder" => "Salario Diario"]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador:'salario_diario_integrado',
            propiedades: ["place_holder" => "Salario Diario Integrado"]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador:'fecha_prestacion',propiedades: ["place_holder" => "Fecha Prestacion"]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador:'num_cuenta', propiedades: ["place_holder" => "Num. Cuenta"]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador:'clabe', propiedades: ["place_holder" => "Clabe"]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }
    }

    public function alta(bool $header, bool $ws = false): array|string
    {
        $r_alta =  parent::alta(header: false);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar template',data:  $r_alta, header: $header,ws:$ws);
        }

        $this->row_upd->fecha_inicio_rel_laboral = date('Y-m-d');
        $this->row_upd->salario_diario = 0;
        $this->row_upd->salario_diario_integrado = 0;

        $inputs = (new em_empleado_html(html: $this->html_base))->genera_inputs(controler: $this,
            keys_selects:  $this->keys_selects);
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al generar inputs',data:  $inputs);
            print_r($error);
            die('Error');
        }
        return $r_alta;
    }

    public function anticipo(bool $header, bool $ws = false): array|stdClass
    {
        $r_alta = parent::alta(header: false, ws: false);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al generar template', data: $r_alta, header: $header, ws: $ws);
        }

        $this->asignar_propiedad(identificador:'em_empleado_id', propiedades: ["id_selected" => $this->registro_id,
            "disabled" => true, "filtro" => array('em_empleado.id' => $this->registro_id)]);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al asignar propiedad',data:  $this->anticipos, header: $header,ws:$ws);
        }

        $this->row_upd->fecha_prestacion = date('Y-m-d');
        $this->row_upd->monto = 0;

        $inputs = (new em_empleado_html(html: $this->html_base))->genera_inputs(controler: $this,
            keys_selects:  $this->keys_selects);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al generar inputs', data: $inputs);
            print_r($error);
            die('Error');
        }

        $this->anticipos = $this->ver_anticipos(header: $header,ws: $ws);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener los anticipos',data:  $this->anticipos, header: $header,ws:$ws);
        }

        return $inputs;
    }

    public function anticipo_alta_bd(bool $header, bool $ws = false): array|stdClass
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
        $_POST['em_empleado_id'] = $this->registro_id;

        $alta = (new em_anticipo($this->link))->alta_registro(registro: $_POST);
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al dar de alta anticipo', data: $alta,
                header: $header, ws: $ws);
        }

        $this->link->commit();

        if ($header) {
            $this->retorno_base(registro_id:$this->registro_id, result: $alta,
                siguiente_view: "anticipo", ws:  $ws);
        }
        if ($ws) {
            header('Content-Type: application/json');
            echo json_encode($alta, JSON_THROW_ON_ERROR);
            exit;
        }
        $alta->siguiente_view = "anticipo";

        return $alta;
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

    private function asigna_link_row(stdClass $row, string $accion, string $propiedad, string $estilo): array|stdClass
    {
        $keys = array('em_empleado_id');
        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $row);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al validar row',data:  $valida);
        }

        $link = $this->obj_link->link_con_id(accion: $accion,registro_id:  $row->em_empleado_id,
            seccion:  $this->tabla);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al genera link',data:  $link);
        }

        $row->$propiedad = $link;
        $row->$estilo = 'info';

        return $row;
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
        $r_modifica =  parent::modifica(header: false,aplica_form:  false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al generar template',data:  $r_modifica);
        }

        $this->asignar_propiedad(identificador:'dp_calle_pertenece_id',
            propiedades: ["id_selected"=>$this->row_upd->dp_calle_pertenece_id]);
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

        $this->asignar_propiedad(identificador:'im_registro_patronal_id',
            propiedades: ["id_selected"=>$this->row_upd->im_registro_patronal_id]);
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

        $inputs = (new em_empleado_html(html: $this->html_base))->genera_inputs(controler: $this,
            keys_selects: $this->keys_selects);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar inputs',data:  $inputs);
        }
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar inputs',data:  $inputs);
        }

        $data = new stdClass();
        $data->template = $r_modifica;
        $data->inputs = $inputs;

        return $data;
    }

    public function calcula_sdi(bool $header, bool $ws = true){
        $em_empleado_id = $_GET['em_empleado_id'];
        $fecha_inicio_rel = $_GET['fecha_inicio_rel_laboral'];
        $salario_diario = $_GET['salario_diario'];

        $result = (new em_empleado($this->link))->calcula_sdi(em_empleado_id: $em_empleado_id,
            fecha_inicio_rel: $fecha_inicio_rel, salario_diario: $salario_diario);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener',data:  $result, header: $header,ws:$ws);
        }

        if($header){
            $retorno = $_SERVER['HTTP_REFERER'];
            header('Location:'.$retorno);
            exit;
        }
        if($ws){
            header('Content-Type: application/json');
            try {
                echo json_encode($result, JSON_THROW_ON_ERROR);
            }
            catch (Throwable $e){
                return $this->retorno_error(mensaje: 'Error al maquetar estados',data:  $e, header: false,ws:$ws);
            }
            exit;
        }

        return $result;
    }

    public function cuenta_bancaria(bool $header, bool $ws = false): array|stdClass
    {
        $r_alta = parent::alta(header: false);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al generar template', data: $r_alta, header: $header, ws: $ws);
        }

        $this->asignar_propiedad(identificador:'em_empleado_id', propiedades: ["id_selected" => $this->registro_id,
            "disabled" => true, "filtro" => array('em_empleado.id' => $this->registro_id)]);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al asignar propiedad',data:  $this->anticipos, header: $header,ws:$ws);
        }

        $inputs = (new em_empleado_html(html: $this->html_base))->genera_inputs(controler: $this,
            keys_selects:  $this->keys_selects);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al generar inputs', data: $inputs);
            print_r($error);
            die('Error');
        }

        $cuentas_bancarias = (new em_cuenta_bancaria($this->link))->get_cuentas_bancarias_empleado(
            em_empleado_id: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener anticipos',data:  $cuentas_bancarias,
                header: $header,ws:$ws);
        }

        foreach ($cuentas_bancarias->registros as $indice => $cuenta_bancaria) {
            $cuenta_bancaria = $this->data_cuenta_bancaria_btn(cuenta_bancaria: $cuenta_bancaria);
            if (errores::$error) {
                return $this->retorno_error(mensaje: 'Error al asignar botones', data: $cuenta_bancaria, header: $header, ws: $ws);
            }
            $cuentas_bancarias->registros[$indice] = $cuenta_bancaria;
        }

        $this->cuentas_bancarias = $cuentas_bancarias;

        return $inputs;
    }

    public function cuenta_bancaria_alta_bd(bool $header, bool $ws = false)
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
        $_POST['em_empleado_id'] = $this->registro_id;

        $alta = (new em_cuenta_bancaria($this->link))->alta_registro(registro: $_POST);
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al dar de alta cuenta bancaria', data: $alta,
                header: $header, ws: $ws);
        }

        $this->link->commit();

        if ($header) {
            $this->retorno_base(registro_id:$this->registro_id, result: $alta,
                siguiente_view: "cuenta_bancaria", ws:  $ws);
        }
        if ($ws) {
            header('Content-Type: application/json');
            echo json_encode($alta, JSON_THROW_ON_ERROR);
            exit;
        }
        $alta->siguiente_view = "cuenta_bancaria";

        return $alta;

    }

    public function cuenta_bancaria_modifica(bool $header, bool $ws = false): array|stdClass
    {
        $controlador = new controlador_em_cuenta_bancaria($this->link);
        $controlador->registro_id = $this->em_cuenta_bancaria_id;

        $r_alta =  $controlador->modifica(header: false);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar template',data:  $r_alta, header: $header,ws:$ws);
        }

        $this->asignar_propiedad(identificador:'bn_sucursal_id',
            propiedades: ["id_selected"=> $controlador->row_upd->bn_sucursal_id]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador:'em_empleado_id', propiedades: ["id_selected" => $controlador->row_upd->em_empleado_id,
            "disabled" => true, "filtro" => array('em_empleado.id' => $controlador->row_upd->em_empleado_id)]);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al asignar propiedad',data:  $this, header: $header,ws:$ws);
        }

        $this->inputs = (new em_cuenta_bancaria_html(html: $this->html_base))->genera_inputs(controler: $controlador,
            keys_selects:  $this->keys_selects);
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al generar inputs',data:  $this->inputs);
            print_r($error);
            die('Error');
        }

        return $this->inputs;
    }


    private function data_anticipo_btn(array $anticipo): array
    {
        $btn_elimina = $this->html_base->button_href(accion: 'elimina_bd', etiqueta: 'Elimina',
            registro_id: $anticipo['em_anticipo_id'], seccion: 'em_anticipo', style: 'danger');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al generar btn', data: $btn_elimina);
        }
        $anticipo['link_elimina'] = $btn_elimina;

        $btn_modifica = $this->html_base->button_href(accion: 'modifica', etiqueta: 'Modifica',
            registro_id: $anticipo['em_anticipo_id'], seccion: 'em_anticipo', style: 'warning');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al generar btn', data: $btn_modifica);
        }
        $anticipo['link_modifica'] = $btn_modifica;

        return $anticipo;
    }

    private function data_cuenta_bancaria_btn(array $cuenta_bancaria): array
    {
        $btn_elimina = $this->html_base->button_href(accion: 'cuenta_bancaria_elimina_bd', etiqueta: 'Elimina',
            registro_id: $cuenta_bancaria['em_cuenta_bancaria_id'], seccion: 'em_empleado', style: 'danger');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al generar btn', data: $btn_elimina);
        }
        $cuenta_bancaria['link_elimina'] = $btn_elimina;

        $btn_modifica = $this->html_base->button_href(accion: 'cuenta_bancaria_modifica', etiqueta: 'Modifica',
            registro_id: $cuenta_bancaria['em_cuenta_bancaria_id'], seccion: 'em_empleado', style: 'warning');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al generar btn', data: $btn_modifica);
        }
        $cuenta_bancaria['link_modifica'] = $btn_modifica;

        return $cuenta_bancaria;
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

    private function keys_rows_lista(): array
    {
        $keys_rows_lista = array();
        $keys = array('em_empleado_id','em_empleado_codigo','em_empleado_nombre','em_empleado_ap','em_empleado_am','em_empleado_rfc');

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

        $campo = str_replace(array("em_empleado", "em_", "_"), '', $campo);
        $campo = ucfirst(strtolower($campo));

        $data->name_lista = $campo;
        $keys_rows_lista[] = $data;

        return $keys_rows_lista;
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

        print_r((new em_anticipo($this->link))->get_saldo_anticipo(8));

        return $r_lista;
    }

    private function maqueta_registros_lista(array $registros): array
    {
        foreach ($registros as $indice=> $row){
            $row = $this->asigna_link_row(row: $row, accion: "anticipo",propiedad: "link_anticipo",
                estilo: "link_anticipo_style");
            if(errores::$error){
                return $this->errores->error(mensaje: 'Error al maquetar row',data:  $row);
            }
            $registros[$indice] = $row;

            $row = $this->asigna_link_row(row: $row, accion: "ver_anticipos",propiedad: "link_ver_anticipos",
                estilo: "link_ver_anticipos_style");
            if(errores::$error){
                return $this->errores->error(mensaje: 'Error al maquetar row',data:  $row);
            }
            $registros[$indice] = $row;

            $row = $this->asigna_link_row(row: $row, accion: "cuenta_bancaria",propiedad: "link_cuenta_bancaria",
                estilo: "link_cuenta_bancaria_style");
            if(errores::$error){
                return $this->errores->error(mensaje: 'Error al maquetar row',data:  $row);
            }
            $registros[$indice] = $row;
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
            $anticipos->registros[$indice] = $anticipo;
        }

        $this->anticipos = $anticipos;

        return $this->anticipos;
    }

}
