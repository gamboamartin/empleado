<?php /** @var  \gamboamartin\empleado\controllers\controlador_em_tipo_anticipo $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->descripcion; ?>
<?php echo $controlador->inputs->factor; ?>
<?php include (new views())->ruta_templates.'botons/submit/modifica_bd.php';?>