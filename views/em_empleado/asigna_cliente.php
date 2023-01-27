<?php /** @var \gamboamartin\empleado\controllers\controlador_em_empleado $controlador  controlador en ejecucion */ ?>
<?php use config\views; ?>

<main class="main section-color-primary">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="widget  widget-box box-container form-main widget-form-cart" id="form">
                    <?php include (new views())->ruta_templates."head/title.php"; ?>
                    <?php include (new views())->ruta_templates."head/subtitulo.php"; ?>
                    <?php include (new views())->ruta_templates."mensajes.php"; ?>
                    <form method="post" action="<?php echo $controlador->link_nom_conf_empleado_alta_bd; ?>" class="form-additional">
                        <?php echo $controlador->inputs->em_empleado_id; ?>
                        <?php echo $controlador->inputs->com_sucursal_id; ?>
                        <?php echo $controlador->inputs->codigo; ?>
                        <?php echo $controlador->inputs->descripcion; ?>
                        <div class="control-group btn-alta">
                            <div class="controls">
                                <button type="submit" class="btn btn-success" value="asigna_percepcion" name="btn_action_next">Alta</button><br>
                            </div>
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-md-12">

            <div class="widget widget-box box-container widget-mylistings">

                <div class="">
                    <div class="widget-header">
                        <h2>Configuraciones de Nomina Asignadas</h2>
                    </div>

                    <table class="table table-striped footable-sort" data-sorting="true">
                        <thead>
                        <tr>
                            <th data-breakpoints="xs sm md" data-type="html">Id</th>
                            <th data-breakpoints="xs sm md" data-type="html">Codigo</th>
                            <th data-breakpoints="xs sm md" data-type="html">Descripcion</th>

                            <th data-breakpoints="xs md" class="control"  data-type="html">Modifica</th>
                            <th data-breakpoints="xs md" class="control"  data-type="html">Elimina</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php foreach ($controlador->anticipos->registros as $anticipo){?>
                            <tr>
                                <td><?php echo $anticipo['nom_conf_empleado_id']; ?></td>
                                <td><?php echo $anticipo['nom_conf_empleado_codigo']; ?></td>
                                <td><?php echo $anticipo['nom_conf_empleado_descripcion']; ?></td>

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

</main>







