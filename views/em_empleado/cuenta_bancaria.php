<?php /** @var \gamboamartin\empleado\controllers\controlador_em_empleado $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>
<main class="main section-color-primary">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <?php include (new views())->ruta_templates."head/title.php"; ?>

                <?php include (new views())->ruta_templates."mensajes.php"; ?>

                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">
                    <form method="post" action="<?php echo $controlador->link_em_cuenta_bancaria_alta_bd; ?>" class="form-additional">
                        <?php include (new views())->ruta_templates."head/subtitulo.php"; ?>

                        <?php echo $controlador->inputs->em_empleado_id; ?>
                        <?php echo $controlador->inputs->bn_sucursal_id; ?>
                        <?php echo $controlador->inputs->num_cuenta; ?>
                        <?php echo $controlador->inputs->clabe; ?>
                        <?php echo $controlador->inputs->descripcion; ?>

                        <?php echo $controlador->inputs->hidden_row_id; ?>
                        <?php echo $controlador->inputs->hidden_seccion_retorno; ?>
                        <?php echo $controlador->inputs->hidden_id_retorno; ?>
                        <div class="controls">
                            <button type="submit" class="btn btn-success" value="cuenta_bancaria" name="btn_action_next">Alta</button><br>
                        </div>
                        <div class="error"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<main class="main section-color-primary">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="widget widget-box box-container widget-mylistings">
                    <?php echo $controlador->contenido_table; ?>
                </div> <!-- /. widget-table-->
            </div><!-- /.center-content -->
        </div>
    </div>
</main>

