<?php use config\views; ?>
<?php /** @var stdClass $row  viene de registros del controler*/ ?>
<tr>
    <td><?php echo $row->em_anticipo_id; ?></td>
    <td><?php echo $row->em_anticipo_descripcion; ?></td>
    <td><?php echo $row->em_empleado_codigo; ?></td>
    <td><?php echo $row->em_empleado_nombre. ' '. $row->em_empleado_ap. ' '.$row->em_empleado_am; ?></td>
    <td><?php echo $row->em_anticipo_monto; ?></td>
    <td><?php echo $row->em_anticipo_fecha_prestacion; ?></td>

    <td><?php include 'templates/botons/em_anticipo/link_ver_abonos.php';?></td>
    <!-- End dynamic generated -->

    <?php include (new views())->ruta_templates.'listas/action_row.php';?>
</tr>
