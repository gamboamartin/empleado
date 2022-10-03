<?php
namespace gamboamartin\empleado\test;
use base\orm\modelo_base;
use gamboamartin\errores\errores;
use gamboamartin\empleado\models\em_empleado;
use PDO;

class base_test{





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




    public function del(PDO $link, string $name_model): array
    {
        $model = (new modelo_base($link))->genera_modelo(modelo: $name_model);
        $del = $model->elimina_todo();
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al eliminar '.$name_model, data: $del);
        }
        return $del;
    }

    public function del_em_empleado(PDO $link): array
    {
        $del = $this->del($link, 'gamboamartin\\empleado\\models\\em_empleado');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

}
