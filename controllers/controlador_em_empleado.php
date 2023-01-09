<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\empleado\controllers;

use gamboamartin\documento\models\doc_documento;
use gamboamartin\empleado\models\em_abono_anticipo;
use gamboamartin\empleado\models\em_anticipo;
use gamboamartin\empleado\models\em_cuenta_bancaria;
use gamboamartin\errores\errores;
use gamboamartin\system\actions;
use gamboamartin\system\links_menu;
use gamboamartin\system\system;
use gamboamartin\template\html;

use html\em_empleado_html;
use gamboamartin\empleado\models\em_empleado;
use PDO;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use stdClass;
use Throwable;

class controlador_em_empleado extends system {

    public array $keys_selects = array();
    public stdClass $cuentas_bancarias;
    public stdClass $anticipos;
    public stdClass $abonos;
    public string $link_em_anticipo_alta_bd = '';
    public string $link_em_cuenta_bancaria_alta_bd = '';
    public string $link_em_abono_anticipo_alta_bd = '';
    public string $link_em_cuenta_bancaria_modifica_bd = '';
    public string $link_em_anticipo_modifica_bd = '';
    public string $link_em_abono_anticipo_modifica_bd = '';
    public int $em_cuenta_bancaria_id = -1;
    public int $em_anticipo_id = -1;
    public int $em_abono_anticipo_id = -1;
    public controlador_em_cuenta_bancaria $controlador_em_cuenta_bancaria;
    public controlador_em_anticipo $controlador_em_anticipo;
    public controlador_em_abono_anticipo $controlador_em_abono_anticipo;
    public array $columnas_lista_data_table_full = array();
    public array $columnas_lista_data_table_label = array();


    public function __construct(PDO $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass()){
        $modelo = new em_empleado(link: $link);
        $html_ = new em_empleado_html(html: $html);
        $obj_link = new links_menu(link: $link, registro_id: $this->registro_id);

        $columns["em_empleado_id"]["titulo"] = "Id";
        $columns["em_empleado_codigo"]["titulo"] = "Codigo";
        $columns["em_empleado_nombre"]["titulo"] = "Nombre";
        $columns["em_empleado_nombre"]["campos"] = array("em_empleado_ap","em_empleado_am");
        $columns["em_empleado_rfc"]["titulo"] = "Rfc";
        $columns["em_empleado_alias"]["titulo"] = "Rfc";

        $filtro = array("em_empleado.id","em_empleado.nombre","em_empleado.ap","em_empleado.am","em_empleado.rfc",
            "em_empleado_nombre_completo","em_empleado_nombre_completo_inv");

        $datatables = new stdClass();
        $datatables->columns = $columns;

        parent::__construct(html: $html_, link: $link, modelo: $modelo, obj_link: $obj_link, datatables: $datatables,
            paths_conf: $paths_conf);

        $obj_link->genera_links($this);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al inicializar links', data: $obj_link);
            print_r($error);
            die('Error');
        }

        $this->titulo_lista = 'Empleados';

        $this->controlador_em_cuenta_bancaria= new controlador_em_cuenta_bancaria(link:$this->link, paths_conf: $paths_conf);
        $this->controlador_em_anticipo= new controlador_em_anticipo(link:$this->link, paths_conf: $paths_conf);
        $this->controlador_em_abono_anticipo= new controlador_em_abono_anticipo(link:$this->link, paths_conf: $paths_conf);

