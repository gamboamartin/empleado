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

function appendInput(value){
    var content = `<input type='text' class='bss-input' onKeyDown='event.stopPropagation();' onKeyPress='addSelectInpKeyPress(this,event)' onClick='event.stopPropagation()' placeholder='Add item'> <span class='bx bx-plus addnewicon' onClick='addSelectItem(this,event,${value});'></span>`;
    var inputEle = $('.addItem.dropdown-item span.text');
    $('.addItem.dropdown-item span.text').each(function(index, el) {
        if ($(this).text() == '') {
            $(this).html(content);
        }
    });
}

function addSelectItem(t,ev, value)
{
    ev.stopPropagation();

    var bs = $(t).closest('.bootstrap-select')
    var txt=bs.find('.bss-input').val().replace(/[|]/g,"");
    var txt=$(t).prev().val().replace(/[|]/g,"");
    if ($.trim(txt)=='') return;

    var p=bs.find('select');
    var o=$('option', p).eq(-2);
    o.before( $("<option>", { "selected": false, "text": txt, "value": value}) );
    p.selectpicker('refresh');
    appendInput(1212);
}

function addSelectInpKeyPress(t,ev)
{
    ev.stopPropagation();
    if (ev.which==124) ev.preventDefault();
    if (ev.which==13)
    {
        ev.preventDefault();
        addSelectItem($(t).next(),ev, 1233);
    }
}

sl_dp_pais.change(function () {
    let selected = $(this).find('option:selected');
    let value = selected.val()
    let text = selected.text()
    let predeterminado = selected.data(`dp_pais_predeterminado`);
    let inputObject = `<input type="hidden" id="campo_extra_dp_pais" name="campo_extra_dp_pais" value="${text}">`
    let append = `<li ><a tabindex="0" class="addItem"  data-tokens="null" style="display: flex;justify-content: space-evenly;align-items: center;" ><input type="text" class="bss-input" style="width: 90%;" onkeydown="event.stopPropagation();" onkeypress="addSelectInpKeyPress(this,event)" onclick="event.stopPropagation()" placeholder="Add item"> <span class="glyphicon glyphicon-plus addnewicon" onclick="addSelectItem(this,event,${value});"></span><span class="glyphicon glyphicon-ok check-mark"></span></a></li>`;

    if (predeterminado === 'activo'){
        sl_dp_pais.siblings(".dropdown-menu").children(".inner").append(append);
    } else if(typeof predeterminado === "undefined"){
        if($("#campo_extra_dp_pais").length <= 0){
            let input = jQuery(inputObject);
            jQuery('.form-additional').append(input);
        }else{
            $("#campo_extra_dp_pais").val(text)
        }
    }
})

sl_dp_estado.change(function () {
    let selected = $(this).find('option:selected');
    let value = selected.val()
    let text = selected.text()
    let predeterminado = selected.data(`dp_estado_predeterminado`);
    let inputObject = `<input type="hidden" id="campo_extra_dp_estado" name="campo_extra_dp_estado" value="${text}">`
    let append = `<li ><a tabindex="0" class="addItem"  data-tokens="null" style="display: flex;justify-content: space-evenly;align-items: center;" ><input type="text" class="bss-input" style="width: 90%;" onkeydown="event.stopPropagation();" onkeypress="addSelectInpKeyPress(this,event)" onclick="event.stopPropagation()" placeholder="Add item"> <span class="glyphicon glyphicon-plus addnewicon" onclick="addSelectItem(this,event,${value});"></span><span class="glyphicon glyphicon-ok check-mark"></span></a></li>`;

    if (predeterminado === 'activo'){
        sl_dp_estado.siblings(".dropdown-menu").children(".inner").append(append);
    } else if(typeof predeterminado === "undefined"){
        if($("#campo_extra_dp_estado").length <= 0){
            let input = jQuery(inputObject);
            jQuery('.form-additional').append(input);
        }else{
            $("#campo_extra_dp_estado").val(text)
        }
    }
})

