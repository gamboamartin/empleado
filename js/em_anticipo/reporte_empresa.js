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
            var org_sucursal_id = $('#org_sucursal_id').val();
            var em_tipo_anticipo_id = $('#em_tipo_anticipo_id').val();

            if(org_sucursal_id !== '' && em_tipo_anticipo_id !== ''){
                data.data = {"org_sucursal.id": org_sucursal_id,"em_tipo_anticipo.id": em_tipo_anticipo_id}
            }
            if(org_sucursal_id !== ''){
                data.data = {"org_sucursal.id": org_sucursal_id}
            }
            if(em_tipo_anticipo_id !== ''){
                data.data = {"em_tipo_anticipo.id": em_tipo_anticipo_id}
            }

            data.filtros = [
                {
                    "key": "em_anticipo.fecha_prestacion",
                    "valor": fecha_inicio,
                    "operador": "<=",
                    "comparacion": "AND"
                },
                {
                    "key": "em_anticipo.fecha_prestacion",
                    "valor": fecha_final,
                    "operador": ">=",
                    "comparacion": "AND"
                },
            ]

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

$('.filter-checkbox,#fecha_inicio,#fecha_final,#org_sucursal_id,#em_tipo_anticipo_id').on('change', function (e) {
    datatable.draw();
});





