let sl_dp_pais = $("#dp_pais_id");
let sl_dp_estado = $("#dp_estado_id");
let sl_dp_municipio = $("#dp_municipio_id");
let sl_dp_cp = $("#dp_cp_id");
let sl_dp_colonia = $("#dp_colonia_id");
let sl_dp_colonia_postal = $("#dp_colonia_postal_id");
let sl_dp_calle = $("#dp_calle_id");
let sl_dp_calle_pertenece = $("#dp_calle_pertenece_id");
let in_campo_extra = $("#campo_extra");

let sl_predeterminado = (identificador) => {

    identificador.change(function(){
        let selected = identificador.find('option:selected');
        let objeto = identificador.attr('id');
        objeto = objeto.substring(0, objeto.length - 2);
        let predeterminado = selected.data(`${objeto}predeterminado`);

        if (predeterminado === 'activo'){
            let campo = identificador.parent().parent().siblings().text()
            in_campo_extra.parent().siblings().text(campo)
            $('#campo').modal('show');
        }
    });
};

sl_predeterminado(sl_dp_pais);
sl_predeterminado(sl_dp_estado);
sl_predeterminado(sl_dp_municipio);
sl_predeterminado(sl_dp_cp);
sl_predeterminado(sl_dp_colonia);
sl_predeterminado(sl_dp_colonia_postal);
sl_predeterminado(sl_dp_calle);
sl_predeterminado(sl_dp_calle_pertenece);