        $keys_rows_lista = $this->keys_rows_lista();
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al generar keys de lista', data: $keys_rows_lista);
            print_r($error);
            die('Error');
        }
        $this->keys_row_lista = $keys_rows_lista;

        $link_em_anticipo_alta_bd = $obj_link->link_con_id(accion: 'anticipo_alta_bd', link: $link,
            registro_id: $this->registro_id, seccion: $this->seccion);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al generar link', data: $link_em_anticipo_alta_bd);
            print_r($error);
            die('Error');
        }
        $this->link_em_anticipo_alta_bd = $link_em_anticipo_alta_bd;

        $link_em_cuenta_bancaria_alta_bd = $obj_link->link_con_id(accion: 'cuenta_bancaria_alta_bd', link: $link,
            registro_id: $this->registro_id, seccion: $this->seccion);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al generar link', data: $link_em_anticipo_alta_bd);
            print_r($error);
            die('Error');
        }
        $this->link_em_cuenta_bancaria_alta_bd = $link_em_cuenta_bancaria_alta_bd;

        $link_em_abono_anticipo_alta_bd = $obj_link->link_con_id(accion: 'abono_alta_bd', link: $link,
            registro_id: $this->registro_id, seccion: $this->seccion);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al generar link', data: $link_em_abono_anticipo_alta_bd);
            print_r($error);
            die('Error');
        }
        $this->link_em_abono_anticipo_alta_bd = $link_em_abono_anticipo_alta_bd;

        $link_em_cuenta_bancaria_modifica_bd = $obj_link->link_con_id(accion: 'cuenta_bancaria_modifica_bd',
            link: $link, registro_id: $this->registro_id, seccion: $this->seccion);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al generar link', data: $link_em_cuenta_bancaria_modifica_bd);
            print_r($error);
            die('Error');
        }
        $this->link_em_cuenta_bancaria_modifica_bd = $link_em_cuenta_bancaria_modifica_bd;

        $link_em_anticipo_modifica_bd = $obj_link->link_con_id(accion: 'anticipo_modifica_bd', link: $link,
            registro_id: $this->registro_id, seccion: $this->seccion);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al generar link', data: $link_em_anticipo_modifica_bd);
            print_r($error);
            die('Error');
        }
        $this->link_em_anticipo_modifica_bd = $link_em_anticipo_modifica_bd;

        $link_em_abono_anticipo_modifica_bd = $obj_link->link_con_id(accion: 'abono_modifica_bd', link: $link,
            registro_id: $this->registro_id, seccion: $this->seccion);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al generar link', data: $link_em_abono_anticipo_modifica_bd);
            print_r($error);
            die('Error');
        }
        $this->link_em_abono_anticipo_modifica_bd = $link_em_abono_anticipo_modifica_bd;

        if (isset($_GET['em_cuenta_bancaria_id'])){
            $this->em_cuenta_bancaria_id = $_GET['em_cuenta_bancaria_id'];
        }

        if (isset($_GET['em_anticipo_id'])){
            $this->em_anticipo_id = $_GET['em_anticipo_id'];
        }

        if (isset($_GET['em_abono_anticipo_id'])){
            $this->em_abono_anticipo_id = $_GET['em_abono_anticipo_id'];
        }

        $this->asignar_propiedad(identificador: 'direccion_pendiente_pais',
            propiedades: ['place_holder'=> 'Nuevo Pais',"required"=>false]);
        $this->asignar_propiedad(identificador: 'direccion_pendiente_estado',
            propiedades: ['place_holder'=> 'Nuevo Estado',"required"=>false]);
        $this->asignar_propiedad(identificador: 'direccion_pendiente_municipio',
            propiedades: ['place_holder'=> 'Nuevo Municipio',"required"=>false]);
        $this->asignar_propiedad(identificador: 'direccion_pendiente_cp',
            propiedades: ['place_holder'=> 'Nuevo CP',"required"=>false]);
        $this->asignar_propiedad(identificador: 'direccion_pendiente_colonia',
            propiedades: ['place_holder'=> 'Nueva Colonia',"required"=>false]);
        $this->asignar_propiedad(identificador: 'direccion_pendiente_calle_pertenece',
            propiedades: ['place_holder'=> 'Nueva Calle',"required"=>false]);

        $this->asignar_propiedad(identificador:'dp_pais_id', propiedades: ["label" => "Pais","required"=>false,
            "extra_params_keys"=>array("dp_pais_predeterminado")]);
        $this->asignar_propiedad(identificador:'dp_estado_id', propiedades: ["label" => "Estado","required"=>false,
            "con_registros" => false, "extra_params_keys"=>array("dp_estado_predeterminado")]);
        $this->asignar_propiedad(identificador:'dp_municipio_id', propiedades: ["label" => "Municipio","required"=>false,
            "con_registros" => false, "extra_params_keys"=>array("dp_municipio_predeterminado")]);
        $this->asignar_propiedad(identificador:'dp_cp_id', propiedades: ["label" => "CP","con_registros" => false,
            "required"=>false,"extra_params_keys"=>array("dp_cp_predeterminado")]);
        $this->asignar_propiedad(identificador:'dp_colonia_postal_id', propiedades: ["label" => "Colonia",
            "required"=>false,"con_registros" => false, "extra_params_keys"=>array("dp_colonia_predeterminado")]);
        $this->asignar_propiedad(identificador:'dp_calle_pertenece_id', propiedades: ["label" => "Calle Pertenece",
            "required"=>false,"con_registros" => false, "extra_params_keys"=>array("dp_calle_pertenece_predeterminado")]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador:'campo_extra', propiedades: ["cols" => 12,'place_holder'=> 'Campo',
            'required' => false]);

        $this->asignar_propiedad(identificador:'cat_sat_regimen_fiscal_id', propiedades: ["label" => "Regimen Fiscal"]);
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

        $this->asignar_propiedad(identificador:'org_puesto_id', propiedades: ["label" => "Puesto", "required"=>false]);
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

        $this->asignar_propiedad(identificador:'cat_sat_tipo_jornada_nom_id', propiedades: ["label" => "Tipo Jornada",
            "required"=>false]);
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

        $this->asignar_propiedad(identificador:'rfc', propiedades: ["cols" => 6]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador:'curp', propiedades: ["cols" => 6]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        $this->asignar_propiedad(identificador:'nss', propiedades: ["cols" => 6]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
        }

        /**
         * VALIDAR ERRORES
         */
        $this->asignar_propiedad(identificador: 'codigo', propiedades: ['place_holder'=> 'Codigo']);
        $this->asignar_propiedad(identificador: 'nombre', propiedades: ['place_holder'=> 'Nombre']);
        $this->asignar_propiedad(identificador: 'ap', propiedades: ['place_holder'=> 'Apellido Paterno']);
        $this->asignar_propiedad(identificador: 'am', propiedades: ['place_holder'=> 'Apellido Materno']);
        $this->asignar_propiedad(identificador: 'telefono', propiedades: ['place_holder'=> 'Telefono']);
        $this->asignar_propiedad(identificador: 'rfc', propiedades: ['place_holder'=> 'RFC']);
        $this->asignar_propiedad(identificador: 'nss', propiedades: ['place_holder'=> 'NSS']);
        $this->asignar_propiedad(identificador: 'curp', propiedades: ['place_holder'=> 'CURP']);
        $this->asignar_propiedad(identificador: 'fecha_inicio_rel_laboral',
            propiedades: ['place_holder'=> 'Fecha Inicio Relacion Laboral']);


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

        $inputs = $this->genera_inputs(keys_selects:  $this->keys_selects);
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al generar inputs',data:  $inputs);
            print_r($error);
            die('Error');
        }

        return $r_alta;
    }

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

    public function anticipo(bool $header, bool $ws = false): array|stdClass
    {
        $alta = $this->controlador_em_anticipo->alta(header: false);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al generar template', data: $alta, header: $header, ws: $ws);
        }

        $this->controlador_em_anticipo->asignar_propiedad(identificador: 'em_empleado_id',
            propiedades: ["id_selected" => $this->registro_id, "disabled" => true,
                "filtro" => array('em_empleado.id' => $this->registro_id)]);

        $this->inputs = $this->controlador_em_anticipo->genera_inputs(
            keys_selects:  $this->controlador_em_anticipo->keys_selects);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al generar inputs', data: $this->inputs);
            print_r($error);
            die('Error');
        }

        $this->anticipos = $this->ver_anticipos(header: $header,ws: $ws);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener los anticipos',data:  $this->anticipos, header: $header,ws:$ws);
        }

        return $this->inputs;
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

    public function anticipo_modifica(bool $header, bool $ws = false): array|stdClass
    {
        $this->controlador_em_anticipo->registro_id = $this->em_anticipo_id;

        $modifica = $this->controlador_em_anticipo->modifica(header: false);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar template',data:  $modifica, header: $header,ws:$ws);
        }

        $this->inputs = $this->controlador_em_anticipo->genera_inputs(
            keys_selects:  $this->controlador_em_anticipo->keys_selects);
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al generar inputs',data:  $this->inputs);
            print_r($error);
            die('Error');
        }

        return $this->inputs;
    }

    public function anticipo_modifica_bd(bool $header, bool $ws = false): array|stdClass
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

        $r_modifica = (new em_anticipo($this->link))->modifica_bd(registro: $registros,
            id: $this->em_anticipo_id);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al modificar anticipo', data: $r_modifica, header: $header, ws: $ws);
        }

        $this->link->commit();

        if ($header) {
            $this->retorno_base(registro_id:$this->registro_id, result: $r_modifica,
                siguiente_view: "anticipo", ws:  $ws);
        }
        if ($ws) {
            header('Content-Type: application/json');
            echo json_encode($r_modifica, JSON_THROW_ON_ERROR);
            exit;
        }
        $r_modifica->siguiente_view = "anticipo";

        return $r_modifica;
    }

    public function anticipo_elimina_bd(bool $header, bool $ws = false): array|stdClass
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

        $r_elimina = (new em_anticipo($this->link))->elimina_bd(id: $this->em_anticipo_id);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al eliminar otro pago', data: $r_elimina, header: $header,
                ws: $ws);
        }

        $this->link->commit();

        if ($header) {
            $this->retorno_base(registro_id:$this->registro_id, result: $r_elimina,
                siguiente_view: "anticipo", ws:  $ws);
        }
        if ($ws) {
            header('Content-Type: application/json');
            echo json_encode($r_elimina, JSON_THROW_ON_ERROR);
            exit;
        }
        $r_elimina->siguiente_view = "anticipo";

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

    private function asigna_link_row(stdClass $row, string $accion, string $propiedad, string $estilo): array|stdClass
    {
        $keys = array('em_empleado_id');
        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $row);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al validar row',data:  $valida);
        }

        $link = $this->obj_link->link_con_id(accion: $accion, link: $this->link,registro_id:  $row->em_empleado_id,
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
        $alta = $this->controlador_em_cuenta_bancaria->alta(header: false);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al generar template', data: $alta, header: $header, ws: $ws);
        }

        $this->controlador_em_cuenta_bancaria->asignar_propiedad(identificador: 'em_empleado_id',
            propiedades: ["id_selected" => $this->registro_id, "disabled" => true,
                "filtro" => array('em_empleado.id' => $this->registro_id)]);

        $this->inputs = $this->controlador_em_cuenta_bancaria->genera_inputs(
            keys_selects:  $this->controlador_em_cuenta_bancaria->keys_selects);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al generar inputs', data: $this->inputs);
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

        return $this->inputs;
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
        $this->controlador_em_cuenta_bancaria->registro_id = $this->em_cuenta_bancaria_id;

        $modifica = $this->controlador_em_cuenta_bancaria->modifica(header: false);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar template',data:  $modifica, header: $header,ws:$ws);
        }

        $this->inputs = $this->controlador_em_cuenta_bancaria->genera_inputs(
            keys_selects:  $this->controlador_em_cuenta_bancaria->keys_selects);
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al generar inputs',data:  $this->inputs);
            print_r($error);
            die('Error');
        }

        return $this->inputs;
    }

    public function cuenta_bancaria_modifica_bd(bool $header, bool $ws = false): array|stdClass
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

        $r_modifica = (new em_cuenta_bancaria($this->link))->modifica_bd(registro: $registros,
            id: $this->em_cuenta_bancaria_id);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al modificar deduccion', data: $r_modifica, header: $header, ws: $ws);
        }

        $this->link->commit();

        if ($header) {
            $this->retorno_base(registro_id:$this->registro_id, result: $r_modifica,
                siguiente_view: "cuenta_bancaria", ws:  $ws);
        }
        if ($ws) {
            header('Content-Type: application/json');
            echo json_encode($r_modifica, JSON_THROW_ON_ERROR);
            exit;
        }
        $r_modifica->siguiente_view = "cuenta_bancaria";

        return $r_modifica;
    }

    public function cuenta_bancaria_elimina_bd(bool $header, bool $ws = false): array|stdClass
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

        $r_elimina = (new em_cuenta_bancaria($this->link))->elimina_bd(id: $this->em_cuenta_bancaria_id);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al eliminar otro pago', data: $r_elimina, header: $header,
                ws: $ws);
        }

        $this->link->commit();

        if ($header) {
            $this->retorno_base(registro_id:$this->registro_id, result: $r_elimina,
                siguiente_view: "cuenta_bancaria", ws:  $ws);
        }
        if ($ws) {
            header('Content-Type: application/json');
            echo json_encode($r_elimina, JSON_THROW_ON_ERROR);
            exit;
        }
        $r_elimina->siguiente_view = "cuenta_bancaria";

        return $r_elimina;
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

    private function data_cuenta_bancaria_btn(array $cuenta_bancaria): array
    {
        $params['em_cuenta_bancaria_id'] = $cuenta_bancaria['em_cuenta_bancaria_id'];

        $btn_elimina = $this->html_base->button_href(accion: 'cuenta_bancaria_elimina_bd', etiqueta: 'Elimina',
            registro_id: $this->registro_id, seccion: 'em_empleado', style: 'danger',params: $params);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al generar btn', data: $btn_elimina);
        }
        $cuenta_bancaria['link_elimina'] = $btn_elimina;

        $btn_modifica = $this->html_base->button_href(accion: 'cuenta_bancaria_modifica', etiqueta: 'Modifica',
            registro_id: $this->registro_id, seccion: 'em_empleado', style: 'warning',params: $params);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al generar btn', data: $btn_modifica);
        }
        $cuenta_bancaria['link_modifica'] = $btn_modifica;

        return $cuenta_bancaria;
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

        return $r_lista;
    }

    public function lista_ajax(bool $header, bool $ws = false){


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

    public function modifica(bool $header, bool $ws = false): array|stdClass
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

    public function sube_archivo(bool $header, bool $ws = false){
        $r_alta =  parent::alta(header: false,ws:  false); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al generar template',data:  $r_alta);
        }

        return $r_alta;
    }


}
