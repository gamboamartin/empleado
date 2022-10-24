let sl_dp_pais = $("#dp_pais_id");
let sl_dp_estado = $("#dp_estado_id");
let sl_dp_municipio = $("#dp_municipio_id");
let sl_dp_cp = $("#dp_cp_id");
let sl_dp_colonia = $("#dp_colonia_id");
let sl_dp_colonia_postal = $("#dp_colonia_postal_id");
let sl_dp_calle = $("#dp_calle_id");
let sl_dp_calle_pertenece = $("#dp_calle_pertenece_id");
let in_campo_extra = $("#campo_extra");

let input_seleccionado;

let sl_predeterminado = (identificador) => {

    identificador.change(function(){
        let selected = identificador.find('option:selected');
        let objeto = identificador.attr('id');
        objeto = objeto.substring(0, objeto.length - 2);
        let predeterminado = selected.data(`${objeto}predeterminado`);
        input_seleccionado = null;

        if (predeterminado === 'activo'){
            let campo = identificador.parent().parent().siblings().text()
            in_campo_extra.parent().siblings().text(campo)
            input_seleccionado = identificador;
            in_campo_extra.val("")
            $('#campo').modal('show');
        }
    });
};

$("#modal-aceptar").click(function () {
    if (input_seleccionado != null){
        let name = input_seleccionado.attr('name');
        let value = in_campo_extra.val();
        let id = `#campo_extra_${name}`;
        console.log(id)
        console.log($(id).length)

        if($(id).length <= 0){
            let input = jQuery(`<input id="campo_extra_${name}" name="campo_extra_${name}" value="${value}">`);
            jQuery('.form-additional').append(input);
        }else{
            $(id).val(value)
        }
    }
});

sl_predeterminado(sl_dp_pais);
sl_predeterminado(sl_dp_estado);
sl_predeterminado(sl_dp_municipio);
sl_predeterminado(sl_dp_cp);
sl_predeterminado(sl_dp_colonia);
sl_predeterminado(sl_dp_colonia_postal);
sl_predeterminado(sl_dp_calle);
sl_predeterminado(sl_dp_calle_pertenece);











