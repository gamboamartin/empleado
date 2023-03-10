<?php /** @var  \gamboamartin\empleado\controllers\controlador_em_anticipo $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->em_empleado_id; ?>
<?php echo $controlador->inputs->em_tipo_anticipo_id; ?>
<?php echo $controlador->inputs->em_tipo_descuento_id; ?>
<?php echo $controlador->inputs->descripcion; ?>
<?php echo $controlador->inputs->monto; ?>
<?php echo $controlador->inputs->n_pagos; ?>
<?php echo $controlador->inputs->fecha_prestacion; ?>
<?php echo $controlador->inputs->fecha_inicio_descuento; ?>
<?php echo $controlador->inputs->comentarios; ?>
<?php include (new views())->ruta_templates.'botons/submit/alta_bd.php';?>