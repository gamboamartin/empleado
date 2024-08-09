<?php
namespace tests\templates\directivas;

use gamboamartin\empleado\models\em_cuenta_bancaria;
use gamboamartin\empleado\models\em_empleado;
use gamboamartin\empleado\test\base_test;
use gamboamartin\errores\errores;
use gamboamartin\test\liberator;
use gamboamartin\test\test;
use stdClass;


class em_cuenta_bancariaTest extends test {
    public errores $errores;
    private stdClass $paths_conf;
    public function __construct(?string $name = null)
    {
        parent::__construct($name);
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


    public function test_registro(): void
    {
        errores::$error = false;


        $modelo = new em_cuenta_bancaria($this->link);
        //$modelo = new liberator($modelo);

        $del = (new base_test())->del_em_cuenta_bancaria($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar', $del);
            print_r($error);
            exit;
        }


        $registro_id = 1;
        $resultado = $modelo->registro(registro_id: $registro_id);
        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertStringContainsStringIgnoringCase('Error al obtener registro', $resultado['mensaje']);

        errores::$error = false;

        $del = (new base_test())->del_em_empleado($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar', $del);
            print_r($error);
            exit;
        }

        $del = (new \gamboamartin\banco\tests\base_test())->del_bn_sucursal(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar', $del);
            print_r($error);
            exit;
        }

        $alta = (new base_test())->alta_em_empleado($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al insertar', $alta);
            print_r($error);
            exit;
        }

        $alta = (new \gamboamartin\banco\tests\base_test())->alta_bn_sucursal(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al insertar', $alta);
            print_r($error);
            exit;
        }

        $alta = (new base_test())->alta_em_cuenta_bancaria($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al insertar', $alta);
            print_r($error);
            exit;
        }


        $registro_id = 1;
        $resultado = $modelo->registro(registro_id: $registro_id);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('1', $resultado['em_cuenta_bancaria_id']);
        $this->assertEquals('1', $resultado['em_cuenta_bancaria_descripcion']);
        $this->assertEquals('SUC', $resultado['em_cuenta_bancaria_codigo']);
        $this->assertEquals('1', $resultado['em_empleado_id']);
        $this->assertEquals('001', $resultado['bn_sucursal_codigo']);
        errores::$error = false;
    }

}

