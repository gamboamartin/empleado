let sl_dp_pais = $("#dp_pais_id");
let sl_dp_estado = $("#dp_estado_id");
let sl_dp_municipio = $("#dp_municipio_id");
let sl_dp_cp = $("#dp_cp_id");
let sl_dp_colonia = $("#dp_colonia_id");
let sl_dp_colonia_postal = $("#dp_colonia_postal_id");
let sl_dp_calle = $("#dp_calle_id");
let sl_dp_calle_pertenece = $("#dp_calle_pertenece_id");

let sl_org_puesto = $("#org_puesto_id");


let in_campo_extra = $("#campo_extra");

let input_seleccionado;

$(function() {
    var content = "<input type='text' class='bss-input' onKeyDown='event.stopPropagation();' onKeyPress='addSelectInpKeyPress(this,event)' onClick='event.stopPropagation()' placeholder='Add item'> <span class='glyphicon glyphicon-plus addnewicon' onClick='addSelectItem(this,event,1);'></span>";


    var addoption = $('<option/>', {class: 'addItem'})
        .data('content', content)

    let sl_predeterminado = (identificador) => {

        identificador.change(function(){
            let selected = identificador.find('option:selected');
            let id = identificador.attr('id');
            let objeto = id.substring(0, id.length - 2);
            let predeterminado = selected.data(`${objeto}predeterminado`);

            if (predeterminado === 'activo'){
                $(`#${id}`).siblings(".dropdown-menu").children(".inner").append('<li data-original-index="198"><a tabindex="0" class="addItem"  data-tokens="null" style="display: flex;justify-content: space-evenly;align-items: center;" ><input type="text" class="bss-input" style="width: 90%;" onkeydown="event.stopPropagation();" onkeypress="addSelectInpKeyPress(this,event)" onclick="event.stopPropagation()" placeholder="Add item"> <span class="glyphicon glyphicon-plus addnewicon" onclick="addSelectItem(this,event,1);"></span><span class="glyphicon glyphicon-ok check-mark"></span></a></li>');
                $(`#${id}`).prepend(addoption)
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




});













