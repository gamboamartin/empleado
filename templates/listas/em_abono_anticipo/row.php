<?php use config\views; ?>
<?php /** @var stdClass $row  viene de registros del controler*/ ?>
<tr>
    <td><?php echo $row->em_abono_anticipo_id; ?></td>
    <td><?php echo $row->em_abono_anticipo_descripcion; ?></td>
    <td><?php echo $row->em_empleado_codigo; ?></td>
    <td><?php echo $row->em_empleado_nombre. ' '. $row->em_empleado_ap. ' '.$row->em_empleado_am; ?></td>
    <td><?php echo $row->em_abono_anticipo_monto; ?></td>
    <td><?php echo $row->cat_sat_forma_pago_descripcion; ?></td>
    <td><?php echo $row->em_abono_anticipo_fecha; ?></td>
    <!-- End dynamic generated -->

    <?php include (new views())->ruta_templates.'listas/action_row.php';?>
</tr>
