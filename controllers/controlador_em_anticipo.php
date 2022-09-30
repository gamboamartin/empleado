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
use gamboamartin\errores\errores;
use gamboamartin\system\links_menu;
use gamboamartin\system\system;
use gamboamartin\template\html;
use html\em_anticipo_html;
use PDO;
use stdClass;

class controlador_em_anticipo extends system {

    public array $keys_selects = array();

    public function __construct(PDO $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass()){
        $modelo = new em_anticipo(link: $link);
        $html_ = new em_anticipo_html(html: $html);
        $obj_link = new links_menu($this->registro_id);
        parent::__construct(html:$html_, link: $link,modelo:  $modelo, obj_link: $obj_link, paths_conf: $paths_conf);

        $this->titulo_lista = 'Anticipo';

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

        $this->asignar_propiedad(identificador:'fecha_prestacion', propiedades: ["place_holder" => "Fecha Prestacion"]);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al asignar propiedad', data: $this);
            print_r($error);
            die('Error');
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
        $this->row_upd->monto = 0;

        $inputs = (new em_anticipo_html(html: $this->html_base))->genera_inputs(controler: $this,
            keys_selects:  $this->keys_selects);
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

        $inputs = (new em_anticipo_html(html: $this->html_base))->genera_inputs(controler: $this,
            keys_selects: $this->keys_selects);
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
        $keys = array('em_anticipo_id','em_anticipo_codigo','em_anticipo_descripcion','em_empleado_nombre', 'em_empleado_ap', 'em_empleado_am','em_anticipo_monto','em_anticipo_fecha_prestacion');

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
            $row = $this->asigna_link_row(row: $row, accion: "ver_abonos",propiedad: "link_ver_abonos",
                estilo: "link_ver_abonos_style");
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

    public function ver_abonos(bool $header, bool $ws = false): array|stdClass
    {
        $modifica = $this->modifica(header: false);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar template',data:  $modifica, header: $header,ws:$ws);
        }

        $this->inputs = (new em_anticipo_html(html: $this->html_base))->genera_inputs(controler: $this,
            keys_selects:  $this->keys_selects);
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al generar inputs',data:  $this->inputs);
            print_r($error);
            die('Error');
        }

        return $this->inputs;
    }

}
