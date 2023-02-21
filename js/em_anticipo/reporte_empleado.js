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
            var em_empleado_id = $('#em_empleado_id').val();
            var em_tipo_anticipo_id = $('#em_tipo_anticipo_id').val();



            data.filtros = {
                /*

                extra_join: [
                    {
                        "entidad": "em_empleado",
                        "key": "id",
                        "enlace": "em_rel_empleado_sucursal",
                        "key_enlace": "em_empleado_id",
                        "renombre": "com_sucursal_empleado"
                    },
                ],
                filtro: [
                    {
                        "key": "em_rel_empleado_sucursal.com_sucursal_id",
                        "valor": 1,
                    }
                ],*/
                filtro_especial: [
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
                    }
                ]
            }
            /*
                        data.data = {"em_empleado.id": em_empleado_id,"em_tipo_anticipo.id": em_tipo_anticipo_id}
                          if(em_empleado_id !== ''){
                              data.data = {"em_empleado.id": em_empleado_id}
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
                          ]*/

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

$('.filter-checkbox,#fecha_inicio,#fecha_final,#com_sucursal_id,#em_tipo_anticipo_id').on('change', function (e) {
    datatable.draw();
});





