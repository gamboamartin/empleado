<?php
namespace tests\templates\directivas;

use gamboamartin\empleado\models\em_clase_riesgo;
use gamboamartin\empleado\models\em_registro_patronal;
use gamboamartin\empleado\test\base_test;
use gamboamartin\errores\errores;
use gamboamartin\test\test;
use stdClass;


class em_registro_patronalTest extends test {
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



        $modelo = new em_registro_patronal($this->link);
        //$modelo = new liberator($modelo);

        $del = (new base_test())->del_cat_sat_isn(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar',$del);
            print_r($error);
            exit;
        }

        $alta = (new base_test())->alta_cat_sat_isn(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al insertar',$alta);
            print_r($error);
            exit;
        }


        $modelo->registro = array();
        $modelo->registro['fc_csd_id'] = 1;
        $modelo->registro['em_clase_riesgo_id'] = 1;
        $modelo->registro['cat_sat_isn_id'] = 1;
        $modelo->registro['descripcion'] = 1;

        $resultado = $modelo->alta_bd();

        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(' 1 0.01', $resultado->registro['em_clase_riesgo_codigo']);
        $this->assertEquals('1 0.01 0.01', $resultado->registro['em_clase_riesgo_descripcion_select']);

        errores::$error = false;

    }

    public function test_modifica_bd(): void
    {
        errores::$error = false;


        $modelo = new em_registro_patronal($this->link);
        //$modelo = new liberator($modelo);

        $del = (new base_test())->del_em_registro_patronal(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar',$del);
            print_r($error);
            exit;
        }

        $alta = (new base_test())->alta_em_registro_patronal(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al insertar', $alta);
            print_r($error);
            exit;
        }

        $registro = array();
        $registro['descripcion'] = 'a';
        $id = 1;

        $resultado = $modelo->modifica_bd($registro, $id);

        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("Exito al ejecutar sql del modelo em_registro_patronal transaccion UPDATE", $resultado->mensaje);
        errores::$error = false;
    }

}

