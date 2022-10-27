let sl_dp_pais = $("#dp_pais_id");
let sl_dp_estado = $("#dp_estado_id");
let sl_dp_municipio = $("#dp_municipio_id");
let sl_dp_cp = $("#dp_cp_id");
let sl_dp_colonia = $("#dp_colonia_id");
let sl_dp_colonia_postal = $("#dp_colonia_postal_id");
let sl_dp_calle = $("#dp_calle_id");
let sl_dp_calle_pertenece = $("#dp_calle_pertenece_id");

let animaciones = (inputs,efecto = 0,margin = 0) => {
    inputs.forEach( function(valor, indice, array) {
        $(`#direccion_pendiente_${valor}`).parent().parent().css('margin-bottom', margin);
        if (margin == 0){
            $(`#direccion_pendiente_${valor}`).parent().hide(efecto);
            $(`#direccion_pendiente_${valor}`).parent().siblings().hide(efecto);
        } else {
            $(`#direccion_pendiente_${valor}`).parent().show(efecto);
            $(`#direccion_pendiente_${valor}`).parent().siblings().show(efecto);
        }
    });
};

animaciones(["pais","estado","municipio","cp","colonia","calle_pertenece"]);

sl_dp_pais.change(function () {
    let selected = $(this).find('option:selected');
    let predeterminado = selected.data(`dp_pais_predeterminado`);

    animaciones(["pais","estado","municipio","cp","colonia","calle_pertenece"],"slow");

    if (predeterminado === 'activo'){
        animaciones(["pais","estado","municipio","cp","colonia","calle_pertenece"],"slow", 20);
    }
});

sl_dp_estado.change(function () {
    let selected = $(this).find('option:selected');
    let predeterminado = selected.data(`dp_estado_predeterminado`);

    animaciones(["estado","municipio","cp","colonia","calle_pertenece"],"slow");

    if (predeterminado === 'activo'){
        animaciones(["estado","municipio","cp","colonia","calle_pertenece"],"slow", 20);
    }
});

sl_dp_municipio.change(function () {
    let selected = $(this).find('option:selected');
    let predeterminado = selected.data(`dp_municipio_predeterminado`);

    animaciones(["municipio","cp","colonia","calle_pertenece"],"slow");

    if (predeterminado === 'activo'){
        animaciones(["municipio","cp","colonia","calle_pertenece"],"slow", 20);
    }
});

sl_dp_cp.change(function () {
    let selected = $(this).find('option:selected');
    let predeterminado = selected.data(`dp_cp_predeterminado`);

    animaciones(["cp","colonia","calle_pertenece"],"slow");

    if (predeterminado === 'activo'){
        animaciones(["cp","colonia","calle_pertenece"],"slow", 20);
    }
});

sl_dp_colonia.change(function () {
    let selected = $(this).find('option:selected');
    let predeterminado = selected.data(`dp_colonia_predeterminado`);

    animaciones(["colonia","calle_pertenece"],"slow");

    if (predeterminado === 'activo'){
        animaciones(["colonia","calle_pertenece"],"slow", 20);
    }
});

sl_dp_calle_pertenece.change(function () {
    let selected = $(this).find('option:selected');
    let predeterminado = selected.data(`dp_calle_pertenece_predeterminado`);

    animaciones(["calle_pertenece"],"slow");

    if (predeterminado === 'activo'){
        animaciones(["calle_pertenece"],"slow", 20);
    }
});














