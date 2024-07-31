<?php /** @var  gamboamartin\inmuebles\controllers\controlador_inm_prospecto $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>

<main class="main section-color-primary">
    <div class="container">

        <div class="row">

            <div class="col-lg-12">

                <div class="widget" >

                    <?php include (new views())->ruta_templates."head/title.php"; ?>
                    <?php include (new views())->ruta_templates."head/subtitulo.php"; ?>
                    <?php include (new views())->ruta_templates."mensajes.php"; ?>

                    <?php echo $controlador->inputs->nombre; ?>
                    <?php echo $controlador->inputs->ap; ?>
                    <?php echo $controlador->inputs->am; ?>
                    <?php echo $controlador->inputs->telefono; ?>
                    <?php echo $controlador->inputs->correo; ?>
                    <?php echo $controlador->inputs->rfc; ?>
                    <?php echo $controlador->inputs->nss; ?>
                    <?php echo $controlador->inputs->curp; ?>

                </div>
            </div>

        </div>
    </div>
    <br>

    <div class="container">
        <div class="row">
            <div class="col-lg-12 table-responsive">
                <table id="table-em_empleado" class="table mb-0 table-striped table-sm "></table>
            </div>
        </div>
    </div>
</main>

<dialog id="myModal">
    <span class="close-btn" id="closeModalBtn">&times;</span>
    <h2>Vista Previa</h2>
    <div class="content">
    </div>
</dialog>