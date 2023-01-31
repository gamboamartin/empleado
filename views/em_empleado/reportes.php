<?php /** @var \gamboamartin\empleado\models\em_empleado $controlador controlador en ejecucion */ ?>
<?php use config\views; ?>

<main class="main section-color-primary">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <?php include (new views())->ruta_templates . "head/lista/title.php"; ?>
                <?php include (new views())->ruta_templates . "mensajes.php"; ?>
                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">
                    <?php include (new views())->ruta_templates . "head/subtitulo.php"; ?>
                    <div class="filtros">

                        <div class="filtro-titulo">
                            <h3>Estimado usuario, por favor seleccione una opción de busqueda:</h3>
                        </div>

                        <div class="filtro-categorias">
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
                        <a href="<?php echo $controlador->link_em_empleado_exportar; ?>" class="btn btn-success export" >Exportar</a>
                    </div>
                    <table id="em_empleado" class="datatables table table-striped "></table>
                </div>

            </div>
        </div>
    </div>
</main>







