<?php /** @var \gamboamartin\empleado\models\em_empleado $controlador controlador en ejecucion */ ?>
<?php use config\views; ?>

<main class="main section-color-primary">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <?php include (new views())->ruta_templates . "head/lista/title.php"; ?>
                <?php include (new views())->ruta_templates . "mensajes.php"; ?>
                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">
                    <div class="table-head" style="display: flex; justify-content: space-between; align-items: center ">
                        <?php include (new views())->ruta_templates . "head/subtitulo.php"; ?>
                        <div class="botones" style="display: flex; justify-content: space-around; align-items: center">
                            <form method="post" action="<?php echo $controlador->link_em_empleado_exportar; ?> "
                                  class="form-additional" id="form_export">
                                <?php echo $controlador->inputs->filtro_fecha_inicio; ?>
                                <?php echo $controlador->inputs->filtro_fecha_final; ?>
                            </form>
                            <button type="submit" class="btn btn-success" name="btn_action_next"
                                    style="border-radius: 5px" value="exportar" form="form_export">
                                Exportar
                            </button>
                        </div>
                    </div>
                    <table id="em_empleado" class="table table-striped "></table>
                </div>

            </div>
        </div>
    </div>
</main>







