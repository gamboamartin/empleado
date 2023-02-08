<?php
namespace tests\templates\directivas;

use gamboamartin\empleado\models\em_abono_anticipo;
use gamboamartin\empleado\models\em_anticipo;
use gamboamartin\empleado\models\em_empleado;
use gamboamartin\empleado\test\base_test;
use gamboamartin\errores\errores;
use gamboamartin\test\test;
use stdClass;


class em_anticipoTest extends test {
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
    }

    public function test_get_anticipos_empleado(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $modelo = new em_anticipo($this->link);
        //$modelo = new liberator($modelo);


        $em_empleado_id = 1;
        $resultado = $modelo->get_anticipos_empleado($em_empleado_id);


        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('activo', $resultado->registros[0]['em_anticipo_tiene_saldo']);

        errores::$error = false;


        errores::$error = false;
    }

    public function test_get_total_abonado(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $modelo = new em_abono_anticipo($this->link);
        //$modelo = new liberator($modelo);

        $del = (new base_test())->del_em_tipo_anticipo($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar', $del);
            print_r($error);
            exit;
        }


        $del = (new base_test())->del_org_clasificacion_dep($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar', $del);
            print_r($error);
            exit;
        }

        $del = (new base_test())->del_com_cliente($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar', $del);
            print_r($error);
            exit;
        }

        $em_anticipo_id = 1;
        $resultado = $modelo->get_total_abonado($em_anticipo_id);
        $this->assertIsFloat($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(0, $resultado);

        errores::$error = false;

        $alta = (new base_test())->alta_em_abono_anticipo($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al insertar', $alta);
            print_r($error);
            exit;
        }

        $em_anticipo_id = 1;
        $resultado = $modelo->get_total_abonado($em_anticipo_id);
        $this->assertIsFloat($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(50, $resultado);

        errores::$error = false;
    }




}

