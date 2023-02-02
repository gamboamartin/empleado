<?php /** @var  \gamboamartin\empleado\models\em_anticipo $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>

<main class="main section-color-primary">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">
                    <form method="post" action="<?php echo $controlador->link_em_abono_anticipo_reporte_empresa; ?>" class="form-additional">
                        <?php include (new views())->ruta_templates."head/title.php"; ?>
                        <?php include (new views())->ruta_templates."head/subtitulo.php"; ?>
                        <?php include (new views())->ruta_templates."mensajes.php"; ?>

                        <?php echo $controlador->inputs->com_sucursal_id; ?>
                        <?php echo $controlador->inputs->em_tipo_anticipo_id; ?>
                        <?php echo $controlador->inputs->codigo; ?>
                        <?php echo $controlador->inputs->fecha_inicio; ?>
                        <?php echo $controlador->inputs->fecha_final; ?>

                        <div class="botones">
                            <div class="controls">
                                <button type="submit" class="btn btn-success export" name="btn_action_next"
                                        style="border-radius: 5px" value="exportar" form="form_export">
                                    Exportar
                                </button>                            </div>
                        </div>
                    </form>
                    <table id="em_anticipo" class="datatables table table-striped "></table>
                </div>

            </div>

        </div>

</main>
