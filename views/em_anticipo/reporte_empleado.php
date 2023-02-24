<?php /** @var  \gamboamartin\empleado\models\em_anticipo $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>

<main class="main section-color-primary">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <?php include (new views())->ruta_templates . "head/lista/title.php"; ?>
                <?php include (new views())->ruta_templates . "mensajes.php"; ?>
                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">
                    <?php include (new views())->ruta_templates . "head/subtitulo.php"; ?>
                    <form method="post" action="<?php echo $controlador->link_em_anticipo_reporte_empleado_exportar; ?> "
                          class="form-additional" id="form_export">

                        <div class="filtros">

                            <div class="filtro-titulo">
                                <h3>Estimado usuario, por favor seleccione una opción de busqueda:</h3>
                            </div>

                            <div class="filtro-categorias">
                                <div>
                                    <label>Por Empleado</label>
                                    <?php echo $controlador->inputs->em_empleado_id; ?>
                                </div>
                                <div>
                                    <label>Por Tipo de Anticipo</label><br>
                                    <?php echo $controlador->inputs->em_tipo_anticipo_id; ?>
                                </div>
                            </div>
                            <div class="filtro-reportes">
                                <div class="filtro-fechas">
                                    <label>Rango Fechas</label>
                                    <div class="fechas form-main widget-form-cart">

                                        <?php echo $controlador->inputs->fecha_inicio; ?>
                                        <?php echo $controlador->inputs->fecha_final; ?>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="botones">
                            <button type="submit" class="btn btn-success export" name="btn_action_next"
                                    style="border-radius: 5px" value="exportar" form="form_export">
                                Exportar
                            </button>
                        </div>
                    </form>
                    <table id="em_anticipo" class="datatables table table-striped "></table>
                </div>

            </div>
        </div>
    </div>
</main>