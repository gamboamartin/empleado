var table = $('.test').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        "url": "http://localhost/empleado/index.php?seccion=em_empleado&accion=get_data&session_id=7120962810&ws=1",
    }

});

$('.test').on( 'page.dt', function () {
    var info = table.page.info();
    console.log(info);
} );




