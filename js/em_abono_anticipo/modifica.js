let sl_em_empleado = $("#em_empleado_id");
let sl_em_anticipo = $("#em_anticipo_id");

let txt_anticipo = $('#anticipo');
let txt_n_pagos = $('#n_pagos');
let txt_num_pago = $('#num_pago');
let txt_saldo = $('#saldo');
let txt_monto = $('#monto');

function new_option_sl2(descripcion, value, data = [], data_obj = {}) {

    if (Array.isArray(data) && data.length){
        let data_value = "";

        data.forEach(function (value, index, array) {
            let prop_value = data_obj[value];
            data_value += `data-${value}=${prop_value} `;
        });

        return `<option value ="${value}" ${data_value}>${descripcion}</option>`;
    }

    return `<option value ="${value}">${descripcion}</option>`;
}

function add_new_option2(container, descripcion, value, data = [], data_obj = {}) {
    let new_option = new_option_sl2(descripcion, value, data, data_obj);
    $(new_option).appendTo(container);
}

const ajax_get_data = function (seccion, accion, extra_params, identificador, extra_data = [], selects = []) {

    const url = get_url(seccion, accion, extra_params);

    get_data(url, function (data) {

        identificador.empty();

        selects.forEach(function (value, index, array) {

            if (typeof value !== 'object') {
                alert(`${value.selector} no es un objeto`);
                return;
            }

            if (value[0].tagName !== 'SELECT') {
                alert(`${value.selector} no es un objeto select`);
                return;
            }

            value.empty();
            integra_new_option(value, 'Selecciona una opción', '-1');
            value.selectpicker('refresh');
        });

        add_new_option2(identificador, 'Selecciona una opción', '-1');

        data.registros.forEach(function (value, index, array) {
            add_new_option2(identificador, value[`${seccion}_descripcion_select`], value[`${seccion}_id`], extra_data, value);
        });

        identificador.selectpicker('refresh');
    });
};

sl_em_empleado.change(function () {
    let selected = $(this).find('option:selected');
    ajax_get_data("em_anticipo", "get_anticipos", {em_empleado_id: selected.val()},
        sl_em_anticipo, ["em_anticipo_monto","em_anticipo_n_pagos", "n_pago", "em_anticipo_saldo",
            "pago_siguiente"]);
});

sl_em_anticipo.change(function () {
    let selected = $(this).find('option:selected');

    let anticipo = selected.data('em_anticipo_monto');
    let n_pagos = selected.data('em_anticipo_n_pagos');
    let num_pago = selected.data('n_pago');
    let saldo = selected.data('em_anticipo_saldo');
    let monto = selected.data('pago_siguiente');

    txt_anticipo.val(anticipo);
    txt_n_pagos.val(n_pagos);
    txt_num_pago.val(num_pago);
    txt_saldo.val(saldo);
    txt_monto.val(monto);
});