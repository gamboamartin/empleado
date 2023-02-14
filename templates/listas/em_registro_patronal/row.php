<?php use config\views; ?>
<?php /** @var stdClass $row  viene de registros del controler*/ ?>
<tr>
    <?php //var_dump($row); ?>
    <td><?php echo $row->em_registro_patronal_id; ?></td>
    <td><?php echo $row->org_empresa_rfc; ?></td>
    <td><?php echo $row->em_registro_patronal_descripcion; ?></td>
    <!-- Dynamic generated -->
    <td><?php echo $row->em_clase_riesgo_factor; ?></td>
    <td><?php echo $row->dp_estado_descripcion; ?></td>


    <!-- End dynamic generated -->

    <?php include (new views())->ruta_templates.'listas/action_row.php';?>
</tr>
