let sl_em_anticipo = $("#em_anticipo_id");

let txt_num_pago = $('#num_pago');
let txt_monto = $('#monto');

console.log("aaaaaaaaaawwwwww")

sl_em_anticipo.change(function(){
    let selected = $(this).find('option:selected');

    let num_pago = selected.data('n_pago');
    let monto = selected.data('pago_siguiente');

    txt_num_pago.val(num_pago);
    txt_monto.val(monto);
});
