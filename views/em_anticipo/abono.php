<?php /** @var \gamboamartin\empleado\models\em_anticipo $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>

<main class="main section-color-primary">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">
                    <?php include (new views())->ruta_templates."head/title.php"; ?>
                    <?php include (new views())->ruta_templates."head/subtitulo.php"; ?>
                    <?php include (new views())->ruta_templates."mensajes.php"; ?>
                    <form method="post" action="<?php echo $controlador->link_em_abono_anticipo_alta_bd; ?>&em_anticipo_id=<?php echo $controlador->em_anticipo_id; ?>" class="form-additional">
                        <?php echo $controlador->inputs->codigo; ?>
                        <?php echo $controlador->inputs->select->em_anticipo_id; ?>
                        <?php echo $controlador->inputs->descripcion; ?>
                        <?php echo $controlador->inputs->select->em_tipo_abono_anticipo_id; ?>
                        <?php echo $controlador->inputs->select->cat_sat_forma_pago_id; ?>
                        <?php echo $controlador->inputs->monto; ?>
                        <?php echo $controlador->inputs->fecha; ?>
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
                            <h2>Abonos</h2>
                        </div>

                        <table class="table table-striped footable-sort" data-sorting="true">
                            <thead>
                            <tr>
                                <th data-breakpoints="xs sm md" data-type="html">Id</th>
                                <th data-breakpoints="xs sm md" data-type="html">Codigo</th>
                                <th data-breakpoints="xs sm md" data-type="html">Descripcion</th>
                                <th data-breakpoints="xs sm md" data-type="html">Monto</th>
                                <th data-breakpoints="xs sm md" data-type="html">Fecha</th>
                                <th data-breakpoints="xs sm md" data-type="html">Tipo Abono</th>
                                <th data-breakpoints="xs sm md" data-type="html">Forma Pago</th>

                                <th data-breakpoints="xs md" class="control"  data-type="html">Modifica</th>
                                <th data-breakpoints="xs md" class="control"  data-type="html">Elimina</th>
                            </tr>
                            </thead>
                            <tbody>

                            <?php foreach ($controlador->abonos->registros as $abono){?>
                                <tr>
                                    <td><?php echo $abono['em_abono_anticipo_id']; ?></td>
                                    <td><?php echo $abono['em_abono_anticipo_codigo']; ?></td>
                                    <td><?php echo $abono['em_abono_anticipo_descripcion']; ?></td>
                                    <td><?php echo $abono['em_abono_anticipo_monto']; ?></td>
                                    <td><?php echo $abono['em_abono_anticipo_fecha']; ?></td>
                                    <td><?php echo $abono['em_tipo_abono_anticipo_descripcion']; ?></td>
                                    <td><?php echo $abono['cat_sat_forma_pago_descripcion']; ?></td>
                                    <td><?php echo $abono['link_modifica']; ?></td>
                                    <td><?php echo $abono['link_elimina']; ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        <div class="box-body">
                            * Total registros: <?php echo $controlador->abonos->n_registros; ?><br />
                            * Fecha Hora: <?php echo $controlador->fecha_hoy; ?>
                        </div>
                    </div>
                </div> <!-- /. widget-table-->
            </div><!-- /.center-content -->
        </div>

    </div>

</main>







