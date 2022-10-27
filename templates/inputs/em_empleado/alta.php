<?php /** @var  \gamboamartin\empleado\controllers\controlador_em_empleado $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<?php echo $controlador->inputs->codigo; ?>
<?php echo $controlador->inputs->nombre; ?>
<?php echo $controlador->inputs->ap; ?>
<?php echo $controlador->inputs->am; ?>
<?php echo $controlador->inputs->dp_pais_id; ?>
<?php echo $controlador->inputs->dp_estado_id; ?>
<?php echo $controlador->inputs->direccion_pendiente_pais; ?>
<?php echo $controlador->inputs->direccion_pendiente_estado; ?>
<?php echo $controlador->inputs->dp_municipio_id; ?>
<?php echo $controlador->inputs->dp_cp_id; ?>
<?php echo $controlador->inputs->direccion_pendiente_municipio; ?>
<?php echo $controlador->inputs->direccion_pendiente_cp; ?>
<?php echo $controlador->inputs->dp_colonia_postal_id; ?>
<?php echo $controlador->inputs->dp_calle_pertenece_id; ?>
<?php echo $controlador->inputs->direccion_pendiente_colonia; ?>
<?php echo $controlador->inputs->direccion_pendiente_calle_pertenece; ?>
<?php echo $controlador->inputs->cat_sat_regimen_fiscal_id; ?>
<?php echo $controlador->inputs->org_puesto_id; ?>
<?php echo $controlador->inputs->cat_sat_tipo_regimen_nom_id; ?>
<?php echo $controlador->inputs->cat_sat_tipo_jornada_nom_id; ?>
<?php echo $controlador->inputs->telefono; ?>
<?php echo $controlador->inputs->rfc; ?>
<?php echo $controlador->inputs->curp; ?>
<?php echo $controlador->inputs->nss; ?>
<?php echo $controlador->inputs->im_registro_patronal_id; ?>
<?php echo $controlador->inputs->fecha_inicio_rel_laboral; ?>
<?php echo $controlador->inputs->salario_diario; ?>
<?php echo $controlador->inputs->salario_diario_integrado; ?>

<?php include (new views())->ruta_templates.'botons/submit/alta_bd_otro.php';?>

<div class="modal fade" id="campo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Agregar Campo</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <?php echo $controlador->inputs->campo_extra; ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button id="modal-aceptar" type="button" class="btn btn-primary" data-dismiss="modal">Agregar</button>
            </div>
        </div>
    </div>
</div>
