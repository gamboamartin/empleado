<?php /** @var  \gamboamartin\empleado\controllers\controlador_em_empleado_usuario $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->em_empleado_id; ?>
<?php echo $controlador->inputs->codigo; ?>
<?php include (new views())->ruta_templates.'botons/submit/alta_bd.php';?>