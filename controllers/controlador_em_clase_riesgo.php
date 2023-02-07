<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */

namespace gamboamartin\empleado\controllers;

use gamboamartin\empleado\models\em_clase_riesgo;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use html\em_centro_clase_riesgo_html;
use PDO;
use stdClass;

class controlador_em_clase_riesgo extends _ctl_base
{
    public function __construct(PDO      $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass())
    {
        $modelo = new em_clase_riesgo(link: $link);
        $html_ = new em_centro_clase_riesgo_html(html: $html);
        $obj_link = new links_menu(link: $link, registro_id: $this->registro_id);

        $datatables = new stdClass();
        $datatables->columns = array();
        $datatables->columns['em_clase_riesgo_id']['titulo'] = 'Id';
        $datatables->columns['em_clase_riesgo_descripcion']['titulo'] = 'Clase';
        $datatables->columns['em_clase_riesgo_factor']['titulo'] = 'Factor';

        $datatables->filtro = array();
        $datatables->filtro[] = 'em_clase_riesgo.id';
        $datatables->filtro[] = 'em_clase_riesgo.descripcion';
        $datatables->filtro[] = 'em_clase_riesgo.factor';


        parent::__construct(html: $html_, link: $link, modelo: $modelo, obj_link: $obj_link, datatables: $datatables,
            paths_conf: $paths_conf);


        $this->lista_get_data = true;
    }



}
