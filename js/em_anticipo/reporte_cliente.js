let url = get_url("em_anticipo", "data_ajax", {em_anticipo_id: "1"});

var datatable = $(".datatables").DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    ajax: {
        "url": url,
        'data': function (data) {
            var fecha_inicio = $('#fecha_inicio').val();
            var fecha_final = $('#fecha_final').val();

        },
        "error": function (jqXHR, textStatus, errorThrown) {
            let response = jqXHR.responseText;
            document.body.innerHTML = response.replace('[]', '')
        },
    },
    columns: [
        {
            title: 'Id',
            data: 'em_anticipo_id'
        },
        {
            title: 'Codigo',
            data: 'em_anticipo_codigo'
        },
        {
            title: 'Descripcion',
            data: 'em_anticipo_descripcion'
        },
        {
            title: 'Monto',
            data: 'em_anticipo_monto'
        },
        {
            title: 'Fecha Prestacion',
            data: 'em_anticipo_fecha_prestacion'
        },
        {
            title: 'Fecha Inicio Descuento',
            data: 'em_anticipo_fecha_inicio_descuento'
        },
    ],
});

$('.filter-checkbox,#fecha_inicio,#fecha_final').on('change', function (e) {
    datatable.draw();
});





