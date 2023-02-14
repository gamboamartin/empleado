<?php /** @var \gamboamartin\im_registro_patronal\controllers\controlador_im_registro_patronal $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->fc_csd_id; ?>
<?php echo $controlador->inputs->em_clase_riesgo_id; ?>
<?php echo $controlador->inputs->cat_sat_isn_id; ?>
<?php echo $controlador->inputs->descripcion;?>
<?php include (new views())->ruta_templates.'botons/submit/modifica_bd.php';?>
