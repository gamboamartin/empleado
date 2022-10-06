<?php use config\views; ?>
<?php /** @var stdClass $row  viene de registros del controler*/ ?>
<tr>
    <td><?php echo $row->em_cuenta_bancaria_id; ?></td>
    <!-- Dynamic generated -->
    <td><?php echo $row->em_empleado_codigo; ?></td>
    <td><?php echo $row->em_empleado_nombre. ' '. $row->em_empleado_ap. ' '.$row->em_empleado_am; ?></td>
    <td><?php echo $row->bn_banco_descripcion; ?></td>
    <td><?php echo $row->em_cuenta_bancaria_num_cuenta; ?></td>
    <td><?php echo $row->em_cuenta_bancaria_clabe; ?></td>
    <!-- End dynamic generated -->

    <?php include (new views())->ruta_templates.'listas/action_row.php';?>
</tr>
