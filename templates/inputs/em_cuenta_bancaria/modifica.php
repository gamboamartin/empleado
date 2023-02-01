<?php /** @var  \gamboamartin\empleado\controllers\controlador_em_cuenta_bancaria $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->em_empleado_id; ?>
<?php echo $controlador->inputs->bn_sucursal_id; ?>
<?php echo $controlador->inputs->num_cuenta; ?>
<?php echo $controlador->inputs->clabe; ?>
<?php echo $controlador->inputs->descripcion; ?>
<?php include (new views())->ruta_templates.'botons/submit/modifica_bd.php';?>
<div class="col-row-12">
    <?php foreach ($controlador->buttons as $button){ ?>
        <?php echo $button; ?>
    <?php }?>
</div>