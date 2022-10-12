<?php /** @var gamboamartin\empleado\controllers\controlador_em_empleado $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>


<main class="main section-color-primary">
    <div class="container">

        <div class="row">
            <div class="col-md-12">

                <div class="widget widget-box">

                    <div class="">
                        <div class="widget-header">
                            <h2>Abonos</h2>
                        </div>

                        <table class="table table-striped test">
                            <thead>
                            <tr>
                                <?php
                                foreach ($controlador->columnas_lista_data_table_label as $columna){ ?>
                                    <th><?php echo $columna; ?></th>
                                <?php } ?>

                            </tr>
                            </thead>
                            <tbody>


                            </tbody>
                        </table>

                    </div>
                </div> <!-- /. widget-table-->
            </div><!-- /.center-content -->
        </div>

    </div>

</main>







