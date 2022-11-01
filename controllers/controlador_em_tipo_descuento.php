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

    public function __construct(PDO $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass()){
        $modelo = new em_tipo_descuento(link: $link);
        $html_ = new em_tipo_anticipo_html(html: $html);
        $obj_link = new links_menu(link: $link, registro_id: $this->registro_id);
        parent::__construct(html:$html_, link: $link,modelo:  $modelo, obj_link: $obj_link, paths_conf: $paths_conf);

        $this->titulo_lista = 'Tipo Descuento';
    }

    public function alta(bool $header, bool $ws = false): array|string
    {
        $r_alta =  parent::alta(header: false);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar template',data:  $r_alta, header: $header,ws:$ws);
        }

        $this->row_upd->monto = 0;

        $inputs = $this->genera_inputs();
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

        $inputs = $this->genera_inputs();
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar inputs',data:  $inputs);
        }

        $data = new stdClass();
        $data->template = $r_modifica;
        $data->inputs = $inputs;

        return $data;
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

}
