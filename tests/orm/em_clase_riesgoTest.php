<?php
namespace tests\templates\directivas;

use gamboamartin\empleado\models\em_clase_riesgo;
use gamboamartin\empleado\test\base_test;
use gamboamartin\errores\errores;
use gamboamartin\test\test;
use stdClass;


class em_clase_riesgoTest extends test {
    public errores $errores;
    private stdClass $paths_conf;
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->errores = new errores();
        $this->paths_conf = new stdClass();
        $this->paths_conf->generales = '/var/www/html/cat_sat/config/generales.php';
        $this->paths_conf->database = '/var/www/html/cat_sat/config/database.php';
        $this->paths_conf->views = '/var/www/html/cat_sat/config/views.php';

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

    }


    public function test_alta_bd(): void
    {
        errores::$error = false;



        $modelo = new em_clase_riesgo($this->link);
        //$modelo = new liberator($modelo);

        $del = (new base_test())->del_em_clase_riesgo(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar', $del);
            print_r($error);
            exit;
        }

        $modelo->registro = array();
        $modelo->registro['descripcion'] = 'a';
        $modelo->registro['factor'] = 'a';
        $resultado = $modelo->alta_bd();
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('a a', $resultado->registro['em_clase_riesgo_codigo']);
        $this->assertEquals('A A', $resultado->registro['em_clase_riesgo_descripcion_select']);

        errores::$error = false;

    }

}

