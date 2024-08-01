<?php /** @var  gamboamartin\inmuebles\controllers\controlador_inm_doc_comprador $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>

<main class="main section-color-primary">
    <div class="container">

        <div class="row ">

            <!--<div class="col-lg-12">

                <div class="widget" >

                    <?php /*include (new views())->ruta_templates."head/title.php"; */?>
                    <?php /*include (new views())->ruta_templates."head/subtitulo.php"; */?>
                    <?php /*include (new views())->ruta_templates."mensajes.php"; */?>


                    <?php /*echo $controlador->inputs->com_tipo_prospecto_descripcion; */?>
                    <?php /*echo $controlador->inputs->nss; */?>
                    <?php /*echo $controlador->inputs->curp; */?>
                    <?php /*echo $controlador->inputs->rfc; */?>
                    <?php /*echo $controlador->inputs->apellido_paterno; */?>
                    <?php /*echo $controlador->inputs->apellido_materno; */?>
                    <?php /*echo $controlador->inputs->nombre; */?>
                    <div class="control-group col-sm-12" style="margin-top: 10px;">
                        <div class="controls">
                            <?php /*echo $controlador->button_inm_doc_prospecto_descarga; */?>
                            <br>
                        </div>
                    </div>

                </div>
            </div>-->

            <div class="col-lg-12 ">
                <div class="row">
                    <br>
                    <div class="col-md-12">
                        <iframe class="col-md-12 view" height="600px" src="<?php echo $controlador->ruta_doc; ?>"></iframe>
                    </div>
                </div>
                <br>
            </div>

        </div>
    </div>
</main>


