sl_dp_municipio.change(function () {
    let selected = $(this).find('option:selected');
    let value = selected.val()
    let text = selected.text()
    let predeterminado = selected.data(`dp_municipio_predeterminado`);
    let inputObject = `<input type="hidden" id="campo_extra_dp_municipio" name="campo_extra_dp_municipio" value="${text}">`
    let append = `<li ><a tabindex="0" class="addItem"  data-tokens="null" style="display: flex;justify-content: space-evenly;align-items: center;" ><input type="text" class="bss-input" style="width: 90%;" onkeydown="event.stopPropagation();" onkeypress="addSelectInpKeyPress(this,event)" onclick="event.stopPropagation()" placeholder="Add item"> <span class="glyphicon glyphicon-plus addnewicon" onclick="addSelectItem(this,event,${value});"></span><span class="glyphicon glyphicon-ok check-mark"></span></a></li>`;

    if (predeterminado === 'activo'){
        sl_dp_municipio.siblings(".dropdown-menu").children(".inner").append(append);
    } else if(typeof predeterminado === "undefined"){
        if($("#campo_extra_dp_municipio").length <= 0){
            let input = jQuery(inputObject);
            jQuery('.form-additional').append(input);
        }else{
            $("#campo_extra_dp_municipio").val(text)
        }
    }
})

sl_dp_cp.change(function () {
    let selected = $(this).find('option:selected');
    let value = selected.val()
    let text = selected.text()
    let predeterminado = selected.data(`dp_cp_predeterminado`);
    let inputObject = `<input type="hidden" id="campo_extra_dp_cp" name="campo_extra_dp_cp" value="${text}">`
    let append = `<li ><a tabindex="0" class="addItem"  data-tokens="null" style="display: flex;justify-content: space-evenly;align-items: center;" ><input type="text" class="bss-input" style="width: 90%;" onkeydown="event.stopPropagation();" onkeypress="addSelectInpKeyPress(this,event)" onclick="event.stopPropagation()" placeholder="Add item"> <span class="glyphicon glyphicon-plus addnewicon" onclick="addSelectItem(this,event,${value});"></span><span class="glyphicon glyphicon-ok check-mark"></span></a></li>`;

    if (predeterminado === 'activo'){
        sl_dp_cp.siblings(".dropdown-menu").children(".inner").append(append);
    } else if(typeof predeterminado === "undefined"){
        if($("#campo_extra_dp_cp").length <= 0){
            let input = jQuery(inputObject);
            jQuery('.form-additional').append(input);
        }else{
            $("#campo_extra_dp_cp").val(text)
        }
    }
})

sl_dp_colonia.change(function () {
    let selected = $(this).find('option:selected');
    let value = selected.val()
    let text = selected.text()
    let predeterminado = selected.data(`dp_colonia_predeterminado`);
    let inputObject = `<input type="hidden" id="campo_extra_dp_colonia" name="campo_extra_dp_colonia" value="${text}">`
    let append = `<li ><a tabindex="0" class="addItem"  data-tokens="null" style="display: flex;justify-content: space-evenly;align-items: center;" ><input type="text" class="bss-input" style="width: 90%;" onkeydown="event.stopPropagation();" onkeypress="addSelectInpKeyPress(this,event)" onclick="event.stopPropagation()" placeholder="Add item"> <span class="glyphicon glyphicon-plus addnewicon" onclick="addSelectItem(this,event,${value});"></span><span class="glyphicon glyphicon-ok check-mark"></span></a></li>`;

    if (predeterminado === 'activo'){
        sl_dp_colonia.siblings(".dropdown-menu").children(".inner").append(append);
    } else if(typeof predeterminado === "undefined"){
        if($("#campo_extra_dp_colonia").length <= 0){
            let input = jQuery(inputObject);
            jQuery('.form-additional').append(input);
        }else{
            $("#campo_extra_dp_colonia").val(text)
        }
    }
})

