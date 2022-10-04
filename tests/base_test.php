<?php
namespace gamboamartin\empleado\test;
use base\orm\modelo_base;
use gamboamartin\empleado\models\em_abono_anticipo;
use gamboamartin\empleado\models\em_anticipo;
use gamboamartin\empleado\models\em_tipo_abono_anticipo;
use gamboamartin\empleado\models\em_tipo_anticipo;
use gamboamartin\errores\errores;
use gamboamartin\empleado\models\em_empleado;
use PDO;

class base_test{


    public function alta_em_abono_anticipo(PDO $link): array|\stdClass
    {

        $alta = $this->alta_em_anticipo($link);
        if(errores::$error){
            return (new errores())->error('Error al dar de alta ', $alta);

        }

        $alta = $this->alta_em_tipo_abono_anticipo($link);
        if(errores::$error){
            return (new errores())->error('Error al dar de alta ', $alta);

        }

        $registro = array();
        $registro['id'] = 1;
        $registro['codigo'] = 1;
        $registro['descripcion'] = 1;
        $registro['em_anticipo_id'] = 1;
        $registro['monto'] = 50;
        $registro['em_tipo_abono_anticipo_id'] = 1;
        $registro['cat_sat_forma_pago_id'] = 1;
        $registro['fecha'] = '2020-01-01';


        $alta = (new em_abono_anticipo($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error('Error al dar de alta ', $alta);

        }
        return $alta;
    }

    public function alta_em_anticipo(PDO $link): array|\stdClass
    {

        $alta = $this->alta_em_tipo_anticipo($link);
        if(errores::$error){
            return (new errores())->error('Error al dar de alta ', $alta);

        }

        $registro = array();
        $registro['id'] = 1;
        $registro['codigo'] = 1;
        $registro['descripcion'] = 1;
        $registro['em_tipo_anticipo_id'] = 1;
        $registro['em_empleado_id'] = 1;
        $registro['monto'] = 100;
        $registro['fecha_prestacion'] = '2020-01-01';


        $alta = (new em_anticipo($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error('Error al dar de alta ', $alta);

        }
        return $alta;
    }


    public function alta_em_empleado(PDO $link): array|\stdClass
    {
        $registro = array();
        $registro['id'] = 1;
        $registro['codigo'] = 1;
        $registro['descripcion'] = 1;
        $registro['nombre'] = 1;
        $registro['ap'] = 1;
        $registro['org_puesto_id'] = 1;
        $registro['dp_calle_pertenece_id'] = 1;



        $alta = (new em_empleado($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error('Error al dar de alta ', $alta);

        }
        return $alta;
    }

    public function alta_em_tipo_abono_anticipo(PDO $link): array|\stdClass
    {


        $registro = array();
        $registro['id'] = 1;
        $registro['codigo'] = 1;
        $registro['descripcion'] = 1;
        $registro['descripcion_select'] = 1;
        $registro['codigo_bis'] = 1;
        $registro['alias'] = 1;



        $alta = (new em_tipo_abono_anticipo($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error('Error al dar de alta ', $alta);

        }
        return $alta;
    }

    public function alta_em_tipo_anticipo(PDO $link): array|\stdClass
    {
        $registro = array();
        $registro['id'] = 1;
        $registro['codigo'] = 1;
        $registro['descripcion'] = 1;
        $registro['descripcion_select'] = 1;
        $registro['codigo_bis'] = 1;
        $registro['alias'] = 1;




        $alta = (new em_tipo_anticipo($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error('Error al dar de alta ', $alta);

        }
        return $alta;
    }




    public function del(PDO $link, string $name_model): array
    {
        $model = (new modelo_base($link))->genera_modelo(modelo: $name_model);
        $del = $model->elimina_todo();
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al eliminar '.$name_model, data: $del);
        }
        return $del;
    }

    public function del_em_abono_anticipo(PDO $link): array
    {

        $del = $this->del($link, 'gamboamartin\\empleado\\models\\em_abono_anticipo');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_em_anticipo(PDO $link): array
    {

        $del = $this->del($link, 'gamboamartin\\empleado\\models\\em_anticipo');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_em_cuenta_bancaria(PDO $link): array
    {

        $del = $this->del($link, 'gamboamartin\\empleado\\models\\em_cuenta_bancaria');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_em_empleado(PDO $link): array
    {

        $del = $this->del_em_cuenta_bancaria($link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }

        $del = $this->del($link, 'gamboamartin\\empleado\\models\\em_empleado');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }
    public function del_em_tipo_abono_anticipo(PDO $link): array
    {


        $del = $this->del($link, 'gamboamartin\\empleado\\models\\em_tipo_abono_anticipo');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_em_tipo_anticipo(PDO $link): array
    {

        $del = $this->del_em_anticipo($link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }

        $del = $this->del($link, 'gamboamartin\\empleado\\models\\em_tipo_anticipo');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

}
