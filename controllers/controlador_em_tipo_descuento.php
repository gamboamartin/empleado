<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\empleado\controllers;

use gamboamartin\empleado\models\em_tipo_descuento;
use gamboamartin\errores\errores;
use gamboamartin\system\links_menu;
use gamboamartin\system\system;
use gamboamartin\template\html;
use html\em_tipo_anticipo_html;
use PDO;
use stdClass;

class controlador_em_tipo_descuento extends system {

    public array|stdClass $keys_selects = array();
    public function __construct(PDO $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass()){
        $modelo = new em_tipo_descuento(link: $link);
        $html_ = new em_tipo_anticipo_html(html: $html);
        $obj_link = new links_menu(link: $link, registro_id: $this->registro_id);

        $columns["em_tipo_descuento_id"]["titulo"] = "Id";
        $columns["em_tipo_descuento_codigo"]["titulo"] = "Código";
        $columns["em_tipo_descuento_descripcion"]["titulo"] = "Tipo Descuento";
        $columns["em_metodo_calculo_descripcion"]["titulo"] = "Metodo Calculo";
        $columns["em_tipo_descuento_monto"]["titulo"] = "Monto";

        $filtro = array("em_tipo_descuento.id","em_tipo_descuento.codigo","em_tipo_descuento.descripcion");

        $datatables = new stdClass();
        $datatables->columns = $columns;
        $datatables->filtro = $filtro;

        parent::__construct(html:$html_, link: $link,modelo:  $modelo, obj_link: $obj_link,datatables: $datatables,
            paths_conf: $paths_conf);

        $this->titulo_lista = 'Tipo Descuento';
        $propiedades = $this->inicializa_propiedades();
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al inicializar propiedades',data:  $propiedades);
            print_r($error);
            die('Error');
        }

        $this->lista_get_data = true;
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

    private function inicializa_propiedades(): array
    {
        $identificador = "codigo";
        $propiedades = array("place_holder" => "Código", "cols" => 4);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "descripcion";
        $propiedades = array("place_holder" => "Tipo", "cols" => 8);
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        $identificador = "em_metodo_calculo_id";
        $propiedades = array("place_holder" => "Metodo Calculo");
        $this->asignar_propiedad(identificador:$identificador, propiedades: $propiedades);

        return $this->keys_selects;
    }

    public function alta(bool $header, bool $ws = false): array|string
    {
        $r_alta =  parent::alta(header: false);
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


    public function modifica(bool $header, bool $ws = false): array|stdClass
    {
        $r_modifica =  parent::modifica(header: false);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar template',data:  $r_modifica, header: $header,ws:$ws);
        }

        $inputs = $this->genera_inputs(keys_selects:  $this->keys_selects);
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al generar inputs',data:  $inputs);
            print_r($error);
            die('Error');
        }

        return $r_modifica;
    }


}
