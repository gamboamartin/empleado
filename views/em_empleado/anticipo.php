<?php /** @var \gamboamartin\empleado\models\em_empleado $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>

<main class="main section-color-primary">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">
                    <?php include (new views())->ruta_templates."head/title.php"; ?>
                    <?php include (new views())->ruta_templates."head/subtitulo.php"; ?>
                    <?php include (new views())->ruta_templates."mensajes.php"; ?>
                    <form method="post" action="<?php echo $controlador->link_em_anticipo_alta_bd; ?>" class="form-additional">
                        <?php echo $controlador->inputs->codigo; ?>
                        <?php echo $controlador->inputs->em_empleado_id; ?>
                        <?php echo $controlador->inputs->descripcion; ?>
                        <?php echo $controlador->inputs->em_tipo_anticipo_id; ?>
                        <?php echo $controlador->inputs->em_tipo_descuento_id; ?>
                        <?php echo $controlador->inputs->monto; ?>
                        <?php echo $controlador->inputs->fecha_prestacion; ?>
                        <?php echo $controlador->inputs->fecha_inicio_descuento; ?>
                        <div class="control-group btn-alta">
                            <div class="controls">
                                <button type="submit" class="btn btn-success" value="genera_anticipo" name="btn_action_next">Alta</button><br>
                            </div>
                        </div>
                    </form>
                </div>

            </div>

        </div>

        <div class="row">
            <div class="col-md-12">

                <div class="widget widget-box box-container widget-mylistings">

                    <div class="">
                        <div class="widget-header">
                            <h2>Anticipos</h2>
                        </div>

                        <table class="table table-striped footable-sort" data-sorting="true">
                            <thead>
                            <tr>
                                <th data-breakpoints="xs sm md" data-type="html">Id</th>
                                <th data-breakpoints="xs sm md" data-type="html">Descripcion</th>
                                <th data-breakpoints="xs sm md" data-type="html">Monto</th>
                                <th data-breakpoints="xs sm md" data-type="html">Fecha</th>
                                <th data-breakpoints="xs sm md" data-type="html">Saldo Pendiente</th>
                                <th data-breakpoints="xs sm md" data-type="html">Total Abonado</th>

                                <th data-breakpoints="xs md" class="control"  data-type="html">Abono</th>
                                <th data-breakpoints="xs md" class="control"  data-type="html">Modifica</th>
                                <th data-breakpoints="xs md" class="control"  data-type="html">Elimina</th>
                            </tr>
                            </thead>
                            <tbody>

                            <?php foreach ($controlador->anticipos->registros as $anticipo){?>
                                <tr>
                                    <td><?php echo $anticipo['em_anticipo_id']; ?></td>
                                    <td><?php echo $anticipo['em_anticipo_descripcion']; ?></td>
                                    <td><?php echo $anticipo['em_anticipo_monto']; ?></td>
                                    <td><?php echo $anticipo['em_anticipo_fecha_prestacion']; ?></td>
                                    <td><?php echo $anticipo['em_anticipo_saldo_pendiente']; ?></td>
                                    <td><?php echo $anticipo['em_anticipo_total_abonado']; ?></td>
                                    <td><?php echo $anticipo['link_abono']; ?></td>
                                    <td><?php echo $anticipo['link_modifica']; ?></td>
                                    <td><?php echo $anticipo['link_elimina']; ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        <div class="box-body">
                            * Total registros: <?php echo $controlador->anticipos->n_registros; ?><br />
                            * Fecha Hora: <?php echo $controlador->fecha_hoy; ?>
                        </div>
                    </div>
                </div> <!-- /. widget-table-->
            </div><!-- /.center-content -->
        </div>

    </div>

</main>







