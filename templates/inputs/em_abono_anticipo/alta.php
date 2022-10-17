<?php /** @var  \gamboamartin\empleado\controllers\controlador_em_abono_anticipo $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->codigo; ?>
<?php echo $controlador->inputs->em_anticipo_id; ?>
<?php echo $controlador->inputs->num_pago; ?>
<?php echo $controlador->inputs->em_tipo_abono_anticipo_id; ?>
<?php echo $controlador->inputs->descripcion; ?>
<?php echo $controlador->inputs->cat_sat_forma_pago_id; ?>
<?php echo $controlador->inputs->monto; ?>
<?php echo $controlador->inputs->fecha; ?>
<?php include (new views())->ruta_templates.'botons/submit/alta_bd_otro.php';?>