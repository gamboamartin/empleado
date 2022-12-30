<?php /** @var  \gamboamartin\empleado\controllers\controlador_em_tipo_descuento $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->codigo; ?>
<?php echo $controlador->inputs->descripcion; ?>
<?php echo $controlador->inputs->em_metodo_calculo_id; ?>
<?php echo $controlador->inputs->monto; ?>
<?php include (new views())->ruta_templates.'botons/submit/alta_bd_otro.php';?>