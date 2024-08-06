<?php /** @var  \gamboamartin\empleado\controllers\controlador_em_empleado $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->nombre; ?>
<?php echo $controlador->inputs->telefono; ?>
<?php echo $controlador->inputs->ap; ?>
<?php echo $controlador->inputs->am; ?>
<?php echo $controlador->inputs->dp_pais_id; ?>
<?php echo $controlador->inputs->dp_estado_id; ?>
<?php echo $controlador->inputs->dp_municipio_id; ?>
<?php echo $controlador->inputs->cp; ?>
<?php echo $controlador->inputs->colonia; ?>
<?php echo $controlador->inputs->calle; ?>
<?php echo $controlador->inputs->numero_exterior; ?>
<?php echo $controlador->inputs->numero_interior; ?>
<?php echo $controlador->inputs->cat_sat_regimen_fiscal_id; ?>
<?php echo $controlador->inputs->org_puesto_id; ?>
<?php echo $controlador->inputs->cat_sat_tipo_regimen_nom_id; ?>
<?php echo $controlador->inputs->cat_sat_tipo_jornada_nom_id; ?>
<?php echo $controlador->inputs->em_empleado_rfc; ?>
<?php echo $controlador->inputs->em_empleado_nss; ?>
<?php echo $controlador->inputs->em_empleado_curp; ?>
<?php echo $controlador->inputs->em_registro_patronal_id; ?>
<?php echo $controlador->inputs->em_centro_costo_id; ?>
<?php echo $controlador->inputs->salario_diario; ?>
<?php echo $controlador->inputs->salario_diario_integrado; ?>
<?php echo $controlador->inputs->salario_total; ?>
<?php include (new views())->ruta_templates.'botons/submit/alta_bd.php';?>

