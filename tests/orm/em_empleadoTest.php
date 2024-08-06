<?php
namespace tests\templates\directivas;

use gamboamartin\empleado\models\em_empleado;
use gamboamartin\errores\errores;
use gamboamartin\test\liberator;
use gamboamartin\test\test;
use stdClass;
use tests\base_test;


class em_empleadoTest extends test {
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
    }

    /**
     * rEVISAR ELEMENTOS EM
    public function test_am(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $modelo = new em_empleado($this->link);
        $modelo = new liberator($modelo);

        $registro = array();
        $resultado = $modelo->am($registro);


        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("", $resultado['am']);

        errores::$error = false;
    }
     * */

    public function test_cat_sat_tipo_jornada_nom_id(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $modelo = new em_empleado($this->link);
        $modelo = new liberator($modelo);


        $registro = array();
        $resultado = $modelo->cat_sat_tipo_jornada_nom_id($registro);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(99, $resultado['cat_sat_tipo_jornada_nom_id']);
        errores::$error = false;
    }

    public function test_filtro_and(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';
        
        

        $modelo = new em_empleado($this->link);
        //$modelo = new liberator($modelo);

        $del = (new \gamboamartin\empleado\test\base_test())->del_com_cliente($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar', $del);
            print_r($error);
            exit;
        }


        $filtro_especial[0]['em_empleado_nombre_completo']['operador'] = 'LIKE';
        $filtro_especial[0]['em_empleado_nombre_completo']['valor'] = addslashes("%Z%");
        $filtro_especial[0]['em_empleado_nombre_completo']['comparacion'] = "OR";
        $resultado = $modelo->filtro_and(filtro_especial: $filtro_especial);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEmpty( $resultado->registros);

        errores::$error = false;

        $del = (new \gamboamartin\empleado\test\base_test())->del_em_empleado($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar', $del);
            print_r($error);
            exit;
        }
        $alta = (new \gamboamartin\empleado\test\base_test())->alta_em_empleado(link: $this->link, am: 'AM', ap: 'AP', nombre: 'NOMBRE');
        if(errores::$error){
            $error = (new errores())->error('Error al insertar', $alta);
            print_r($error);
            exit;
        }
        $filtro_especial[0]['em_empleado_nombre_completo']['operador'] = 'LIKE';
        $filtro_especial[0]['em_empleado_nombre_completo']['valor'] = addslashes("%A%");
        $filtro_especial[0]['em_empleado_nombre_completo']['comparacion'] = "OR";
        $resultado = $modelo->filtro_and(filtro_especial: $filtro_especial);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertCount(1, $resultado->registros);
        errores::$error = false;
    }

    /**
     * Verificar usi

    public function test_limpia_campos(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $modelo = new em_empleado($this->link);
       // $modelo = new liberator($modelo);


        $registro = array('a');
        $campos_limpiar = array(0);
        $resultado = $modelo->limpia_campos($registro, $campos_limpiar);
        print_r($resultado);exit;

        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEmpty($resultado);

        errores::$error = false;



    }
     * */

    public function test_registro(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'cat_sat_tipo_persona';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 2;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $modelo = new em_empleado($this->link);
        //$modelo = new liberator($modelo);

        $del = (new \gamboamartin\empleado\test\base_test())->del_em_empleado($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar', $del);
            print_r($error);
            exit;
        }

        $del = (new \gamboamartin\empleado\test\base_test())->del_com_cliente($this->link);
        if(errores::$error){
            $error = (new errores())->error('Error al eliminar', $del);
            print_r($error);
            exit;
        }


        $resultado = $modelo->registro(registro_id: 1);
        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertStringContainsStringIgnoringCase('Error al obtener registro', $resultado['mensaje']);

        errores::$error = false;



        $alta = (new \gamboamartin\empleado\test\base_test())->alta_em_empleado(link: $this->link, am: 'AM', ap: 'AP', nombre: 'NOMBRE');
        if(errores::$error){
            $error = (new errores())->error('Error al insertar', $alta);
            print_r($error);
            exit;
        }

        $resultado = $modelo->registro(registro_id: 1,columnas: array('em_empleado_nombre_completo'));
        //print_r($resultado);exit;
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('NOMBRE AP AM', $resultado['em_empleado_nombre_completo']);

        errores::$error = false;
    }

    






}