sl_dp_colonia_postal.change(function () {
    let selected = $(this).find('option:selected');
    let value = selected.val()
    let text = selected.text()
    let predeterminado = selected.data(`dp_colonia_postal_predeterminado`);
    let inputObject = `<input type="hidden" id="campo_extra_dp_estado" name="campo_extra_dp_colonia_postal" value="${text}">`
    let append = `<li ><a tabindex="0" class="addItem"  data-tokens="null" style="display: flex;justify-content: space-evenly;align-items: center;" ><input type="text" class="bss-input" style="width: 90%;" onkeydown="event.stopPropagation();" onkeypress="addSelectInpKeyPress(this,event)" onclick="event.stopPropagation()" placeholder="Add item"> <span class="glyphicon glyphicon-plus addnewicon" onclick="addSelectItem(this,event,${value});"></span><span class="glyphicon glyphicon-ok check-mark"></span></a></li>`;

    if (predeterminado === 'activo'){
        sl_dp_colonia_postal.siblings(".dropdown-menu").children(".inner").append(append);
    } else if(typeof predeterminado === "undefined"){
        if($("#campo_extra_dp_colonia_postal").length <= 0){
            let input = jQuery(inputObject);
            jQuery('.form-additional').append(input);
        }else{
            $("#campo_extra_dp_colonia_postal").val(text)
        }
    }
})

sl_dp_calle.change(function () {
    let selected = $(this).find('option:selected');
    let value = selected.val()
    let text = selected.text()
    let predeterminado = selected.data(`dp_calle_predeterminado`);
    let inputObject = `<input type="hidden" id="campo_extra_dp_calle" name="campo_extra_dp_calle" value="${text}">`
    let append = `<li ><a tabindex="0" class="addItem"  data-tokens="null" style="display: flex;justify-content: space-evenly;align-items: center;" ><input type="text" class="bss-input" style="width: 90%;" onkeydown="event.stopPropagation();" onkeypress="addSelectInpKeyPress(this,event)" onclick="event.stopPropagation()" placeholder="Add item"> <span class="glyphicon glyphicon-plus addnewicon" onclick="addSelectItem(this,event,${value});"></span><span class="glyphicon glyphicon-ok check-mark"></span></a></li>`;

    if (predeterminado === 'activo'){
        sl_dp_calle.siblings(".dropdown-menu").children(".inner").append(append);
    } else if(typeof predeterminado === "undefined"){
        if($("#campo_extra_dp_calle").length <= 0){
            let input = jQuery(inputObject);
            jQuery('.form-additional').append(input);
        }else{
            $("#campo_extra_dp_calle").val(text)
        }
    }
})

sl_dp_calle_pertenece.change(function () {
    let selected = $(this).find('option:selected');
    let value = selected.val()
    let text = selected.text()
    let predeterminado = selected.data(`dp_calle_pertenece_predeterminado`);
    let inputObject = `<input type="hidden" id="campo_extra_dp_calle_pertenece" name="campo_extra_dp_calle_pertenece" value="${text}">`
    let append = `<li ><a tabindex="0" class="addItem"  data-tokens="null" style="display: flex;justify-content: space-evenly;align-items: center;" ><input type="text" class="bss-input" style="width: 90%;" onkeydown="event.stopPropagation();" onkeypress="addSelectInpKeyPress(this,event)" onclick="event.stopPropagation()" placeholder="Add item"> <span class="glyphicon glyphicon-plus addnewicon" onclick="addSelectItem(this,event,${value});"></span><span class="glyphicon glyphicon-ok check-mark"></span></a></li>`;

    if (predeterminado === 'activo'){
        sl_dp_calle_pertenece.siblings(".dropdown-menu").children(".inner").append(append);
    } else if(typeof predeterminado === "undefined"){
        if($("#campo_extra_dp_calle_pertenece").length <= 0){
            let input = jQuery(inputObject);
            jQuery('.form-additional').append(input);
        }else{
            $("#campo_extra_dp_calle_pertenece").val(text)
        }
    }
})













