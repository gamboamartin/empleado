<?php /** @var \gamboamartin\empleado\controllers\controlador_em_empleado_documento $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->doc_documento_id; ?>
<?php echo $controlador->inputs->em_empleado_id; ?>
<?php include (new views())->ruta_templates.'botons/submit/modifica_bd.php';?>